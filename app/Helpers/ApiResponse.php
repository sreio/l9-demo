<?php
namespace App\Helpers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait ApiResponse
{

    /**
     * Normal return resources, whose default value is 0.
     * @var int
     */
    protected int $statusCode = HttpCode::SUCCESS_OK;

    /**
     * @param int|null $code
     * @return $this
     */
    public function setStatusCode(int $code = null): static
    {
        if ($code !== null) {
            $this->statusCode = $code;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $data
     * @param array $header
     * @return JsonResponse
     */
    public function respond($data, array $header = []): JsonResponse
    {
        return Response::json($data, 200, $header);
    }

    /**
     * @param $status
     * @param array $data
     * @param int|null $code
     * @return JsonResponse
     */
    public function status($status, array $data, int $code = null): JsonResponse
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $respond = [
            'code' => $this->statusCode,
            'status' => $status,
        ];

        $respond = array_merge($respond, $data);
        return $this->respond($respond);
    }

    /**
     * @param $data
     * @param int|null $code
     * @param string $status
     * @return JsonResponse
     */
    public function success($data, int $code = null, string $status = 'success'): JsonResponse
    {
        return $this->setStatusCode($code)->status($status, compact('data'));
    }

    /**
     * @param $msg
     * @param int $code
     * @param string $status
     * @return JsonResponse
     */
    public function failed($msg, int $code = HttpCode::FAILED_ERROR, string $status = 'error'): JsonResponse
    {
        return $this->setStatusCode($code)->status($status, compact('msg'));
    }
}
