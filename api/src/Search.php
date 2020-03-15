<?php

namespace ArrowComSearch;

use Dotenv\Dotenv;

class Search {

	/*
	 * OAuth token
	 */
	private $token;

	/*
	 * Request query components
	 */
	public $url = 'https://my.arrow.com/api/priceandavail/search';
	public $billTo = 0;
	public $shipTo = 0;
	public $currency = 'RUB';
	public $quantity = 1;
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

		$response = $this->seed();
//		$response = $request->getResponse();

		Response::success([
			'data' => $response,
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
	private function getQuery()
	{
		return [
			'billTo' => $this->billTo,
			'shipTo' => $this->shipTo,
			'currency' => $this->currency,
			'quantity' => $this->quantity,
			'limit' => $this->limit,
			'search' => $this->term
		];
	}

	/**
	 * Set request's query
	 *
	 * @return void
	 */
	private function setQuery()
	{
		$this->term = trim( $_GET['search'] );
	}

	private function seed()
	{

		sleep(1);

		$id = time() + mt_rand(1000,2000);
		$data = [];

		for($i = 0; $i < $this->limit; $i++) {
			$data[] = [
				'id' => $id + $i,
				'label' => self::generateRandomString( mt_rand(4,6) ),
				'attr1' => self::generateRandomString( mt_rand(6,8) ),
				'attr2' => self::generateRandomString( mt_rand(6,8) ),
				'attr3' => self::generateRandomString( mt_rand(6,8) ),
			];
		}

		return $data;

	}

	public static function generateRandomString($length = 10) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}

}
