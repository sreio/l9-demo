<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    /**
     * @param $method
     * @param $parameters
     * @return JsonResponse|View|Response
     */
    public function callAction($method, $parameters): JsonResponse|View|Response
    {
        $response = parent::callAction($method, $parameters);
        if ($response instanceof Response
        || $response instanceof View
        ) {
            return $response;
        }
        return $this->success($response);
    }
}
