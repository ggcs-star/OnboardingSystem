/**
 * Drives lesson video playback (native <video> or YouTube IFrame API) and
 * pauses at quiz checkpoints. Talks to the Alpine curriculum-player
 * component (defined inline in client/courses/show.blade.php) only via
 * window CustomEvents, never direct calls — that component is created by a
 * plain, non-module <script> so it's guaranteed ready before Alpine starts,
 * while this module (loaded via @vite) always runs after that point.
 *
 * Re-invokable: the single-page player calls this once per item the user
 * selects, not once per page load, so every mount is preceded by a full
 * teardown of whatever was mounted before (aborting listeners, clearing the
 * poll timer, destroying any YouTube player instance).
 */
(function () {
    let adapter = null;
    let abortController = null;
    let pollTimer = null;
    let youtubePlayer = null;
    let triggered = new Set();
    let pendingCheckpoints = [];
    let lastSavedAt = 0;

    function teardown() {
        abortController?.abort();
        abortController = null;

        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }

        if (youtubePlayer) {
            try {
                youtubePlayer.destroy();
            } catch (e) {
                // container may already be gone if teardown ran late — safe to ignore
            }
            youtubePlayer = null;
        }

        adapter = null;
        triggered = new Set();
        pendingCheckpoints = [];
        lastSavedAt = 0;
    }

    function isResolved(checkpoint) {
        return checkpoint.questions.every((question) => question.answered);
    }

    function postProgress(progressUrl, position, completed) {
        window.axios.post(progressUrl, { position: Math.floor(position), completed: !!completed });
    }

    function handleTick(item, currentTime) {
        const next = pendingCheckpoints.find(
            (checkpoint) => !triggered.has(checkpoint.id) && checkpoint.timestampSeconds <= currentTime
        );

        if (next) {
            triggered.add(next.id);
            adapter.pause();
            window.dispatchEvent(new CustomEvent('course:checkpoint', { detail: next }));
        }

        if (currentTime - lastSavedAt > 5) {
            lastSavedAt = currentTime;
            postProgress(item.progressUrl, currentTime, false);
        }
    }

    function handleEnded(item) {
        postProgress(item.progressUrl, adapter ? adapter.getCurrentTime() : 0, true);
        window.dispatchEvent(new CustomEvent('course:lesson-completed'));
    }

    function mountLesson(item) {
        pendingCheckpoints = (item.checkpoints || [])
            .filter((checkpoint) => !isResolved(checkpoint))
            .sort((a, b) => a.timestampSeconds - b.timestampSeconds);

        abortController = new AbortController();
        const signal = abortController.signal;

        if (item.videoSource === 'upload') {
            initNativeVideo(item, signal);
        } else if (item.videoSource === 'youtube' && item.youtubeVideoId) {
            initYouTubeVideo(item, signal);
        }
    }

    function initNativeVideo(item, signal) {
        const video = document.getElementById('course-native-video');
        if (!video) {
            return;
        }

        adapter = {
            play: () => video.play(),
            pause: () => video.pause(),
            getCurrentTime: () => video.currentTime,
        };

        video.addEventListener('loadedmetadata', () => {
            if (item.startPosition) {
                video.currentTime = item.startPosition;
            }
        }, { signal });

        video.addEventListener('timeupdate', () => handleTick(item, video.currentTime), { signal });
        video.addEventListener('ended', () => handleEnded(item), { signal });
    }

    function loadYouTubeApi() {
        return new Promise((resolve) => {
            if (window.YT && window.YT.Player) {
                resolve();
                return;
            }

            const existingCallback = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = () => {
                if (typeof existingCallback === 'function') {
                    existingCallback();
                }
                resolve();
            };

            if (!document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            }
        });
    }

    function initYouTubeVideo(item, signal) {
        loadYouTubeApi().then(() => {
            if (signal.aborted) {
                return; // user switched away before the (one-time) API script finished loading
            }

            let localPollTimer = null;

            youtubePlayer = new YT.Player('course-youtube-player', {
                videoId: item.youtubeVideoId,
                playerVars: { start: item.startPosition || 0 },
                events: {
                    onReady(event) {
                        const player = event.target;
                        adapter = {
                            play: () => player.playVideo(),
                            pause: () => player.pauseVideo(),
                            getCurrentTime: () => player.getCurrentTime(),
                        };
                    },
                    onStateChange(event) {
                        if (localPollTimer) {
                            clearInterval(localPollTimer);
                            localPollTimer = null;
                            pollTimer = null;
                        }

                        if (event.data === YT.PlayerState.PLAYING) {
                            localPollTimer = setInterval(() => handleTick(item, event.target.getCurrentTime()), 250);
                            pollTimer = localPollTimer;
                        }

                        if (event.data === YT.PlayerState.ENDED) {
                            handleEnded(item);
                        }
                    },
                },
            });
        });
    }

    // Fired synchronously before Alpine swaps the main pane's DOM to the
    // next item, while the outgoing item's video container still exists.
    window.addEventListener('course:before-switch', teardown);

    // Fired after the DOM has settled on the new item (via $nextTick).
    window.addEventListener('course:load-item', (event) => {
        const item = event.detail?.item;
        if (item && item.type === 'lesson') {
            mountLesson(item);
        }
    });

    window.addEventListener('course:continue', () => {
        adapter?.play();
    });
})();
