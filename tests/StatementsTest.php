<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\InvalidCodeException, Utopia, UtopiaWithKeepOutput};
use UtopiaScriptPhpStatementExtension\PhpStatementExtension;
class StatementsTest
{
	function testGetType()
	{
		$utopia = new UtopiaWithKeepOutput();
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
		$utopia = new UtopiaWithKeepOutput();
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
		$utopia = new UtopiaWithKeepOutput();
		foreach([
			"print_line ",
			"print_line",
			"<<",
			"≪"
		] as $statement)
		{
			$utopia->parseAndExecute($statement.'"Hi";');
			Nose::assertEquals($utopia->last_output, "Hi\r\n");
		}
	}

	function testPrintError()
	{
		$utopia = new UtopiaWithKeepOutput();
		foreach([
			"print_error ",
			"print_error",
			"<!",
			"≤"
		] as $statement)
		{
			$utopia->parseAndExecute($statement.'"Hi";');
			Nose::assertEquals($utopia->last_output, "");
			Nose::assertEquals($utopia->last_error_output, "Hi");
		}
	}

	function testPrintErrorLine()
	{
		$utopia = new UtopiaWithKeepOutput();
		foreach([
			"print_error_line ",
			"print_error_line",
			"<<!",
			"<!<",
			"≪!",
			"≤<"
		] as $statement)
		{
			$utopia->parseAndExecute($statement.'"Hi";');
			Nose::assertEquals($utopia->last_output, "");
			Nose::assertEquals($utopia->last_error_output, "Hi\r\n");
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
		$utopia = new UtopiaWithKeepOutput();
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc function { return "Return"; print "Unreachable"; }; myFunc; exit "Exit"; print "Unreachable";')));
		Nose::assertEquals('Exit', $utopia->last_output);
		Nose::assertEquals('Exit', Utopia::externalize($utopia->parseAndExecute('local myFunc func { exit "Exit"; print "Unreachable"; }; myFunc; print "Unreachable";')));
		Nose::assertEquals('Exit', $utopia->last_output);
	}
}
