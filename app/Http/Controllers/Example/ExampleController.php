<?php
namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class ExampleController extends Controller
{
    #[ArrayShape(['request' => "array", 'auth' => "array"])] public function test(Request $request): array
    {
        $params = $request->all();
        $auth = ['By' => 'sreio' , 'sex' => 1];
        return ['request' => $params, 'auth' => $auth];
    }

    public function test2(): bool
    {
        return true;
    }

    public function test3(): JsonResponse
    {
        return $this->failed(['one_err', 'two_err' => ["This is", 'Array']], 40001);
    }

    public function ok(): JsonResponse
    {
//        return $this->setStatusCode(0)->success(['key' => 'ok']);
        return $this->success('This is ok Action', 1000, 'ok');
    }

    public function err(): JsonResponse
    {
        return $this->failed('This is err Action !', 4000, 'err');
    }
}
