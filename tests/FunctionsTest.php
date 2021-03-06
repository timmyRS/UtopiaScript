<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia, UtopiaWithKeepOutput};
class FunctionsTest
{
	function testExplicitFunctionDeclaration()
	{
		$utopia = new Utopia(null, null);
		$utopia->parseAndExecute('const myFunc function {= "Hi";};');
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
	}

	function testFunctionWithStrictParameters()
	{
		$utopia = new Utopia(null, null);
		$utopia->parseAndExecute("const myAdd*number a, number b { return a + b; };");
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("print myAdd 1 true;");
		});
	}

	function testFunctionWithAnytypeParameters()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(",myFunc = routine mixed:a b any_type c { [a b c]@v{print_line get_type v}; }; myFunc 'Hello' 1337 null; print get_type a = 'undefined';");
		Nose::assertEquals("string\r\nnumber\r\nnull\r\ntrue", $utopia->last_output);
	}
}
