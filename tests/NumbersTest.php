<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\Utopia;
use UtopiaScript\UtopiaWithKeepOutput;
class NumbersTest
{
	function testAddition()
	{
		$utopia = new Utopia();
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("1 + 2 + 0.5")), 3.5);
	}

	function testSubtraction()
	{
		$utopia = new Utopia();
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("9 - 5 - 1")), 3);
	}

	function testMultiplication()
	{
		$utopia = new Utopia();
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("2 * 2 * 2")), 8);
	}

	function testDivision()
	{
		$utopia = new Utopia();
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("20 / 2 / 2")), 5);
	}

	function testArrayNumberArithmetic()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute("print_line [16 32] / 8; print_line 16 / [8 4];");
		Nose::assertEquals($utopia->last_output, "array 2 4\r\narray 2 4\r\n");
	}

	function testPower()
	{
		$utopia = new Utopia();
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("2 ^ 4 pow 2")), 256);
	}

	function testFactorial()
	{
		$utopia = new Utopia();
		foreach([
			"!",
			" factorial",
			" fact"
		] as $action)
		{
			Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute('3'.$action)), 6);
		}
	}

	function testComparisons()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute("<<1≤1;<1≥1;");
		Nose::assertEquals($utopia->last_output, "true\r\ntrue");
	}
}
