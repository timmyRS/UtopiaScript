<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\TimeoutException, Utopia};
class ConditionalsTest
{
	function testWhile()
	{
		Nose::assertEquals("10... 9... 8... 7... 6... 5... 4... 3... 2... 1... ", Utopia::getOutput(<<<EOC
local counter 10;
while counter > 0 {
    print counter "... ";
    set counter counter - 1;
};
EOC
		));
	}

	function testTimeLimit()
	{
		$utopia = new Utopia();
		$utopia->maximum_execution_time = 0.05;
		Nose::expectException(TimeoutException::class, function() use (&$utopia)
		{
			$utopia->parseAndExecute("while true { }");
		});
	}

	function testIfAndElse()
	{
		Nose::assertEquals("if\r\nelse\r\n", Utopia::getOutput(<<<EOC
[0 1] for_each item {
	if item == 0 {
		print_line "if";
	} otherwise {
		print_line "else";
	};
};
EOC
		));
	}

	function testWhileOtherwise()
	{
		Nose::assertEquals("otherwise", Utopia::getOutput(<<<EOC
local var = 0;
while var >= 1
{
	print "while";
}
otherwise
{
	print "otherwise"
}
EOC
		));
	}
}
