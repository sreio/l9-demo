<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionReport
{
    use ApiResponse;
    /**
     * @var Request
     */
    public Request $request;

    /**
     * @var Throwable
     */
    public Throwable $throwable;

    /**
     * @var
     */
    protected $report;

    public function __construct(Request $request, Throwable $throwable)
    {
        $this->request = $request;
        $this->throwable = $throwable;
    }


    /**
     * A list of the exception types that are reported.
     *
     * @var array|array[]
     */
    public array $doReport = [
        ModelNotFoundException::class => ['模型未找到', 404],
        ValidationException::class => ['参数验证错误', 422],
        NotFoundHttpException::class => ['接口不存在', 404],
        MethodNotAllowedHttpException::class => ['访问方式不正确', 405],
    ];

    /**
     * @param $className
     * @param callable $callable
     * @return void
     */
    public function register($className, callable $callable)
    {
        $this->doReport[$className] = $callable;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Throwable $throwable
     * @return $this
     */
    public static function make(Throwable $throwable): static
    {
        return new static(Request(), $throwable);
    }

    /**
     * Determine if the exception handler response should return.
     *
     * @return bool
     */
    public function shouldReturn(): bool
    {
        foreach (array_keys($this->doReport) as $report) {
            if ($this->throwable instanceof $report) {
                $this->report = $report;
                return true;
            }
        }
        return false;
    }

    /**
     * Throwing Exceptions.
     *
     * @return JsonResponse
     */
    public function report(): JsonResponse
    {
        if ($this->throwable instanceof ValidationException) {
            $error = $this->throwable->errors();
            return $this->failed($error, $this->throwable->status);
        }
        $report = $this->doReport[$this->report];
        return $this->failed($report[0], $report[1]);
    }

    /**
     * @return JsonResponse
     */
    public function prodReport(): JsonResponse
    {
        return $this->failed('服务器错误', 500);
    }
}
