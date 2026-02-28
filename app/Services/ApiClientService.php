<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class ApiClientService
{
	protected $client;
	
	public function __construct()
	{
		$stack = HandlerStack::create();

		$stack->push($this->authMiddleware());
		$stack->push($this->logMiddleware());
		$stack->push($this->retryMiddleware());

		$this->client = new Client([
			'handler' => $stack,
			'timeout' => 5,
		]);
	}

	public function getClient()
	{
		return $this->client;
	}

	protected function authMiddleware()
	{
		return Middleware::mapRequest(function (RequestInterface $request) {
			return $request->withHeader(
				'Authorization',
				'Bearer abcd123efg',
			);
		});
	}

	protected function logMiddleware()
	{
		return Middleware::tap(
			function (RequestInterface $request) {
				logger('Request URL: '. $request->getUri());
			},

			function ($response) {
				if ($response instanceof \Psr\Http\Message\ResponseInterface) {
					logger('Response URL: ' . $response->getStatusCode());
				}	
			},
		);
	}

	protected function retryMiddleware()
	{
		return Middleware::retry(function ($retries, $request, $response) {
			if ($retries >= 3) {
				return false;
			}

			if ($response && $response->getStatusCode() >= 500) {
				return true;
			}

			return false;
		});
	}
}