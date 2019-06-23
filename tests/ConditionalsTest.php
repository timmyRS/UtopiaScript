<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once "vendor/autoload.php";
use UtopiaScript\
{Exception\TimeoutException, Utopia};
class ConditionalsTest
{
	function testWhile()
	{
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute(<<<EOC
local counter 10;
while counter > 0 {
    print counter "... ";
    set counter counter - 1;
};
EOC
		);
		Nose::assertEquals("10... 9... 8... 7... 6... 5... 4... 3... 2... 1... ", $utopia->last_output);
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
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute(<<<EOC
[0 1] for_each item {
	if item == 0 {
		print_line "if";
	} otherwise {
		print_line "else";
	};
};
EOC
		);
		Nose::assertEquals("if\r\nelse\r\n", $utopia->last_output);
	}

	function testWhileOtherwise()
	{
		$utopia = new Utopia(null, "keep");
		$utopia->parseAndExecute(<<<EOC
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
		);
		Nose::assertEquals("otherwise", $utopia->last_output);
	}
}
