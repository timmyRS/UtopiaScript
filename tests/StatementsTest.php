<?php /** @noinspection PhpUnhandledExceptionInspection */
require "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
use UtopiaScriptPhpStatementExtension\PhpStatementExtension;
class StatementsTest
{
	function testPrint()
	{
		foreach([
			"<",
			"say",
			"out",
			"echo",
			"print",
			"output"
		] as $statement)
		{
			$fh = fopen(".test_tmp_print", "w");
			$utopia = new Utopia(null, $fh);
			$utopia->parseAndExecute($statement.' "Hi";');
			fclose($fh);
			Nose::assertEquals("Hi", file_get_contents(".test_tmp_print"));
			unlink(".test_tmp_print");
		}
	}

	function testPrintln()
	{
		foreach([
			"<<",
			"println"
		] as $statement)
		{
			$fh = fopen(".test_tmp_println", "w");
			$utopia = new Utopia(null, $fh);
			$utopia->parseAndExecute($statement.' "Hi";');
			fclose($fh);
			Nose::assertEquals("Hi\r\n", file_get_contents(".test_tmp_println"));
			unlink(".test_tmp_println");
		}
	}

	function testPhp()
	{
		$utopia = new Utopia();
		Nose::expectException(InvalidCodeException::class, function() use ($utopia)
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
