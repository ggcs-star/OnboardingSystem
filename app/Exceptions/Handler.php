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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Laravel wraps exceptions thrown while a Blade view is rendering
     * (e.g. the Vite manifest missing) inside Illuminate\View\ViewException,
     * and rewrites TokenMismatchException into a plain HttpException(419)
     * before any renderable() callback ever sees it. Both mean a shallow
     * instanceof check on the outer exception misses them entirely, so
     * these cases are intercepted here first by walking the full
     * getPrevious() chain.
     */
    public function render($request, Throwable $e)
    {
        if ($this->findInChain($e, TokenMismatchException::class)) {
            return $this->expireSession($request);
        }

        if ($vite = $this->findInChain($e, ViteManifestNotFoundException::class)) {
            return $this->renderViteManifestMissing($vite, $request);
        }

        if (! config('app.debug') && ($query = $this->findInChain($e, QueryException::class))) {
            return $this->renderQueryException($query, $request);
        }

        return parent::render($request, $e);
    }

    /**
     * Walk an exception's cause chain (itself, then getPrevious() repeatedly)
     * looking for an instance of the given class.
     */
    protected function findInChain(?Throwable $e, string $class): ?Throwable
    {
        while ($e !== null) {
            if ($e instanceof $class) {
                return $e;
            }

            $e = $e->getPrevious();
        }

        return null;
    }

    /**
     * A deploy shipped without running `npm run build`, so
     * public/build/manifest.json is missing. This is a deploy mistake, not
     * something a visitor should ever see as a raw exception.
     */
    protected function renderViteManifestMissing(Throwable $e, $request)
    {
        Log::critical('Front-end build assets are missing (Vite manifest not found). Run "npm run build" and redeploy.', [
            'exception' => $e,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The application is being updated. Please try again in a moment.',
            ], 503);
        }

        return response()->view('errors.503', [], 503);
    }

    /**
     * Never leak SQL, table/column names, or connection details for a
     * failed database query. Log the real error for developers and show a
     * generic message to the user.
     */
    protected function renderQueryException(Throwable $e, $request)
    {
        Log::error('Database query error: ' . $e->getMessage(), [
            'exception' => $e,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'We are unable to process your request right now. Please try again shortly.',
            ], 500);
        }

        return response()->view('errors.500', [], 500);
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
