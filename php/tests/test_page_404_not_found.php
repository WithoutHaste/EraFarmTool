<?php

include("../page_404_not_found.php");

use PHPUnit\Framework\TestCase;

class TestPage404NotFound extends TestCase
{
	public function testHttpResponseCode() : void
	{
		self::assertSame(404, http_response_code());
	}
}

?>