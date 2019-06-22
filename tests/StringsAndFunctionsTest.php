<?php /** @noinspection PhpUnhandledExceptionInspection */
use UtopiaScript\
{Exception\IncompleteCodeException, Utopia};
class StringsAndFunctionsTest
{
	function testBracketStrings()
	{
		$utopia = new Utopia();
		ob_start();
		Nose::assertEquals("< {Utopia\nScript\n};", Utopia::externalize($utopia->parseAndExecute("= {< {Utopia\nScript\n};};")));
		ob_end_clean();
		Nose::expectException(IncompleteCodeException::class, function() use($utopia)
		{
			$utopia->parseAndExecute("< { There is an unfinished { bracket. }");
		});
	}

	function testConcatenation()
	{
		$utopia = new Utopia();
		ob_start();
		Nose::assertEquals("A\nB\nC", Utopia::externalize($utopia->parseAndExecute("= \"A\n\" 'B\n'`C`;")));
		ob_end_clean();
	}

	function testParenthesesEvaluation()
	{
		$utopia = new Utopia();
		ob_start();
		$utopia->parseAndExecute('const myFunc {= "Hi";};');
		Nose::assertEquals('= "Hi";', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= (myFunc);')));
		ob_end_clean();
	}

	function testExplicitFunctionDeclaration()
	{
		$utopia = new Utopia();
		ob_start();
		$utopia->parseAndExecute('const myFunc void {= "Hi";};');
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
		ob_end_clean();
	}

	// TODO: Create test for functions with arguments

	function testToUpperCase()
	{
		$utopia = new Utopia();
		ob_start();
		foreach(["to_upper_case", "^"] as $action)
		{
			Nose::assertEquals('HI', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
		ob_end_clean();
	}

	function testToLowerCase()
	{
		$utopia = new Utopia();
		ob_start();
		foreach(["to_lower_case", "v"] as $action)
		{
			Nose::assertEquals('hi', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
		ob_end_clean();
	}
}
