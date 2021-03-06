<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia, UtopiaWithKeepOutput};
class VariablesTest
{
	function testScopedDeclarations()
	{
		$utopia = new UtopiaWithKeepOutput();
		$utopia->parseAndExecute(<<<EOC
global greeting "Hi";
local farewell "Bye";
{
    local greeting "Hey";
    local farewell "Later";
    print_line greeting farewell;
};
print greeting farewell;
EOC
		);
		Nose::assertEquals("HeyLater\r\nHiBye", $utopia->last_output);
	}

	function testStatementCantBeOverwritten()
	{
		$utopia = new Utopia(null, null);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute('local local "bla";');
		});
	}

	function testConstCantBeOverwritten()
	{
		$utopia = new Utopia(null, null);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute('local NL 1;');
		});
	}

	function testFinal()
	{
		$utopia = new Utopia(null, null);
		$local_vars = [];
		$utopia->parseAndExecuteWithWritableLocalVars('local bla "bla";', $local_vars);
		$utopia->parseAndExecuteWithWritableLocalVars('final bla [true];', $local_vars);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia, &$local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars('final bla;', $local_vars);
		});
		$utopia->parseAndExecuteWithWritableLocalVars('const bla 1;', $local_vars);
	}

	function testTypeContracts()
	{
		$utopia = new Utopia(null, null);
		$local_vars = [];
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia, &$local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars("local string a;", $local_vars);
		});
		$utopia->parseAndExecuteWithWritableLocalVars("local string a 'Hi';", $local_vars);
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia, &$local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars("local number a 1337;", $local_vars);
		});
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia, &$local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars("set a 1337;", $local_vars);
		});
	}
}
