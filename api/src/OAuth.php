<?php

namespace ArrowComSearch;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;

class OAuth {

	private $username;
	private $password;
	private $url = 'https://my.arrow.com/api/security/oauth/token';

	public $token;
	private $file = API_DIR . '/.token';

	public function __construct()
	{
		try {
			$this->env();
		} catch (\Exception $e) {
			Response::error( $e->getMessage() );
		}
	}

	/**
	 * Return token object
	 * access_token,token_type,expires_in,expires_at,scope,jti
	 *
	 * @return object
	 */
	public function getToken()
	{
		$this->loadToken();
		if(! $this->token) {
			$this->requestToken();
		}
		return $this->token;
	}

	/**
	 * Load token from file
	 *
	 * @return void
	 */
	private function loadToken()
	{

		if(! file_exists( $this->file )) {
			$this->token = null;
			return;
		}

		if( time() - filemtime($this->file) > 3500 ) {
			$this->token = null;
			return;
		}

		$this->token = file_get_contents( $this->file );
		$this->token = json_decode($this->token);

		if( $this->token->expires_at - time() < 0 ) {
			$this->token = null;
			return;
		}

	}

	/**
	 * Request token from API
	 */
	private function requestToken()
	{

		$options = [
			'headers' => [ 'client_id' => $this->username ],
			'query'   => [ 'grant_type' => 'client_credentials' ],
			'auth'    => [ $this->username, $this->password ]
		];

		try {
			$client = new Client();
			$response = $client->request('POST', $this->url, $options);
		}
		catch (RequestException $e) {

			$response = Psr7\str( $e->getResponse() );
			$response = substr( $response, strpos($response,'{"') );
			$response = json_decode($response);

			Response::error( $response->error_description );

		}

		$code = $response->getStatusCode();
		if( $code > 200 ) {
			Response::error('Invalid response', $code);
		}

		$this->token = $response->getBody();

		$this->appendExpiredAt();
		$this->storeToken();

	}

	/**
	 * Add "expires_at" to token object
	 */
	private function appendExpiredAt()
	{

		$token = (string) $this->token;
		$token = json_decode($token,true);

		$token['expires_at'] = time() + $token['expires_in'];

		// let decrease in a minute...
		$token['expires_at'] -= 60;

		$this->token = json_encode($token);

	}

	/**
	 * Store token to file
	 */
	private function storeToken()
	{
		if(! file_put_contents($this->file, $this->token)) {
			Response::error('Store token failed');
		}
		$this->loadToken();
	}

	/**
	 * Get env variables
	 *
	 * @throws \Exception
	 * @return void
	 */
	private function env()
	{

		$this->username = getenv('OAUTH_USERNAME');
		if(! $this->username) {
			throw new \Exception('Undefined OAuth username');
		}

		$this->password = getenv('OAUTH_PASSWORD');
		if(! $this->password) {
			throw new \Exception('Undefined OAuth password');
		}

	}

}
