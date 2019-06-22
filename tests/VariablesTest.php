<?php /** @noinspection PhpUnhandledExceptionInspection */
require "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
class VariablesTest
{
	function testStatementCantBeOverwritten()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use ($utopia)
		{
			$utopia->parseAndExecute('local local "bla";');
		});
	}

	function testConstCantBeOverwritten()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use ($utopia)
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
		Nose::expectException(InvalidCodeException::class, function() use ($utopia, $local_vars)
		{
			$utopia->parseAndExecuteWithWritableLocalVars('final bla;', $local_vars);
		});
		$utopia->parseAndExecuteWithWritableLocalVars('const bla 1;', $local_vars);
	}
}
