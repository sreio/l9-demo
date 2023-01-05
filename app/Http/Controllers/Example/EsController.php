<?php

namespace App\Http\Controllers\Example;


use App\Components\Es\Es;
use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Exception;
use Http\Promise\Promise;
use Illuminate\Http\Request;

class EsController extends Controller
{
    private $es;

    public function __construct()
    {
        $this->es = new Es();
    }

    /**
     * @OA\Get(
     *     path="/api/es/info",
     *     tags={"es API"},
     *     summary="es基本信息",
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
    public function info(): array
    {
        return $this->es->info();
    }

    public function existsIndex(Request $request)
    {
        return $this->es->existsIndex($request->input('index_key', 'test_ik'));
    }

    public function createIndex(Request $request)
    {
        return $this->es->createIndex($request->input('index_key', 'test_ik'));
    }

    public function delIndex(Request $request)
    {
        return $this->es->delIndex($request->input('index_key', 'test_ik'));
    }


    public function existsDoc(Request $request)
    {
        return $this->es->existsDoc(7, $request->input('index_key', 'test_ik'));
    }

    public function addDoc(Request $request)
    {
        $data = [
            'id' => 7,
            'name' => 'weidada7',
            'age' => 18,
            'memo' => '嘿嘿哈哈7',
            'desc' => 'test7',
        ];
        return $this->es->addDoc(5, $data, 'weidada');
    }

    public function updateDoc(Request $request)
    {
        $data = [
            'id' => 7,
            'name' => 'weidada7-1',
            'age' => 17,
            'memo' => '嘿嘿哈哈7-1',
            'desc' => 'test7-1',
            'time' => time(),
        ];
        return $this->es->updateDoc(7, $data, 'weidada');
    }

    public function deleteDoc(Request $request)
    {
        return $this->es->deleteDoc(5, 'weidada');
    }

    public function getDoc(Request $request)
    {
        return $this->es->getDoc(7, 'weidada');
    }

    public function searchDoc(Request $request)
    {
        $body = [
            'query' => [
                'match' => [
                    // 查询年纪
                    'age' => [
                        'query' => $request->input('search', '')
                    ]
                ]
            ],
            'highlight'=>[
                'fields'=>[
                    'fang_name'=>[
                        'pre_tags'=>[
                            '<span style="color: red">'
                        ],
                        'post_tags'=>[
                            '</span>'
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->es->searchDoc('weidada', $body);
        $data = array_column($response['hits']['hits'], '_source');
//        printf("Total docs: %d\n", $response['hits']['total']['value']);
//        printf("Max score : %.4f\n", $response['hits']['max_score']);
//        printf("Took      : %d ms\n", $response['took']);
//        print_r($response['hits']['hits']); // documents

        return $data;
    }
}