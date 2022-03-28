<?php

namespace App\Exceptions;

use App\Helpers\ExceptionReport;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
     * @param $request
     * @param Throwable $e
     * @return Response|JsonResponse|BaseResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|BaseResponse
    {
        if ($request->ajax() || Str::startsWith($request->path(), 'api')) {
            $report = ExceptionReport::make($e);
            if ($report->shouldReturn()) {
                return $report->report();
            }
            if(env('APP_DEBUG')){
                return parent::render($request, $e);
            }else{
                return $report->prodReport();
            }
        }
        return parent::render($request, $e);
    }
}
