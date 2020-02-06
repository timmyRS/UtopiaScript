<?php
$flags = [
	"repl" => false,
	"debug" => false,
	"stopwatch" => false,
	"time-limit" => 0,
	"stdin" => true,
	"php-statement" => false,
	"help" => false
];
function switchArg(string $arg): bool
{
	global $flags;
	switch($arg)
	{
		case "-i":
		case "repl":
		case "--repl":
		case "interactive":
		case "--interactive":
			$flags["repl"] = true;
			break;
		case "-d":
		case "debug":
		case "--debug":
		case "--debug-mode":
			$flags["debug"] = true;
			break;
		case "-t":
		case "--time":
		case "stopwatch":
		case "--stopwatch":
			$flags["stopwatch"] = true;
			break;
		case "-s":
		case "--no-stdin":
			$flags["stdin"] = false;
			break;
		case "-p":
		case "--enable-php-statement":
			$flags["php-statement"] = true;
			break;
		case "?":
		case "-?":
		case "help":
		case "--help":
			$flags["help"] = true;
			break;
		default:
			return false;
	}
	return true;
}

$file = "";
for($i = 1; $i < count($argv); $i++)
{
	$arg = strtolower($argv[$i]);
	if(substr($arg, 0, 1) == "-" && substr($arg, 0, 2) != "--")
	{
		$arr = str_split($arg);
		for($j = 1; $j < count($arr); $j++)
		{
			switchArg("-".$arr[$j]);
		}
	}
	else if(!switchArg($arg))
	{
		if(substr($arg, 0, 13) == "--time-limit=")
		{
			$flags["time-limit"] = floatval(substr($arg, 13));
		}
		else
		{
			$file = $arg;
		}
	}
}
if($flags["help"] || (!$file && !$flags["repl"]))
{
	die("Syntax: utopia [file] [-i|--repl|--interactive] [-d|--debug] [-t|--stopwatch] [--time-limit=<seconds>] [-s|--no-stdin] [-p|--enable-php-statement]\r\nEither --repl or [file] has to be given.\r\n");
}
if(!is_file(__DIR__."/vendor/autoload.php"))
{
	echo "vendor/autoload.php was not found, attempting to generate it...\n";
	passthru("composer install -o -d \"".__DIR__."\" --no-dev");
	if(!is_file(__DIR__."/vendor/autoload.php"))
	{
		die("Welp, that didn't work. Try again as root/administrator.\n");
	}
}
require __DIR__."/vendor/autoload.php";
use Asyncore\stdin;
use UtopiaScript\
{Exception\Exception, Exception\IncompleteCodeException, Utopia};
$utopia = new Utopia($flags["stdin"] ? "stdin" : null);
$utopia->debug = $flags["debug"];
$utopia->maximum_execution_time = $flags["time-limit"];
$utopia->loadExtension(UtopiaScriptDebugExtension\DebugExtension::class);
if($flags["php-statement"])
{
	$utopia->loadExtension(UtopiaScriptPhpStatementExtension\PhpStatementExtension::class);
}
if($flags["repl"])
{
	$code = "";
	$local_vars = [];
	$output = "";
	$utopia->output_handler = $utopia->error_output_handler = function($str) use (&$output)
	{
		if($str === "")
		{
			return;
		}
		if($output === "")
		{
			echo "< ";
		}
		$output .= $str;
		echo $str;
	};
	stdin::init(null, false);
	echo "UtopiaScript REPL (Read-eval-print loop)".($flags["debug"] ? " [Debug Mode]" : "")."\r\n";
	if($file)
	{
		echo "Note: [file] is not compatible REPL.\r\n";
	}
	echo "> ";
	do
	{
		$input = rtrim(stdin::getNextLine(), "\r\n");
		if($code != "")
		{
			$code .= "\r\n";
		}
		$code .= $input;
		try
		{
			$output = "";
			$ret = $utopia->parseAndExecuteWithWritableLocalVars($code, $local_vars);
			if($output !== "" && substr($output, -1) != "\n")
			{
				echo "\r\n";
			}
			echo "= ".Utopia::strval($ret)."\r\n";
			$code = "";
		}
		catch(IncompleteCodeException $e)
		{
			if(in_array(trim($input), [
				'',
				';'
			]))
			{
				$code = "";
				echo get_class($e).": ".$e->getMessage()."\r\n";
			}
			else
			{
				echo '>';
			}
		}
		catch(Exception $e)
		{
			echo get_class($e).': '.$e->getMessage()."\r\n";
			$code = "";
		}
		if($flags["stopwatch"])
		{
			echo "Parsed and executed in ".($utopia->last_execution_time)." seconds.\r\n";
		}
		echo '> ';
	}
	while(true);
}
else
{
	try
	{
		$utopia->parseAndExecute(file_get_contents($file));
		if($flags["stopwatch"])
		{
			echo "\r\nParsed and executed in ".($utopia->last_execution_time)." seconds.\r\n";
		}
	}
	catch(Exception $e)
	{
		echo get_class($e).': '.$e->getMessage()."\r\n";
		if($flags["debug"])
		{
			echo $e->getTraceAsString()."\r\n";
		}
	}
}
