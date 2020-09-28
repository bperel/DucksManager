<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;

    private const CHUNKABLE_URLS = [
        '/coa/list/countries' => 50,
        '/coa/list/publications' => 10
    ];

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @param string $role
     * @param array $parameters
     * @param string $method
     * @param bool $doNotChunk
     * @return array|bool|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function call(string $url, string $role, $parameters = [], $method = 'GET', $doNotChunk = false)
    {
        if (!$doNotChunk && isset(self::CHUNKABLE_URLS[$url])) {
            return self::callWithChunks($url, $role, $parameters, $method);
        }
        $fullUrl = "http://api$url" . ($method === 'GET' ? '/'.implode('/', $parameters) : '');
        $response = $this->client->request(
            $method,
            $fullUrl, [
                'auth_basic' => [$role, $_ENV['ROLE_PASSWORD_' . strtoupper('DUCKSMANAGER')]],
                'headers' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cache-Control: no-cache',
                    'x-dm-version: 1.0',
                    'x-dm-user: '.($_COOKIE['dm-user'] ?? $_COOKIE['user'] ),
                    'x-dm-pass: '.($_COOKIE['dm-pass'] ?? $_COOKIE['pass'] ),
                ],
                'body' => $method === 'GET' ? null : $parameters
            ]
        );

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            if (empty($response->getContent()) || $response->getContent() === 'OK') {
                return true;
            }
            return $response->toArray();
        }

        $this->logger->info("Call to service $method $url failed, Response code = {$response->getStatusCode()}, response buffer = {$response->getContent()}");
        return null;
    }

    private function callWithChunks($url, $role, array $parameters, $method = 'GET')
    {
        $parameterListChunks = array_chunk(explode(',', $parameters[count($parameters) - 1]), self::CHUNKABLE_URLS[$url]);
        $results = null;
        foreach ($parameterListChunks as $parameterListChunk) {
            $result = $this->call($url, $role, [implode(',', $parameterListChunk)], $method, true);
            if (is_object($result)) {
                $results = is_null($results) ? $result : (object)array_merge_recursive((array)$results, (array)$result);
            } else if (is_array($result)) {
                $results = is_null($results) ? [] : array_merge($results, $result);
            }
        }
        return $results;
    }

}