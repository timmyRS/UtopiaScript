<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\Utopia;
class ArraysTest
{
	function testCreation()
	{
		$utopia = new Utopia();
		Nose::assertEquals([
			"1",
			2,
			[
				true,
				null
			]
		], Utopia::externalize($utopia->parseAndExecute('array "1" 2 [true null]')));
	}

	function testToString()
	{
		$utopia = new Utopia();
		Nose::assertEquals('array "1" 2 array = (array true null)', Utopia::strval($utopia->parseAndExecute('array "1" 2 array:[true null]')));
	}
}
