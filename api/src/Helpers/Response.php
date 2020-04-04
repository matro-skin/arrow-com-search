<?php

namespace PartsSearch\Helpers;

class Response
{

	/**
	 * @param  array|object  $data
	 * @param  int  $code
	 */
	public static function success( $data )
	{
		if (gettype($data) === 'string') {
			$data = json_decode($data, true);
		}
		$data[ 'status' ] = 'success';
		self::output($data);
	}

	/**
	 * @param  string  $message
	 * @param  int  $code
	 */
	public static function error( $message, $code = 400 )
	{
		http_response_code($code);
		self::output([
			'error' => $message,
			'code'  => $code,
		]);
	}

	private static function output( array $data )
	{
		echo json_encode($data);
		die();
	}

}
