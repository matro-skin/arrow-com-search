<?php

namespace PartsSearch\Modules\Element14;

use PartsSearch\Interfaces\ShouldRespond;
use PartsSearch\Helpers\Request;
use PartsSearch\Helpers\Response;

class Search implements ShouldRespond
{

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

		\PartsSearch\Search::log('Prepare request');

		$request = new Request($this->url);
		$request->setQuery($this->getQuery());

		\PartsSearch\Search::log('Requesting...');

		$response = $request->getResponse();

		\PartsSearch\Search::log('Request ready');

		Response::success(self::collection(json_decode($response, true)));
	}

	/**
	 * Get request's query
	 *
	 * @return array
	 */
	public function getQuery()
	{
		return [
			'storeInfo.id'                        => 'ru.farnell.com',
			'resultsSettings.offset'              => $this->offset,
			'resultsSettings.numberOfResults'     => 10,
			'resultsSettings.refinements.filters' => 'inStock',
			'resultsSettings.responseGroup'       => 'medium',
			'callInfo.omitXmlSchema'              => 'false',
			'callInfo.responseDataFormat'         => 'json',
			'callinfo.apiKey'                     => getenv('ELEMENT14_API_KEY'),
			'term'                                => 'any:' . $this->term
		];
	}

	/**
	 * Set request's query
	 *
	 * @return void
	 */
	public function setQuery()
	{
		$this->term   = trim($_REQUEST[ 'search' ]);
		$this->offset = (int) $_REQUEST[ 'offset' ];
	}

	public function collection( array $response )
	{
		$data = [];
		foreach ($response[ 'keywordSearchReturn' ][ 'products' ] as $item) {
			$resource               = $this->parseItem($item);
			$resource[ 'loop_key' ] = \PartsSearch\Search::loop_key($resource);
			$data[]                 = $resource;
		}

		return [
			'meta' => $this->meta($response[ 'keywordSearchReturn' ]),
			'data' => $data,
		];
	}

	private function meta( array $response )
	{
		return [
			'results'        => $response[ 'numberOfResults' ],
			'pages'          => 1,
			'totalRecords'   => $response[ 'numberOfResults' ],
			'currentPage'    => 1,
			'nextPageNumber' => 1,
		];
	}

	/*
	 * Response item
	 *
	 * sku: "3124622"
	 * displayName: "TEXAS INSTRUMENTS - TL431ID - Источник опорного напряжения, прецизионный, шунтирующий - регулируемый, серия TL431, 2.495В до 36В"
	 * productStatus: "STOCKED"
	 * packSize: 1
	 * unitOfMeasure: "ШТУКА"
	 * id: "pf_NA_3124622_0"
	 * prices: [{to: 49, from: 5, cost: 0.266}, {to: 99, from: 50, cost: 0.245}, {to: 249, from: 100, cost: 0.156},…]
	 * vendorName: "TEXAS INSTRUMENTS"
	 * brandName: "TEXAS INSTRUMENTS"
	 * translatedManufacturerPartNumber: "TL431ID"
	 * translatedMinimumOrderQuality: 5
	 * stock: {level: 3955, leastLeadTime: 43, status: 1, shipsFromMultipleWarehouses: false,…}
	 * comingSoon: false
	 * inventoryCode: 5
	 * nationalClassCode: null
	 * publishingModule: null
	 * vatHandlingCode: "SLST"
	 * releaseStatusCode: 4
	 * isSpecialOrder: false
	 * isAwaitingRelease: false
	 * reeling: false
	 * discountReason: 30
	 */
	private function parseItem( array $item )
	{
		return [

			'sku'         => $item[ 'sku' ] ?? null,
			'name'        => $item[ 'translatedManufacturerPartNumber' ] ?? null,
			'description' => $item[ 'displayName' ] ?? null,
			'partNumber'  => $item[ 'translatedManufacturerPartNumber' ] ?? null,
			'external_id' => (string) $item[ 'id' ] ?? null,

			'photo_ext_src'      => null,
			'quantity'           => (int) $item[ 'packSize' ] ?? null,
			'min_order_quantity' => (int) $item[ 'translatedMinimumOrderQuality' ] ?? null,
			'unit_price'         => (float) isset($item[ 'prices' ][ 0 ]) ? $item[ 'prices' ][ 0 ][ 'cost' ] : null,
			'currency'           => $item[ 'currency' ] ?? 'EUR',

			'price_range'     => array_map(function ( $range ) {
				return [
					'from'       => (int) $range[ 'from' ],
					'to'         => (int) $range[ 'to' ],
					'unit_price' => (float) $range[ 'cost' ],
				];
			}, $item[ 'prices' ] ?? []),
			'cart_amount'     => 1,
			'cart_amount_max' => 10,

		];
	}

}
