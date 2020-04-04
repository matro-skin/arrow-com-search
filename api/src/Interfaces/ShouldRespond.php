<?php

namespace PartsSearch\Interfaces;

interface ShouldRespond
{

	public function getResponse();

	public function getQuery();

	public function setQuery();

	public function collection( array $response );

}
