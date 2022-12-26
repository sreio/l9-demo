<?php

namespace App\Components\Es;

use App\Components\BaseComponents;
use Closure;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;

class Es extends BaseComponents
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * 构造函数
     * MyElasticsearch constructor.
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();
    }


    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function info(): array
    {
        return $this->client->info()->asArray();
    }

    /**
     * 判断索引是否存在
     * @throws Exception
     */
    public function existsIndex($indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName
        ];

        return $this->callback(function () use ($params){
            return $this->client->indices()->exists($params)->getStatusCode() === 200;
        });
    }

    /**
     * 创建索引
     * @throws Exception
     */
    public function createIndex($indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 5,
                    'number_of_replicas' => 1
                ]
            ]
        ];

        return $this->callback(function () use ($params){
            return $this->client->indices()->create($params)->getStatusCode() === 200;
        });
    }

    /**
     * 删除索引
     * @throws Exception
     */
    public function delIndex($indexName = 'test_ik')
    {
        $params = [
            'index' => $indexName
        ];
        return $this->callback(function () use ($params) {
           return $this->client->indices()->delete($params)->getStatusCode() === 200;
        });
    }

    /**
     * 获取文档
     * @param int $id
     * @param string $index_name
     * @param string $type_name
     * @return mixed
     * @throws Exception
     */
    public function getDoc(int $id = 1, string $index_name = 'test_ik', string $type_name = 'goods'): mixed
    {
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'id' => $id
        ];
        return $this->callback(function () use ($params){
            return $this->client->get($params)->asArray();
        });
    }

    /**
     * 判断文档存在
     * @param int $id
     * @param string $index_name
     * @param string $type_name
     * @return mixed
     * @throws Exception
     */
    public function existsDoc(int $id = 1, string $index_name = 'test_ik', string $type_name = 'test_type'): mixed
    {
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'id' => $id
        ];
        return $this->callback(function () use ($params){
            return $this->client->exists($params)->getStatusCode() === 200;
        });
    }

    /**
     * 添加文档
     * @param $id
     * @param array $doc ['id'=>100, 'title'=>'phone']
     * @param string $index_name
     * @param string $type_name
     * @return mixed
     * @throws Exception
     */
    public function addDoc($id, array $doc, string $index_name = 'test_ik', string $type_name = 'test_type'): mixed
    {
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'id' => $id,
            'body' => $doc
        ];
        return $this->callback(function () use ($params){
            return $this->client->index($params)->getStatusCode() === 200;
        });
    }

    /**
     * 更新文档
     * @param int $id
     * @param string $index_name
     * @param string $type_name
     * @param array $body ['doc' => ['title' => '苹果手机iPhoneX']]
     * @return mixed
     * @throws Exception
     */
    public function updateDoc(int $id = 1, array $body=[], string $index_name = 'test_ik', string $type_name = 'test_type'): mixed
    {
        // 可以灵活添加新字段,最好不要乱添加
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'id' => $id,
            'body' => ['doc' => $body]
        ];
        return $this->callback(function () use ($params){
            return $this->client->update($params)->getStatusCode() === 200;
        });
    }

    /**
     * 删除文档
     * @param int $id
     * @param string $index_name
     * @param string $type_name
     * @return mixed
     * @throws Exception
     */
    public function deleteDoc(int $id = 1, string $index_name = 'test_ik', string $type_name = 'test_type'): mixed
    {
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'id' => $id
        ];
        return $this->callback(function () use ($params){
            return $this->client->delete($params)->getStatusCode() === 200;
        });
    }

    /**
     * 搜索文档 (分页，排序，权重，过滤)
     * @param string $index_name
     * @param string $type_name
     * @param array $body
     * @return mixed
     * @throws Exception
     */
    public function searchDoc(string $index_name = "test_ik", string $type_name = "test_type", array $body=[]): mixed
    {
        $params = [
            'index' => $index_name,
            'type' => $type_name,
            'body' => $body
        ];
        return $this->callback(function () use ($params){
            return $this->client->search($params)->asArray();
        });
    }


    /**
     * 统一处理异常
     * @throws Exception
     */
    private function callback(Closure $callback)
    {
        try {
            return $callback();
        } catch (ClientResponseException $e) { // HTTP客户端错误，状态代码为4xx
            throw new Exception($e->getMessage());
        } catch (MissingParameterException $e) { // 缺少参数错误
            throw new Exception($e->getMessage());
        } catch (ServerResponseException $e) { // Server端 错误，状态代码为5xx
            throw new Exception($e->getMessage());
        }
    }
}