<?php
namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Generator;

/**
 * @OA\Info(
 *     title="API test l9",
 *     version="1.0"
 * )
 *
 * @OA\Schema(
 *   schema="apiResuponse",
 *   @OA\Property(property="code", type="integer", description="状态码(0:成功,-1:失败)"),
 *   @OA\Property(property="status", type="string", description="状态"),
 *   @OA\Property(property="data", type="object", description="数据", default=null),
 * )
 *
 * @OA\SecurityScheme(type="http", securityScheme="bearer", scheme="bearer", bearerFormat="JWT")
 *
 *
 */

class ExampleController extends Controller
{
    #[ArrayShape(['request' => "array", 'auth' => "array"])] public function test(Request $request): array
    {
        $sql = User::query()->where('name', 'like', "%w%")->get();
        $params = $request->all();
        $auth = ['By' => 'sreio' , 'sex' => 1];
        return ['request' => $params, 'auth' => $auth, 'sql' => $sql];
    }

    public function doc(Request $request)
    {
        if ($request->type == 'json') {
            $openapi = Generator::scan([__DIR__]);
            echo $openapi->toJson();die();
        }

        return view('doc', ['apiUrl' => config('app.url') . '/api/example/doc?type=json']);
    }


    /**
     * @OA\Get(
     *     path="/api/example/test2",
     *     tags={"测试API"},
     *     summary="返回测试",
     *     @OA\Parameter(name="id", in="query", @OA\Schema(type="integer"), description="id"),
     *     @OA\Response(
     *         response="200",
     *         description="ok",
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/apiResuponse"),
     *                      @OA\Schema(
     *                          @OA\Property(property="data", type="string",
     *                              @OA\Property(property="id", type="integer", description="id"),
     *                              @OA\Property(property="number", type="integer", description="数字"),
     *                              @OA\Property(property="str", type="string", description="字符串"),
     *                              @OA\Property(property="list", type="array", description="列表", @OA\Items(type="object",
     *                                  @OA\Property(property="a", type="string", description="a"),
     *                                  @OA\Property(property="b", type="string", description="b"),
     *                              )),
     *                          )
     *                      )
     *                  }
     *              )
     *         )
     *     )
     * )
     */
    public function test2(Request $request)
    {
        return ['id' => $request->id ?? 0, 'number' => 1123, 'str' => 'abc伟大大' , 'list' => [['a' => 'aa', 'b' => 'bb']]];
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
