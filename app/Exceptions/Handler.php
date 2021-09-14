<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the internal exception types that should not be reported.
     *
     * @var array
     */
    protected $internalDontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        HttpResponseException::class,
        SuspiciousOperationException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * レスポンスをそのまま返却できるクラス
     *
     * @var array
     */
    protected $canRespondExceptions = [
        // internalDontReport
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        SuspiciousOperationException::class,
        TokenMismatchException::class,
        ValidationException::class,

        // 追加
        InvalidInputException::class,
        ExternalSystemException::class,
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
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(\Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $exception)
    {
        // 本番環境で内部的なエラーはそのまま返さない
        if ($this->shouldNotRespondOriginalMessage($exception)) {
            return parent::render(
                $request,
                new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, error_format('error.unexpected'))
            );
        }

        // ライブラリから自動でスローされることがあるのでここでまとめる。
        if ($exception instanceof ModelNotFoundException) {
            return parent::render(
                $request,
                new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'), $exception)
            );
        }

        if ($exception instanceof InvalidInputException) {
            return $exception->getResponse();
        }

        return parent::render($request, $exception);
    }

    /**
     * メッセージをそのまま返せるか判定する
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    private function shouldNotRespondOriginalMessage(\Exception $exception)
    {
        $canRespondExceptions = $this->canRespondExceptions;

        foreach ($canRespondExceptions as $canRespond) {
            if ($exception instanceof $canRespond) {
                return false;
            }
        }

        return true;
    }
}
