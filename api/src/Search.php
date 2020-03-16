<?php

namespace PartsSearch;

use Dotenv\Dotenv;
use PartsSearch\Helpers\Response;
use PartsSearch\Interfaces\ShouldRespond;

class Search {

	/** @var ShouldRespond */
	public $driver;

	/**
	 * Search constructor.
	 *
	 * @return ShouldRespond
	 */
	public function __construct()
	{
		$this->env();
		$driver = sprintf( "\\PartsSearch\\Modules\\%s\\Search", $_REQUEST['driver'] );
		if(! class_exists($driver)) {
			Response::error('Invalid driver');
		}
		$this->driver = new $driver();
		return $this->driver;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->driver->getResponse();
	}

	/**
	 * Init .env variables
	 */
	protected function env()
	{
		try {
			Dotenv::createImmutable(API_DIR )->load();
		}
		catch (\Exception $e) {
			Response::error( $e->getMessage() );
		}
	}

}
