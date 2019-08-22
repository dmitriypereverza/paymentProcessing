<?php

namespace app\components;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use yii\base\Component;

class RequestManager extends Component {
    public $baseUrl;

    /** @var Client */
    private $client;

    /** @var ResponseInterface */
    private $lastErrorResponse;

    public function init() {
        parent::init();
        if (!$this->baseUrl) {
            throw new \Exception('Не установлен базовый url');
        }
        $this->client = new Client();
    }

    /**
     * @param $type
     * @param string $url
     * @param array $params
     * @param array $requestParam
     *
     * @return array
     */
    public function send($type, string $url, array $params = [], array $requestParam = []) {
        $response = $this->makeRequest($type, $url, $params, $requestParam);
        if ($response->getStatusCode() === 200 && $response->getReasonPhrase() === 'OK') {
            $responseBody = json_decode($response->getBody(), true);
            return $responseBody ?: [];
        }
        $this->errorHandler($url, $response);
    }

    public function get(string $url, array $params = [], array $requestParam = []) {
        return $this->send('GET', $url, $params, $requestParam);
    }

    public function post(string $url, array $params = [], array $requestParam = []) {
        return $this->send('POST', $url, $params, $requestParam);
    }

    /**
     * @return ResponseInterface
     */
    public function getLastErrorResponse(): ?ResponseInterface {
        return $this->lastErrorResponse;
    }

    private function makeRequest($type, string $url, array $params = [], array $requestParam = []) {
        $this->lastErrorResponse = null;

        [$queryParams, $formParams] = $this->getTypeParams($type, $params);
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->client->request(
            $type,
            $this->baseUrl . '/' . $url,
            array_merge($requestParam, [
                'http_errors' => false,
                'query' => $queryParams
            ],
                $formParams ? ['form_params' => $formParams] : []
            )
        );
    }

    /**
     * @param $type
     * @param $params
     *
     * @return array
     */
    private function getTypeParams(string $type, $params): array {
        $queryParams = $formParams = [];

        if (strtoupper($type) === 'GET') {
            $queryParams = $params;
        } else {
            $formParams = $params;
        }

        return [$queryParams, $formParams];
    }

    /**
     * @param string $url
     * @param ResponseInterface $response
     */
    public function errorHandler(string $url, ResponseInterface $response): void {
        $this->lastErrorResponse = $response;
    }
}
