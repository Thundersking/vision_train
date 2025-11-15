<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
     * @param $request
     * @param Throwable $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        dd(11);
        if ($e instanceof RouteNotFoundException) {
            return responseFailed('Не авторизован', 401);
        }

        if ($e instanceof AuthenticationException) {
            return responseFailed('Не авторизован', 401);
        }

        if ($e instanceof ModelNotFoundException) {
            return responseFailed(getMessage('model_not_found'), 404);
        }

        if ($e instanceof AuthorizationException) {
            return responseFailed('У вас нет прав для выполнения этого действия.', 403);
        }

        if ($e instanceof ThrottleRequestsException) {
            return responseFailed("Вы превысили лимит запросов. Попробуйте снова через 1 минуту.",
                429);
        }

        $this->renderable(function (NotFoundHttpException $e) {
            return responseFailed(getMessage('route_not_found'));
        });

        if ($this->shouldReport($e)) {
            // Логируем ошибки 500
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 500) {
                Log::channel('errors_500')->error($e->getMessage(), [
                    'exception' => $e,
                ]);
            }
        }


        return parent::render($request, $e);
    }
}
