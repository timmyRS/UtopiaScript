<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
class VariablesTest
{
	function testScopedDeclarations()
	{
		Nose::assertEquals("HeyLater\r\nHiBye", Utopia::getOutput(<<<EOC
global greeting "Hi";
local farewell "Bye";
{
    local greeting "Hey";
    local farewell "Later";
    print_line greeting farewell;
};
print greeting farewell;
EOC
		));
	}

	function testStatementCantBeOverwritten()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute('local local "bla";');
		});
	}

	function testConstCantBeOverwritten()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute('local NL 1;');
		});
	}

	function testFinal()
	{
		$utopia = new Utopia();
		$local_vars = [];
		$utopia->parseAndExecuteWithWritableLocalVars('local bla "bla";', $local_vars);
		$utopia->parseAndExecuteWithWritableLocalVars('final bla [true];', $local_vars);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia, $local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars('final bla;', $local_vars);
		});
		$utopia->parseAndExecuteWithWritableLocalVars('const bla 1;', $local_vars);
	}
}
