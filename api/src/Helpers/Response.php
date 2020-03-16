<?php

namespace PartsSearch\Helpers;

class Response {

	/**
	 * @param array|object $data
	 * @param int $code
	 */
	public static function success($data)
	{
		if( gettype($data) === 'array') {
			self::output($data);
		}
		self::output( json_decode($data,true));
	}

	/**
	 * @param string $message
	 * @param int $code
	 */
	public static function error(string $message, int $code = 400)
	{
		self::output([
			'error' => $message,
			'code' => $code,
		]);
	}

	private static function output(array $data)
	{
		echo json_encode($data);
		die();
	}

}
