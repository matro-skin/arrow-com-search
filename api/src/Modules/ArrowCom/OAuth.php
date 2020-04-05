<?php

namespace PartsSearch\Modules\ArrowCom;

use PartsSearch\Helpers\Request;
use PartsSearch\Helpers\Response;

class OAuth
{

	private $username;
	private $password;
	private $url = 'https://my.arrow.com/api/security/oauth/token';

	public $token;
	private $file = ENV_PATH . '/.token_ArrowCom';

	public function __construct()
	{
		\PartsSearch\Search::log('Start Oauth');

		try {
			$this->env();
		} catch (\Exception $e) {
			Response::error($e->getMessage(), 404);
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
		// try load token from $this->file
		$this->loadToken();

		// if token not exists
		if (! $this->token) {

			\PartsSearch\Search::log('Empty token');

			// request token data
			$this->requestToken();

			// append expires_at field
			// for checking if token being expired
			$this->appendExpiresAt();

			// store token data to $this->file
			$this->storeToken();
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

		\PartsSearch\Search::log('Try to get token');

		if (! file_exists($this->file)) {
			$this->token = null;
			\PartsSearch\Search::log('File not exists');

			return;
		}

		if (time() - filemtime($this->file) > 3500) {
			\PartsSearch\Search::log('File is expired');
			$this->token = null;

			return;
		}

		$this->token = file_get_contents($this->file);
		$this->token = json_decode($this->token);

		if ($this->token->expires_at - time() < 0) {
			\PartsSearch\Search::log('Token is expired');
			$this->token = null;

			return;
		}
	}

	/**
	 * Request token from API
	 *
	 * @return void
	 */
	private function requestToken()
	{
		\PartsSearch\Search::log('Request new token');

		$request = new Request($this->url);

		$request->setQuery([ 'grant_type' => 'client_credentials' ])
		        ->setHeaders([ 'client_id' => $this->username ])
		        ->setAuth([ $this->username, $this->password ]);

		$this->token = $request->getResponse('POST');
	}

	/**
	 * Add "expires_at" to token object
	 *
	 * @return void
	 */
	private function appendExpiresAt()
	{
		$token = (string) $this->token;
		$token = json_decode($token, true);

		$token[ 'expires_at' ] = time() + $token[ 'expires_in' ];
		$token[ 'expires_at' ] -= 60; // let decrease in a minute...

		$this->token = json_encode($token);
	}

	/**
	 * Store token to $this->file
	 *
	 * @return void
	 */
	private function storeToken()
	{
		if (! file_put_contents($this->file, $this->token)) {
			Response::error('Store token failed', 400);
		}

		$this->loadToken();
	}

	/**
	 * Get .env variables
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function env()
	{
		$this->username = getenv('ARROWCOM_OAUTH_USERNAME');
		if (! $this->username) {
			throw new \Exception('Undefined OAuth username');
		}

		$this->password = getenv('ARROWCOM_OAUTH_PASSWORD');
		if (! $this->password) {
			throw new \Exception('Undefined OAuth password');
		}
	}

}
