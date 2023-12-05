<?php

include("../404_not_found.php");

use PHPUnit\Framework\TestCase;

class Test404NotFound extends TestCase
{
	public function testHttpResponseCode() : void
	{
		self::assertSame(404, http_response_code());
	}
}

?>