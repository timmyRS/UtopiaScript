<?php /** @noinspection PhpUnhandledExceptionInspection */
use UtopiaScript\
{Exception\IncompleteCodeException, Utopia};
class StringsAndFunctionsTest
{
	function testBracketStrings()
	{
		$utopia = new Utopia();
		Nose::assertEquals("< {Utopia\nScript\n};", Utopia::externalize($utopia->parseAndExecute("= {< {Utopia\nScript\n};};")));
		Nose::expectException(IncompleteCodeException::class, function() use($utopia)
		{
			$utopia->parseAndExecute("< { There is an unfinished { bracket. }");
		});
	}

	function testConcatenation()
	{
		$utopia = new Utopia();
		Nose::assertEquals("A\nB\nC", Utopia::externalize($utopia->parseAndExecute("= \"A\n\" 'B\n'`C`;")));
	}

	function testParenthesesEvaluation()
	{
		$utopia = new Utopia();
		$utopia->parseAndExecute('const myFunc {= "Hi";};');
		Nose::assertEquals('= "Hi";', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= (myFunc);')));
	}

	function testExplicitFunctionDeclaration()
	{
		$utopia = new Utopia();
		$utopia->parseAndExecute('const myFunc void {= "Hi";};');
		Nose::assertEquals('Hi', Utopia::externalize($utopia->parseAndExecute('= myFunc;')));
	}

	function testToUpperCase()
	{
		$utopia = new Utopia();
		foreach(["^", "upper", "uppercase", "toupper", "touppercase", "to_uppercase"] as $action)
		{
			Nose::assertEquals('HI', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
	}

	function testToLowerCase()
	{
		$utopia = new Utopia();
		foreach(["v", "lower", "lowercase", "tolower", "tolowercase", "to_lowercase"] as $action)
		{
			Nose::assertEquals('hi', Utopia::externalize($utopia->parseAndExecute("='Hi'{$action};")));
		}
	}
}
