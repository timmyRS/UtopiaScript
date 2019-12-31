<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia, UtopiaWithKeepOutput};
class ArraysTest
{
	function testCreation()
	{
		$utopia = new Utopia(null, null);
		Nose::assertEquals([
			"1",
			2,
			[
				true,
				null
			]
		], Utopia::externalize($utopia->parseAndExecute('array "1" 2 [true null]')));
	}

	function testChangeKeyValue()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
local data [
    name: "Tim"
    age: 17
];
data age = (data age) + 1;
print (data name) " is " (data age) " years old!";
EOC
		);
		Nose::assertEquals($utopia->last_output, "Tim is 18 years old!");
	}

	function testToString()
	{
		$utopia = new Utopia(null, null);
		Nose::assertEquals('array "1" 2 array = (array true null)', Utopia::strval($utopia->parseAndExecute('array "1" 2 array:[true null]')));
	}

	function testForEach()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
final array a = array 1 2 3;
a for_each item {
	print item " ";
};
print get_type item = "undefined";
EOC
		);
		Nose::assertEquals($utopia->last_output, "1 2 3 true");
	}

	function testAddition()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
print [1, 2] + [3 3];
EOC
		);
		Nose::assertEquals($utopia->last_output, "array 4 5");
	}

	function testSubtraction()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
print [5 10] minus [3 8];
EOC
		);
		Nose::assertEquals($utopia->last_output, "array 2 2");
	}

	function testMultiplication()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
print [2 3 4] * [8 8 8];
EOC
		);
		Nose::assertEquals($utopia->last_output, "array 16 24 32");
	}

	function testDivision()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
print [2 3 4] / [2 2 2];
EOC
		);
		Nose::assertEquals($utopia->last_output, "array 1 1.5 2");
	}

	function testArithmeticSizeMismatch()
	{
		$utopia = new Utopia(null, null);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("[1, 2, 3] + [4, 5]");
		});
	}

	function testRange()
	{
		$utopia = new Utopia(null, null);
		$arr_123 = [
			1,
			2,
			3
		];
		$arr_abc = [
			"A",
			"B",
			"C"
		];
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute('range from 1 to 3')), $arr_123);
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute('array 1 - 3')), $arr_123);
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute('range "A" "C"')), $arr_abc);
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute('["A" - "C"]')), $arr_abc);
	}
}
