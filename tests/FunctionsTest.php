<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
class FunctionsTest
{
	function testExplicitFunctionDeclaration()
	{
		$utopia = new Utopia(null, "suppress");
		$utopia->parseAndExecute('const myFunc function {= "Hi";};');
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
	}

	function testFunctionWithStrictParameters()
	{
		$utopia = new Utopia();
		$utopia->parseAndExecute("const myAdd*number a, number b { return a + b; };");
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("print myAdd 1 true;");
		});
	}

	function testFunctionWithAnytypeParameters()
	{
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute(",myFunc = routine mixed:a b any_type c { [a b c]@v{print_line get_type v}; }; myFunc 'Hello' 1337 null;");
		Nose::assertEquals("string\r\nnumber\r\nnull\r\n", $utopia->last_output);
	}
}
