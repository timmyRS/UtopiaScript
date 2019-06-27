<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\Utopia;
class NumbersTest
{
	function testAddition()
	{
		$utopia = new Utopia();
		Nose::assertEquals(10, Utopia::externalize($utopia->parseAndExecute("1 + 2 + 3 + 4")));
	}

	function testSubtraction()
	{
		$utopia = new Utopia();
		Nose::assertEquals(3, Utopia::externalize($utopia->parseAndExecute("9 - 5 - 1")));
	}

	function testMultiplication()
	{
		$utopia = new Utopia();
		Nose::assertEquals(8, Utopia::externalize($utopia->parseAndExecute("2 * 2 * 2")));
	}

	function testDivision()
	{
		$utopia = new Utopia();
		Nose::assertEquals(5, Utopia::externalize($utopia->parseAndExecute("20 / 2 / 2")));
	}

	function testArrayNumberArithmetic()
	{
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute("print_line [2 3 4] * 8; print_line 8 * [2 3 4];");
		Nose::assertEquals($utopia->last_output, "array 16 24 32\r\narray 16 24 32\r\n");
	}

	function testPower()
	{
		$utopia = new Utopia();
		Nose::assertEquals(256, Utopia::externalize($utopia->parseAndExecute("2 ^ 4 pow 2")));
	}

	function testFactorial()
	{
		$utopia = new Utopia();
		foreach(["!", " factorial", " fact"] as $action)
		{
			Nose::assertEquals(6, Utopia::externalize($utopia->parseAndExecute('3'.$action)));
		}
	}
}
