<?php /** @noinspection PhpUnhandledExceptionInspection */
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

	function testPower()
	{
		$utopia = new Utopia();
		Nose::assertEquals(256, Utopia::externalize($utopia->parseAndExecute("2 ^ 4 pow 2")));
	}

	function testFactorial()
	{
		$utopia = new Utopia();
		Nose::assertEquals(6, Utopia::externalize($utopia->parseAndExecute("3!")));
		Nose::assertEquals(6, Utopia::externalize($utopia->parseAndExecute("3 factorial")));
	}
}
