<?php

namespace App\HttpCommunication;

use App\HttpCommunication\Exceptions\Handler as ErrorHandler;
use App\HttpCommunication\Response\Concrete\Response;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

abstract class HttpCommunicationService implements HttpCommunicationServiceInterface
{
    /**
     * configの値
     *
     * @var array
     */
    protected $config;

    /**
     * http client
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * デフォルトのヘッダ
     *
     * @var array
     */
    protected $defaultHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * 追加で指定するヘッダ。次回のリクエストのみに適用される。
     *
     * @var array
     */
    protected $additionalHeaders = [];

    /**
     * 最大再試行回数
     *
     * @var int
     */
    protected $maxRetryTimes = 0;

    /**
     * 失敗時の待機時間（ミリ秒）
     *
     * @var int
     */
    protected $retryWaitTimeMsec = 1000;

    /**
     * デフォルトのオプション
     *
     * @var array
     */
    protected $defaultOptions = [
        // 'timeout' => 15.0, // タイムアウト（秒）
    ];

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->initializeConfig();

        $this->initialize();

        $this->client = $httpClient;

        if (isset($this->config['headers'])) {
            $this->defaultHeaders = array_merge($this->defaultHeaders, $this->config['headers']);
        }

        if (isset($this->config['options'])) {
            $this->defaultOptions = array_merge($this->defaultOptions, $this->config['options']);
        }
    }

    /**
     * 初期化処理。必要に応じてサブクラスで実装する。
     *
     * @return static
     */
    protected function initialize()
    {
        return $this;
    }

    /**
     * configを取得するためのキー。
     * サブクラスで実装する
     *
     * @return string
     */
    abstract protected function getConfigKey(): string;

    /**
     * エンドポイント設定 prefix取得
     *
     * @return null
     */
    protected function getEndpointPrefix()
    {
        return null;
    }

    /**
     * 追加で指定するヘッダの設定。次回のリクエストのみに適用される。
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function setAdditionalHeader(string $name, $value)
    {
        $this->additionalHeaders[$name] = $value;

        return $this;
    }

    /**
     * 追加で指定するヘッダの削除。
     *
     * @param string $name
     *
     * @return void
     */
    public function removeAdditionalHeader(string $name)
    {
        unset($this->additionalHeaders[$name]);

        return $this;
    }

    /**
     * configの初期化
     *
     * @return void
     */
    protected function initializeConfig()
    {
        $this->config = Config::get('http_communication.' . $this->getConfigKey());
    }

    /**
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getConfig(string $key = null)
    {
        if (!isset($this->config)) {
            $this->initializeConfig();
        }

        return isset($key)
            ? $this->config[$key]
            : $this->config;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function getBaseUrl(array $config = []): string
    {
        if (empty($config)) {
            $config = $this->getConfig();
        }

        if (!isset($config['prefix'])) {
            return $config['host'];
        }

        return $config['host'] . '/' . $config['prefix'];
    }

    /**
     * @param string $key
     * @param array $params
     *
     * @return array
     */
    public function getEndpoint(string $key, array $params = [])
    {
        $prefix = $this->getEndpointPrefix();
        if ($prefix) {
            list($method, $uri) = $this->getConfig('endpoint')[$prefix][$key];
        } else {
            list($method, $uri) = $this->getConfig('endpoint')[$key];
        }

        $method = strtoupper($method);

        foreach ($params as $from => $to) {
            $uri = str_replace(':' . $from, $to, $uri);
        }

        if (strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        $uri = $this->getBaseUrl() . $uri;

        return [$method, $uri];
    }

    /**
     * @param int $maxRetryTimes
     *
     * @return void
     */
    public function setMaxRetryTimes(int $maxRetryTimes)
    {
        $this->maxRetryTimes = $maxRetryTimes;
    }

    /**
     * リクエスト送信
     *
     * @param string $endpointKey
     * @param array $params
     * @param array $body
     * @param array $options クエリパラメータもここに入れる (例: [ 'query' => ['abc' => 'dfg'] ]）
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    public function request(
        string $endpointKey,
        array $params = [],
        array $body = [],
        array $options = []
    ) {
        try {
            $params = $this->preprocessRequest([$endpointKey, $params, $body, $options]);

            [$method, $uri, $headers, $body, $options] = $params;

            if (config('http_communication.save_log.ymdy_member_system_api', false)) {
                Log::info("${method} ${uri}", [
                    'headers' => $headers,
                    'body' => $body,
                    'options' => $options,
                ]);
            }

            $response = $this->send([$method, $uri, $headers, $body, $options]);

            if (config('http_communication.save_log.ymdy_member_system_api', false)) {
                Log::info("${method} ${uri}", [
                    'headers' => $headers,
                    'body' => $body,
                    'options' => $options,
                    'statusCode' => $response->getStatusCode(),
                    'response' => $response->getBody(),
                ]);
            }

            return $this->newResponse($response);
        } catch (RequestException $e) {
            $this->handleRequestError($e);
        }
    }

    /**
     * @param array $bundle
     *
     * @return array
     */
    protected function preprocessRequest(array $bundle)
    {
        [$endpointKey, $params, $body, $options] = $bundle;

        list($method, $uri) = $this->getEndpoint($endpointKey, $params);

        $body = $this->encodeArrayBody($body);

        $headers = array_merge($this->defaultHeaders, $this->additionalHeaders, $options['headers'] ?? []);
        unset($options['headers']);
        $this->additionalHeaders = [];

        $options = array_merge($this->defaultOptions, $options);

        return [$method, $uri, $headers, $body, $options];
    }

    /**
     * レスポンスインスタンスを生成する
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \App\HttpCommunication\Response\ResponseInterface
     */
    protected function newResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        return new Response($response);
    }

    /**
     * 例外処理
     *
     * @param \GuzzleHttp\Exception\RequestException $exception
     *
     * @return void
     */
    protected function handleRequestError(RequestException $exception)
    {
        report($exception);
        ErrorHandler::handle($exception);
    }

    /**
     * @param array $params
     * @param int $retry
     * @param Exception $exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws RequestException
     */
    private function send(array $params, int $retry = 0, RequestException $exception = null)
    {
        if ($retry > $this->maxRetryTimes) {
            throw $exception;
        }

        try {
            [$method, $uri, $headers, $body, $options] = $params;

            $request = new Request($method, $uri, $headers, $body);

            $response = $this->client->send($request, $options);

            $this->validateResponse($request, $response);

            return $response;
        } catch (ClientException $e) {
            // 4xx系のエラー
            throw $e;
        } catch (RequestException $e) {
            report($e);

            usleep(ms2us($this->retryWaitTimeMsec));

            $this->send($params, ++$retry, $e);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    protected function validateResponse(\Psr\Http\Message\RequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    {
        return; // 継承している子クラスで例外を投げる。戻り値は結果に影響しない。
    }

    /**
     * @param array $body
     *
     * @return string
     */
    protected function encodeArrayBody(array $body)
    {
        return json_encode($body);
    }
}
