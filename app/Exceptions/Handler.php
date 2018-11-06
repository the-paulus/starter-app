<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception) {

        // Ignore Gitlab Report in code coverage
        // @codeCoverageIgnoreStart
        if(env('APP_ENV') == 'production' && $this->shouldReport($exception)) {

            app('gitlab.report')->report($exception);

        } else if(env('APP_ENV') == 'production' && $this->shouldntReport($exception)) {

            // Log these exceptions elsewhere? Failed login attempts?
            if(method_exists($this, 'report' . get_class($exception))) {

                //$this->

            }

        }
        // @codeCoverageIgnoreEnd

        parent::report($exception);

    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
