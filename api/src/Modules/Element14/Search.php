<?php

namespace PartsSearch\Modules\Element14;

use PartsSearch\Interfaces\ShouldRespond;
use PartsSearch\Helpers\Request;
use PartsSearch\Helpers\Response;

class Search implements ShouldRespond {

	/*
	 * Request query components
	 */
	public $url = 'https://api.element14.com/catalog/products';
	public $term = '';
	public $offset = 0;

	public function __construct()
	{
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
		$request->setQuery( $this->getQuery() );

		$response = $request->getResponse();

		Response::success([
			'data' => self::collection( json_decode($response,true) ),
		]);

	}

	/**
	 * Get request's query
	 *
	 * @return array
	 */
	public function getQuery()
	{
		return [
			'storeInfo.id' => 'ru.farnell.com',
			'resultsSettings.offset' => $this->offset,
			'resultsSettings.numberOfResults' => 10,
			'resultsSettings.refinements.filters' => 'inStock',
			'resultsSettings.responseGroup' => 'medium',
			'callInfo.omitXmlSchema' => 'false',
			'callInfo.responseDataFormat' => 'json',
			'callinfo.apiKey' => getenv('ELEMENT14_API_KEY'),
			'term' => 'any:' . $this->term
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
		$this->offset = (int) $_REQUEST['offset'];
	}

	public function collection( array $response ) {
		// todo: parse collection
		return $response;
	}

}
