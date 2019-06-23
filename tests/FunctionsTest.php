<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
class FunctionsTest
{
	function testExplicitFunctionDeclaration()
	{
		$utopia = new Utopia();
		ob_start();
		$utopia->parseAndExecute('const myFunc function {= "Hi";};');
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
		ob_end_clean();
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
		Nose::assertEquals("string\r\nnumber\r\nnull\r\n", Utopia::getOutput(",myFunc = routine mixed:a b any_type c { [a b c]@v{print_line get_type v}; }; myFunc 'Hello' 1337 null;"));
	}
}