<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
use UtopiaScriptPhpStatementExtension\PhpStatementExtension;
class StatementsTest
{
	function testPrint()
	{
		foreach([
			"print ",
			"print",
			"<"
		] as $statement)
		{
			Nose::assertEquals("Hi", Utopia::getOutput($statement.'"Hi";'));
		}
	}

	function testPrintln()
	{
		foreach([
			"print_line ",
			"print_line",
			"<<"
		] as $statement)
		{
			Nose::assertEquals("Hi\r\n", Utopia::getOutput($statement.'"Hi";'));
		}
	}

	function testPhp()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("php { return \"H\".'i'; }");
		});
		$utopia->loadExtension(PhpStatementExtension::class);
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("php { return \"H\".'i'; }")), "Hi");
	}

	function testReturnAndExit()
	{
		$utopia = new Utopia();
		ob_start(function($buffer)
		{
			Nose::assertEquals('ExitExit', $buffer);
		});
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc function { return "Return"; print "Unreachable"; }; myFunc; exit "Exit"; print "Unreachable";')));
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc function { exit "Exit"; print "Unreachable"; }; myFunc; print "Unreachable";')));
		ob_end_clean();
	}
}
