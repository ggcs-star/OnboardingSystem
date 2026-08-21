<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\ViteManifestNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // A CSRF/session token mismatch almost always means the session
        // expired while the tab was left open (or the app key rotated on
        // deploy). Instead of the raw "419 Page Expired" screen, quietly
        // sign the user out and send them back to login.
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            return $this->expireSession($request);
        });

        // Never leak SQL, table/column names, or connection details for a
        // failed database query. Log the real error for developers and show
        // a generic message to the user. Left alone in local/debug mode so
        // developers still see the full query during development.
        $this->renderable(function (QueryException $e, Request $request) {
            if (config('app.debug')) {
                return null;
            }

            Log::error('Database query error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'We are unable to process your request right now. Please try again shortly.',
                ], 500);
            }

            return response()->view('errors.500', [], 500);
        });

        // Thrown when a deploy shipped without running `npm run build`, so
        // public/build/manifest.json is missing. This is a deploy mistake,
        // not something a visitor should ever see as a raw exception.
        $this->renderable(function (ViteManifestNotFoundException $e, Request $request) {
            Log::critical('Front-end build assets are missing (Vite manifest not found). Run "npm run build" and redeploy.', [
                'exception' => $e,
            ]);

            if (config('app.debug')) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The application is being updated. Please try again in a moment.',
                ], 503);
            }

            return response()->view('errors.503', [], 503);
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * Reached whenever a request hits an "auth" protected route without a
     * valid, logged-in session — most commonly because the session expired
     * after the browser tab was left open for a long time. No error is
     * shown; the user is simply logged out and redirected to login.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your session has expired. Please log in again.'], 401);
        }

        return $this->expireSession($request);
    }

    /**
     * Log the current user out, invalidate the session, and send them to
     * the login page with a friendly (non-error) status message.
     */
    protected function expireSession(Request $request)
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('status', 'Your session has expired. Please log in again.');
    }
}
