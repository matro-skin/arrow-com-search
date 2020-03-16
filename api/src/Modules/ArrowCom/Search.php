<?php

namespace PartsSearch\Modules\ArrowCom;

use PartsSearch\Interfaces\ShouldRespond;
use PartsSearch\Helpers\Request;
use PartsSearch\Helpers\Response;
use Dotenv\Dotenv;

class Search implements ShouldRespond {

	/*
	 * OAuth token
	 */
	private $token;

	/*
	 * Request query components
	 */
	public $url = 'https://my.arrow.com/api/priceandavail/search';
	public $currency = 'RUB';
	public $limit = 10;
	public $term = '';

	public function __construct()
	{

		// init .env variables
		$this->env();

		// get OAuth token
		$oauth = new OAuth();
		$this->token = $oauth->getToken();

		// append GET to query
		$this->setQuery();

	}

	/**
	 * Return response from Arrow.com API
	 *
	 * @return void
	 */
	public function getResponse()
	{

		$request = new Request( $this->url );
		$request->setHeaders([
			'Accept' => 'application/json',
			'Authorization' => sprintf("Bearer %s", $this->token->access_token),
		])->setQuery( $this->getQuery() );

		$response = $request->getResponse();

		Response::success([
			'data' => json_decode($response),
		]);

	}

	/**
	 * Init .env variables
	 */
	protected function env()
	{
		try {
			$dotenv = Dotenv::createImmutable(API_DIR );
			$dotenv->load();
		}
		catch (\Exception $e) {
			Response::error( $e->getMessage() );
		}
	}

	/**
	 * Get request's query
	 *
	 * @return array
	 */
	public function getQuery()
	{
		return [
			'currency' => $this->currency,
			'limit' => $this->limit,
			'search' => $this->term
		];
	}

	/**
	 * Set request's query
	 *
	 * @return void
	 */
	public function setQuery()
	{
		$this->term = trim( $_REQUEST['search'] );
	}

}
