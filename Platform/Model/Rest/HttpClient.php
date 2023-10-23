<?php

namespace Fortvision\Platform\Model\Rest;

use Fortvision\Platform\Logger\Integration as LoggerIntegration;
use Fortvision\Platform\Provider\GeneralSettings;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\RequestOptions;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClient
 * @package Fortvision\Platform\Model\Rest
 */
class HttpClient
{
    const LIMIT = 5;

    /**
     * @var Manager
     */
    protected $eventManager;

    /**
     * @var GeneralSettings
     */
    protected $generalSettings;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var LoggerIntegration|OaIntegration
     */
    protected $logger;

    /**
     * HttpClient constructor.
     * @param Manager $eventManager
     * @param GeneralSettings $generalSettings
     * @param ResponseFactory $responseFactory
     * @param ClientFactory $clientFactory
     * @param Json $json
     * @param LoggerIntegration $logger
     */
    public function __construct(
        Manager $eventManager,
        GeneralSettings $generalSettings,
        ResponseFactory $responseFactory,
        ClientFactory $clientFactory,
        Json $json,
        LoggerIntegration $logger
    ) {
        $this->eventManager = $eventManager;
        $this->generalSettings = $generalSettings;
        $this->responseFactory = $responseFactory;
        $this->clientFactory = $clientFactory;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * @param string $uriEndpoint
     * @param array $params
     * @param $requestMethod
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function doRequest(string $uriEndpoint, array $params = [], $requestMethod = Request::HTTP_METHOD_GET)
    {
       // echo('doRequest-'.$uriEndpoint);
        if (!$this->generalSettings->isModuleEnabled()) {
            throw new LocalizedException(__('Fortvision Integration is disabled'));
        }
        /** @var Client $client */
        $client = $this->clientFactory->create();
        $requestedUrl = 'https://magentotools.fortvision.net';//$this->getUrl($uriEndpoint, $params, $requestMethod);
        $this->logger->debug('Fortvision Request: ' . $requestedUrl, $params);
        $this->setHeaders($params);
        $response = $this->request($client, $requestedUrl, $params, $requestMethod);
        if ($response != null && $response->getStatusCode() != 204) {
            throw new LocalizedException(__('Fortvision Integration error ' . $response->getStatusCode() . ' - ' . $response->getReasonPhrase()));
        }
        $this->logger->debug('Fortvision Response Code: ' . $response->getStatusCode());
        return $response;
    }

    /**
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     * @return string
     */
    private function getUrl(string $uriEndpoint, array $params, string $requestMethod)
    {
        $query = '';
        if ($requestMethod == Request::HTTP_METHOD_GET) {
            if (!isset($params['startRow'])) {
                $params['startRow'] = 1;
            }
            if (!isset($params['pageRows'])) {
                $params['pageRows'] = self::LIMIT;
            }
        }

        if (isset($params['body'])) {
            unset($params['body']);
        }

        if (count($params)) {
            $query .= '?' . http_build_query($params);
        }

        return $this->generalSettings->getUrl() . $uriEndpoint . $query;
    }

    /**
     * @param array $params
     */
    private function setHeaders(array &$params)
    {

        $headers = [
            'Accept-Encoding' => 'gzip, deflate',
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/xml'
        ];
        $headers = isset($params['headers']) ? array_merge($headers, $params['headers']) : $headers;
        $params['headers'] = $headers;
    }

    /**
     * @param Client $client
     * @param $requestedUrl
     * @param array $params
     * @param $requestMethod
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function request(
        Client $client,
        $requestedUrl,
        array $params,
        $requestMethod = REQUEST::HTTP_METHOD_POST
    ) {
        $params[RequestOptions::VERIFY] = false;
        if ($this->generalSettings->useSslVerify()) {
            $params[RequestOptions::VERIFY] = true;
        }

        return $client->request($requestMethod, $requestedUrl, $params);
    }
}
