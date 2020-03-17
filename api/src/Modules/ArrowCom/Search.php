<?php

namespace PartsSearch\Modules\ArrowCom;

use PartsSearch\Interfaces\ShouldRespond;
use PartsSearch\Helpers\Request;
use PartsSearch\Helpers\Response;

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

		Response::success( self::collection( json_decode($response,true) ) );

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


	public function collection( array $response )
	{
		$data = [];
		foreach ($response['pricingResponse'] as $item) {
			$resource = $this->parseItem($item);
			$resource['loop_key'] = \PartsSearch\Search::loop_key($resource);
			$data[] = $resource;
		}

		return [
			'meta' => $this->meta($response),
			'data' => $data,
		];
	}

	private function meta(array $response)
	{
		return [
			'results' => $response['results'],
			'pages' => $response['pages'],
			'totalRecords' => $response['totalRecords'],
			'currentPage' => $response['currentPage'],
			'nextPageNumber' => $response['nextPageNumber'],
		];
	}

	/*
	 * Response item
	 *
	 * responseState: "SUCCESS"
	 * itemId: 102475
	 * warehouseId: 1989
	 * warehouseCode: "J56"
	 * currency: "EUR"
	 * documentId: "1989_00102475"
	 * resalePrice: "null"
	 * fohQuantity: "87000"
	 * description: "V-Ref Adjustable 2.495V to 36V 100mA Automotive 5-Pin SOT-23 T/R"
	 * partNumber: "TL431IDBVR"
	 * tariffValue: "null"
	 * tariffApplicable: "false"
	 * minOrderQuantity: 3000
	 * multOrderQuantity: 3000
	 * manufacturer: "Texas Instruments"
	 * supplier: "Texas Instruments"
	 * htsCode: "85423990"
	 * pkg: "TAPE/REEL"
	 * spq: 3000
	 * pricingTier: [{minQuantity: "3000", maxQuantity: "1805863905498", resalePrice: "0.06836"}]
	 * urlData: [{type: "Image Small",…}, {type: "Image Large",…}, {type: "Datasheet",…},…]
	 * leadTime: {supplierLeadTime: 6, supplierLeadTimeDate: "03-Mar-2020", arrowLeadTime: 6}
	 *  arwPartNum: {isExactMatch: false, name: "TL431IDBVR"}
	 * suppPartNum: {isExactMatch: false, name: "TL431IDBVR"}
	 * bufferQuantity: 0
	 * euRohs: "Unknown"
	 * chinaRohs: "Compliant"
	 * quotable: false
	 * purchaseable: true
	 * arrowInitiated: false
	 * nonCancelableNonReturnable: false
	 * taxonomy: "Semiconductor - IC > Linear > Voltage Reference"
	 * partClassification: "B"
	 * partBuyCurrency: "USD"
	 * exportControlClassificationNumberUS: "EAR99"
	 * exportControlClassificationNumberWAS: "NLR"
	 * lifeCycleStatus: "Active"
	 */
	private function parseItem(array $item)
	{
		return [
			'sku'                => $item['warehouseCode'],
			'name'               => $item['partNumber'],
			'description'        => $item['description'],
			'partNumber'         => $item['partNumber'],
			'external_id'        => (string) $item['itemId'],

			'photo_ext_src'      => $item['urlData'][1]['value'],
			'quantity'           => (int) $item['minOrderQuantity'],
			'min_order_quantity' => (int) $item['minOrderQuantity'],
			'unit_price'         => (float) $item['pricingTier'][0]['resalePrice'],
			'currency'           => $item['currency'] ?: 'EUR',

			'price_range'        => [],
			'cart_amount'        => 1,
			'cart_amount_max'    => 10,

		];
	}

}
