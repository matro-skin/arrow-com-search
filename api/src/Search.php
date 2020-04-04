<?php

namespace PartsSearch;

use Dotenv\Dotenv;
use PartsSearch\Helpers\Response;
use PartsSearch\Interfaces\ShouldRespond;

class Search
{

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
		self::log('Start Search');
		$driver = sprintf("\\PartsSearch\\Modules\\%s\\Search", $_REQUEST[ 'driver' ]);
		if (! class_exists($driver)) {
			Response::error('Invalid driver', 400);
		}
		$this->driver = new $driver();

		return $this->driver;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		self::log('Start driver response');

		return $this->driver->getResponse();
	}

	/**
	 * Init .env variables
	 */
	protected function env()
	{
		try {
			Dotenv::createImmutable(ENV_PATH)->load();
		} catch (\Exception $e) {
			Response::error($e->getMessage());
		}
	}

	public static function loop_key( array $resource )
	{
		return implode('_', [ $resource[ 'sku' ], $resource[ 'partNumber' ], $resource[ 'external_id' ] ]);
	}

	/**
	 * @param  string  $data
	 *
	 * @return void
	 */
	public static function log( $message )
	{
		if (getenv('DEBUG') !== 'true') {
			return;
		}

		ob_start();
		debug_print_backtrace();
		$content = ob_get_contents();
		ob_end_clean();

		$log    = [
			date('Y-m-d H:i:s') . ' ' . $message,
			json_encode($_REQUEST),
			$content
		];
		$string = implode(PHP_EOL, $log) . PHP_EOL;
		file_put_contents(ENV_PATH . '/debug.log', $string, FILE_APPEND);
	}

}
