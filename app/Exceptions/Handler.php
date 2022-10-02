<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    public function render($request, Throwable $e)
    {
        return response()->json(
            [
                'error' => $e->getMessage(),
                'code' => $this->getCode($e),
                'status_code' => $this->getExceptionHTTPStatusCode($e),
                'line' => app()->environment('prod') || app()->environment('testing') ? null : $e->getTrace(),
            ],
            $this->getExceptionHTTPStatusCode($e),
        );
    }

    public function getCode(Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return \Devesharp\Exceptions\Exception::TOKEN_INVALID;
        }

        return $e->getCode();
    }

    protected function getExceptionHTTPStatusCode($e)
    {
        if ($e instanceof AuthenticationException) {
            return 401;
        }

        if ($e instanceof \Devesharp\Exceptions\Exception) {
            if ($e->getCode() === \Devesharp\Exceptions\Exception::TOKEN_INVALID) {
                return 401;
            }

            if ($e->getCode() === \Devesharp\Exceptions\Exception::NOT_FOUND_RESOURCE) {
                return 404;
            }
        }

        return method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
    }

    public function report(Throwable $exception)
    {
        /**
         * Enviar erro para sentry
         */
        if (! empty(env('SENTRY_LARAVEL_DSN')) && app()->environment(['prod'])) {
            if ($this->shouldReport($exception) && app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }
        }

        parent::report($exception);
    }
}
