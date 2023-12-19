<?php

namespace App\Exceptions;

use App\Http\Controllers\Admin\General\ErrorController;
use App\Http\Controllers\Frontend\Common\ErrorController as CommonErrorController;
use App\Http\Controllers\Merchant\General\ErrorController as GeneralErrorController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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



    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable|HttpException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($this->isHttpException($exception)) {

            $code = $exception->getStatusCode();
            if (!$request->expectsJson()) {

                // dd($request->is('admin/*'));
                if ($request->is('admin/*') && in_array($code, [403, 404])) {
                    /**
                     * @var ErrorController
                     */
                    $errorController = app(ErrorController::class);

                    return response($errorController->showError($request, $code), $code);
                }
                // dd($request->is('admin/*'));
                if ($request->is('merchant/*') && in_array($code, [403, 404])) {
                    /**
                     * @var GeneralErrorController
                     */
                    $errorController = app(GeneralErrorController::class);

                    return response($errorController->showError($request, $code), $code);
                }
                
                if($code == 419){
                    return \Illuminate\Support\Facades\Redirect::back()->withErrors(['_token.mismatch' => 'Token Mismatch'])->withInput()->with('error', 'Token mismatch');
                }
                return parent::render($request, $exception);

                // if ($code == 404) {


                //     /**
                //      * @var CommonErrorController
                //      */
                //     $errorController = app(CommonErrorController::class);
                //     if($errorController->checkModuleExists($code)) return response($errorController->reportError($request, $code), $code);
                // }
                return response([
                    'status' => false,
                    'status_code' => $code,
                    'status_text' => Response::$statusTexts[$code] ?? '',
                    'message' => Response::$statusTexts[$code] ?? '',

                ], $code);
            }
            if ($request->is('api/*') && in_array($code, [403, 404])) {
                return response([
                    'status' => false,
                    'status_code' => $code,
                    'status_text' => Response::$statusTexts[$code] ?? '',
                    'message' => Response::$statusTexts[$code] ?? '',

                ], 200);
            }

            if ($request->is('api/*')) {
                return response([
                    'status' => false,
                    'status_code' => Response::HTTP_UNAUTHORIZED,
                    'status_text' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                    'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
                ], 200);
            }
        }
        return parent::render($request, $exception);
    }
}
