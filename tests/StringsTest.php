<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\IncompleteCodeException, Utopia};
class StringsTest
{
	function testBracketStrings()
	{
		$utopia = new Utopia(null, null);
		Nose::assertEquals("< {Utopia\nScript\n};", Utopia::externalize($utopia->parseAndExecute("= {< {Utopia\nScript\n};};")));
		Nose::expectException(IncompleteCodeException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("< { There is an unfinished { bracket. }");
		});
	}

	function testConcatenation()
	{
		$utopia = new Utopia(null, null);
		Nose::assertEquals("A\nB\nC", Utopia::externalize($utopia->parseAndExecute("= \"A\n\" 'B\n'`C`;")));
	}

	function testFunctionConcatenation()
	{
		$utopia = new Utopia(null, null);
		Nose::assertEquals(substr(Utopia::externalize($utopia->parseAndExecute("!pi*{=m_pi};='I ate some pi and it tasted like 'pi")), 0, 37), "I ate some pi and it tasted like 3.14");
		Nose::assertEquals(Utopia::externalize($utopia->parseAndExecute("!greet str:name{='hi 'name};='oh 'greet'mark'")), "oh hi mark");
	}

	function testParenthesesEvaluation()
	{
		$utopia = new Utopia(null, null);
		$utopia->parseAndExecute('const myFunc {= "Hi";};');
		Nose::assertEquals('= "Hi";', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= (myFunc);')));
	}

	function testToUpperCase()
	{
		$utopia = new Utopia(null, null);
		foreach([
			"to_upper_case",
			"^"
		] as $action)
		{
			Nose::assertEquals('HI', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
	}

	function testToLowerCase()
	{
		$utopia = new Utopia(null, null);
		foreach([
			"to_lower_case",
			"v"
		] as $action)
		{
			Nose::assertEquals('hi', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
	}
}
