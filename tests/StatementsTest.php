<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
use UtopiaScriptPhpStatementExtension\PhpStatementExtension;
class StatementsTest
{
	function testGetType()
	{
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute(<<<EOC
<< get_type bla;
<< get_type print;
<< get_type "Hi";
<< get_type 69;
<< get_type true;
<< get_type null;
<< get_type (*{});
.f*{};
<< get_type f;
EOC
		);
		Nose::assertEquals("undefined\r\nstatement\r\nstring\r\nnumber\r\nboolean\r\nnull\r\nfunction\r\nfunction\r\n", $utopia->last_output);
	}

	function testPrint()
	{
		$utopia = new Utopia(null, "keep");
		foreach([
			"print ",
			"print",
			"<"
		] as $statement)
		{
			$utopia->parseAndExecute($statement.'"Hi";');
			Nose::assertEquals("Hi", $utopia->last_output);
		}
	}

	function testPrintLine()
	{
		$utopia = new Utopia(null, "keep");
		foreach([
			"print_line ",
			"print_line",
			"<<"
		] as $statement)
		{
			$utopia->parseAndExecute($statement.'"Hi";');
			Nose::assertEquals("Hi\r\n", $utopia->last_output);
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
		$utopia = new Utopia(null, "keep");
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc function { return "Return"; print "Unreachable"; }; myFunc; exit "Exit"; print "Unreachable";')));
		Nose::assertEquals('Exit', $utopia->last_output);
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc func { exit "Exit"; print "Unreachable"; }; myFunc; print "Unreachable";')));
		Nose::assertEquals('Exit', $utopia->last_output);
	}
}
