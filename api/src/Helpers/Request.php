<?php

namespace PartsSearch\Helpers;

use GuzzleHttp\Client;

class Request {

	private $url;
	private $query = [];
	private $headers = [];
	private $auth = [];

	/**
	 * Request constructor.
	 *
	 * @param string $url
	 */
	public function __construct(string $url) {
		$this->setUrl($url);
	}

	/**
	 * Return request body
	 *
	 * @return string
	 */
	public function getResponse($method = 'GET')
	{
		try {
			$client = new Client();
			$response = $client->request($method, $this->url, $this->getOptions() );
		}
		catch (\Exception $e) {
			// if request failed
			Response::error( $e->getMessage() );
		}

		$code = $response->getStatusCode();
		if( $code > 200 ) {
			// if request has invalid response
			Response::error('Invalid response', $code);
		}

		return (string) $response->getBody()->getContents();
	}

	/**
	 * Request's options array
	 *
	 * @return array
	 */
	private function getOptions()
	{
		return [
			'headers' => $this->headers,
			'query' => $this->query,
			'auth' => $this->auth,
		];
	}

	/**
	 * @param array $query
	 *
	 * @return Request
	 */
	public function setQuery( array $query ): Request {
		$this->query = $query;

		return $this;
	}

	/**
	 * @param array $headers
	 *
	 * @return Request
	 */
	public function setHeaders( array $headers ): Request {
		$this->headers = $headers;
		$this->headers['User-Agent'] = 'Moliza/5.0';

		return $this;
	}

	/**
	 * @param array $auth
	 *
	 * @return Request
	 */
	public function setAuth( array $auth ): Request {
		$this->auth = $auth;

		return $this;
	}

	/**
	 * @param string $url
	 */
	private function setUrl( $url ): void {
		$this->url = $url;
	}

}
