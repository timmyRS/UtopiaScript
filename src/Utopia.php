<?php /** @noinspection CheckEmptyScriptTag HtmlUnknownTag */
namespace UtopiaScript;
use hellsh\pai;
use InvalidArgumentException;
use UtopiaScript\Exception\
{IncompleteCodeException, InvalidCodeException, InvalidEnvironmentException, TimeoutException};
use UtopiaScript\Statement\
{Conditional\IfNotStatement, Conditional\IfStatement, Conditional\WhileNotStatement, Conditional\WhileStatement, Declaration\ConstStatement, Declaration\FinalStatement, Declaration\GlobalStatement, Declaration\LocalStatement, Declaration\SetStatement, Declaration\UnsetStatement, ExitStatement, GetTypeStatement, ReturnStatement, Statement, Stdio\PrintErrorLineStatement, Stdio\PrintErrorStatement, Stdio\PrintLineStatement, Stdio\PrintStatement, Stdio\ReadStatement, Time\MicroTimeStatement, Time\MilliTimeStatement, Time\TimeStatement, Variable\Action\CeilStatement, Variable\Action\FloorStatement, Variable\Action\RoundStatement, Variable\ArrayDeclarationStatement, Variable\ArrayStatement, Variable\BooleanStatement, Variable\FunctionDeclarationStatement, Variable\NullStatement, Variable\NumberStatement, Variable\RangeStatement, Variable\StringStatement, Variable\VariableStatement};
/** An environment with global variables that can execute UtopiaScript code. */
class Utopia
{
	/**
	 * @var resource $input_stream
	 */
	public $input_stream;
	/**
	 * @var callable|null $output_handler
	 */
	public $output_handler;
	/**
	 * @var callable|null $error_output_handler
	 */
	public $error_output_handler;
	/**
	 * Called when a new script is being executed.
	 *
	 * @var callable|null $reset_handler
	 */
	public $reset_handler;
	/**
	 * If true, parsing will print debug information but take considerably longer.
	 *
	 * @var boolean $debug
	 */
	public $debug = false;
	/**
	 * @var Variable[] $vars
	 */
	public $vars;
	/**
	 * @var array $statements
	 */
	public $statements;
	/**
	 * The total seconds it took to perform reads on STDIN.
	 *
	 * @var float $input_time
	 */
	public $input_time = 0;
	/**
	 * The total seconds it took to perform the last script, excluding STDIN reads.
	 *
	 * @var float $last_execution_time
	 */
	public $last_execution_time = 0;
	/**
	 * The maximum amount of seconds a script may take to execute, excluding STDIN reads.
	 * Use 0 to disable the execution time restriction.
	 *
	 * @var float $maximum_execution_time
	 */
	public $maximum_execution_time = 0;
	protected $execute_start = null;

	/**
	 * @param callable|resource|string|null $input_stream
	 * @param callable|resource|string|null $output_handler
	 * @param callable|resource|string|null $error_output_handler
	 */
	function __construct($input_stream = "stdin", $output_handler = "echo", $error_output_handler = "stderr")
	{
		if($input_stream == "stdin")
		{
			if(!pai::isInitialized())
			{
				pai::init();
			}
			$this->input_stream = function()
			{
				return pai::awaitLine();
			};
		}
		else if(is_resource($input_stream))
		{
			$this->input_stream = function() use (&$input_stream)
			{
				return fgets($input_stream);
			};
		}
		else
		{
			$this->input_stream = $input_stream;
		}
		if($output_handler === "echo")
		{
			$this->output_handler = function($str)
			{
				echo $str;
			};
		}
		else if(is_resource($output_handler))
		{
			$this->output_handler = function() use (&$output_handler)
			{
				return fgets($output_handler);
			};
		}
		else
		{
			$this->output_handler = $output_handler;
		}
		if($error_output_handler === "stderr")
		{
			$stderr = fopen("php://stderr", "w");
			$this->error_output_handler = function($str) use (&$stderr)
			{
				fwrite($stderr, $str);
				fflush($stderr);
			};
		}
		else if(is_resource($error_output_handler))
		{
			$this->error_output_handler = function() use (&$error_output_handler)
			{
				return fgets($error_output_handler);
			};
		}
		else
		{
			$this->error_output_handler = $error_output_handler;
		}
		try
		{
			$this->vars = [
				'true' => new Variable(new BooleanStatement(true), true),
				'yes' => new Variable(new BooleanStatement(true), true),
				'on' => new Variable(new BooleanStatement(true), true),
				'false' => new Variable(new BooleanStatement(false), true),
				'off' => new Variable(new BooleanStatement(false), true),
				'no' => new Variable(new BooleanStatement(false), true),
				'null' => new Variable(new NullStatement(), true),
				'void' => new Variable(new NullStatement(), true),
				'none' => new Variable(new NullStatement(), true),
				'nil' => new Variable(new NullStatement(), true),
				'cr' => new Variable(new StringStatement("\r"), true),
				'lf' => new Variable(new StringStatement("\n"), true),
				'nl' => new Variable(new StringStatement("\n"), true),
				'crlf' => new Variable(new StringStatement("\r\n"), true),
				'crnl' => new Variable(new StringStatement("\r\n"), true),
				'eol' => new Variable(new StringStatement(PHP_EOL), true),
				'tab' => new Variable(new StringStatement("\t"), true),
				'm_pi' => new Variable(new NumberStatement(M_PI), true),
				'm_e' => new Variable(new NumberStatement(M_E), true)
			];
		}
		catch(InvalidCodeException $e)
		{
		}
		$this->statements = [// Conditional
			'if' => IfStatement::class,
			'ifnt' => IfNotStatement::class,
			'ifnot' => IfNotStatement::class,
			'if_not' => IfNotStatement::class,
			'unless' => IfNotStatement::class,
			'while' => WhileStatement::class,
			'whilent' => WhileNotStatement::class,
			'whilenot' => WhileNotStatement::class,
			'while_not' => WhileNotStatement::class,
			'whileunless' => WhileNotStatement::class,
			'while_unless' => WhileNotStatement::class,
			// Declaration
			'local' => LocalStatement::class,
			'final' => FinalStatement::class,
			'global' => GlobalStatement::class,
			'const' => ConstStatement::class,
			'constant' => ConstStatement::class,
			'set' => SetStatement::class,
			'unset' => UnsetStatement::class,
			'dispose_of' => UnsetStatement::class,
			'dispose' => UnsetStatement::class,
			// Standard
			'exit' => ExitStatement::class,
			'end' => ExitStatement::class,
			'die' => ExitStatement::class,
			'return' => ReturnStatement::class,
			'get_type' => GetTypeStatement::class,
			'gettype' => GetTypeStatement::class,
			'type_of' => GetTypeStatement::class,
			'typeof' => GetTypeStatement::class,
			// Stdio
			'print' => PrintStatement::class,
			'say' => PrintStatement::class,
			'echo' => PrintStatement::class,
			'write' => PrintStatement::class,
			'output' => PrintStatement::class,
			'print_line' => PrintLineStatement::class,
			'println' => PrintLineStatement::class,
			'print_error' => PrintErrorStatement::class,
			'printerr' => PrintErrorStatement::class,
			'print_error_line' => PrintErrorLineStatement::class,
			'printerrln' => PrintErrorLineStatement::class,
			'read' => ReadStatement::class,
			// Time
			'time' => TimeStatement::class,
			'militime' => MilliTimeStatement::class,
			'mili_time' => MilliTimeStatement::class,
			'millitime' => MilliTimeStatement::class,
			'milli_time' => MilliTimeStatement::class,
			'microtime' => MicroTimeStatement::class,
			'micro_time' => MicroTimeStatement::class,
			// Variables
			'arr' => ArrayDeclarationStatement::class,
			'array' => ArrayDeclarationStatement::class,
			'range' => RangeStatement::class,
			'func' => FunctionDeclarationStatement::class,
			'function' => FunctionDeclarationStatement::class,
			'routine' => FunctionDeclarationStatement::class,
			// Actions
			'floor' => FloorStatement::class,
			'round' => RoundStatement::class,
			'ceil' => CeilStatement::class
		];
	}

	static function isStatementExecutionSafe(Statement $statement): bool
	{
		return !$statement instanceof StringStatement || !$statement->exec;
	}

	/**
	 * @param mixed $value
	 * @return VariableStatement
	 */
	static function internalize($value): VariableStatement
	{
		$value = self::unwrap($value);
		if($value === null)
		{
			return new NullStatement();
		}
		else
		{
			if(is_bool($value))
			{
				return new BooleanStatement($value);
			}
			else
			{
				if(self::is_numeric($value))
				{
					return new NumberStatement(self::numval($value));
				}
				else
				{
					if(gettype($value) == "string")
					{
						return new StringStatement($value);
					}
				}
			}
		}
		return $value;
	}

	static function unwrap($value, bool $preserve_exit = false)
	{
		return ($preserve_exit ? $value instanceof ReturnStatement : $value instanceof ExitStatement) ? self::unwrap($value->value) : $value;
	}

	static function is_numeric($value): bool
	{
		return $value instanceof NumberStatement || is_numeric($value) || (gettype($value) == "string" && preg_match('/^0(x[0-9A-F]+|b[01]+)$/', $value) === 1);
	}

	static function numval($value)
	{
		if($value instanceof NumberStatement)
		{
			$value = $value->value;
		}
		if(strpos($value, ".") !== false)
		{
			return floatval($value);
		}
		switch(substr($value, 0, 2))
		{
			case "0x":
				return intval(substr($value, 2), 16);
			case "0b":
				return intval(substr($value, 2), 2);
		}
		return intval($value);
	}

	/**
	 * @param string $code
	 * @param array $local_vars
	 * @return Statement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function parseAndExecute($code, array $local_vars = [])
	{
		return $this->parseAndExecuteWithWritableLocalVars($code, $local_vars);
	}

	/**
	 * @param string $code
	 * @param array $local_vars
	 * @return Statement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function parseAndExecuteWithWritableLocalVars($code, array &$local_vars)
	{
		$root_script = ($this->execute_start === null);
		try
		{
			if($root_script)
			{
				$this->execute_start = microtime(true);
				$this->input_time = 0;
				if($this->reset_handler)
				{
					($this->reset_handler)();
				}
			}
			else if($this->maximum_execution_time > 0 && $this->maximum_execution_time < microtime(true) - $this->execute_start - $this->input_time)
			{
				throw new TimeoutException("Script took more than ".$this->maximum_execution_time." second".($this->maximum_execution_time == 1 ? "" : "s")." to finish");
			}
			$chars = preg_split('//u', $code, null, PREG_SPLIT_NO_EMPTY);
			$end_i = count($chars);
			$ret = null;
			$literal = '';
			$statement = null;
			if($this->debug)
			{
				$this->say("<block>");
			}
			for($i = 0; $i < $end_i; $i++)
			{
				if($this->debug)
				{
					$this->say($chars[$i]);
				}
				switch($chars[$i])
				{
					case '#':
						$this->processComment($chars, $i, $end_i);
						break;
					case '<':
						if($statement === null && $literal == '')
						{
							$statement = new PrintStatement();
						}
						else if($literal == '' && $statement instanceof PrintLineStatement && !$statement->isExecutable())
						{
							throw new InvalidCodeException("Unexpected <");
						}
						else if($literal == '' && $statement instanceof PrintErrorStatement && !$statement->isExecutable())
						{
							$statement = new PrintErrorLineStatement();
						}
						else if($literal == '' && $statement instanceof PrintStatement && !$statement->isExecutable())
						{
							$statement = new PrintLineStatement();
						}
						else
						{
							$this->specialCharacter('<', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '≤':
						if($statement === null && $literal == '')
						{
							$statement = new PrintErrorStatement();
						}
						else
						{
							$this->specialCharacter('≤', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '≪':
						if($statement === null && $literal == '')
						{
							$statement = new PrintLineStatement();
						}
						else
						{
							$this->specialCharacter('≪', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '>':
						if($statement === null && $literal == '')
						{
							$statement = new ReadStatement();
						}
						else
						{
							$this->specialCharacter('>', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '=':
						if($statement === null && $literal == '')
						{
							$statement = new ReturnStatement();
						}
						else
						{
							$this->specialCharacter('=', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '.':
						if($statement === null && $literal == '')
						{
							$statement = new LocalStatement();
						}
						else
						{
							$literal .= '.';
						}
						break;
					case ':':
						if($statement === null && $literal == '')
						{
							$statement = new FinalStatement();
						}
						else
						{
							$this->specialCharacter(':', $literal, $statement, $local_vars, $ret);
						}
						break;
					case ',':
						if($statement === null && $literal == '')
						{
							$statement = new GlobalStatement();
						}
						else
						{
							$this->specialCharacter(',', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '!':
						if($statement === null && $literal == '')
						{
							$statement = new ConstStatement();
						}
						else if($literal == '' && $statement instanceof IfStatement)
						{
							$statement = new IfNotStatement();
						}
						else if($literal == '' && $statement instanceof WhileStatement)
						{
							$statement = new WhileNotStatement();
						}
						else if($literal == '' && $statement instanceof PrintLineStatement)
						{
							$statement = new PrintErrorLineStatement();
						}
						else if($literal == '' && $statement instanceof PrintStatement)
						{
							$statement = new PrintErrorStatement();
						}
						else
						{
							$this->specialCharacter('!', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '?':
						if($statement === null && $literal == '')
						{
							$statement = new IfStatement();
						}
						else
						{
							$this->specialCharacter('?', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '@':
						if($statement === null && $literal == '')
						{
							$statement = new WhileStatement();
						}
						else
						{
							$this->specialCharacter('@', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '+':
					case '-':
					case '^':
					case '%':
					case '|':
					case '≠':
					case '≥':
						$this->specialCharacter($chars[$i], $literal, $statement, $local_vars, $ret);
						break;
					case '/':
						if($statement === null && $literal == '')
						{
							$literal = '/';
						}
						else if($statement === null && $literal == '/')
						{
							$literal = '';
							$this->processComment($chars, $i, $end_i);
						}
						else
						{
							$this->specialCharacter('/', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '*':
						if($statement === null)
						{
							if($literal == '')
							{
								$statement = new FunctionDeclarationStatement();
							}
							else if($literal == '/')
							{
								$literal = '';
								if($this->debug)
								{
									$this->say("<comment/>");
								}
								$star = false;
								while(++$i < $end_i)
								{
									if($star && $chars[$i] == "/")
									{
										break;
									}
									else if($chars[$i] == "*")
									{
										$star = true;
									}
									else if($star)
									{
										$star = false;
									}
								}
								if(!$star)
								{
									throw new IncompleteCodeException("Code unexpectedly ended whilst reading multi-line comment");
								}
								if($this->debug)
								{
									$this->say("*/");
								}
							}
						}
						else
						{
							$this->specialCharacter('*', $literal, $statement, $local_vars, $ret);
						}
						break;
					case '"':
					case '\'':
					case '`':
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						$str = $this->readString($chars[$i], $chars, $i, $end_i);
						if($statement instanceof Statement)
						{
							if($statement->acceptsValues())
							{
								$statement->acceptValue(new StringStatement($str), $this, $local_vars);
							}
							else
							{
								$statement->acceptLiteral($chars[$i].$str.$chars[$i], $this, $local_vars);
							}
							$this->processStatement($statement, $local_vars, $ret);
						}
						else
						{
							$statement = new StringStatement($str);
						}
						break;
					case '{':
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						$str = $this->readBracketString($chars, $i, $end_i);
						if($statement instanceof Statement)
						{
							if($statement->acceptsValues())
							{
								$statement->acceptValue(new StringStatement($str), $this, $local_vars);
							}
							else
							{
								$statement->acceptLiteral("{".$str."}", $this, $local_vars);
							}
							$this->processStatement($statement, $local_vars, $ret);
						}
						else
						{
							$statement = new StringStatement($str);
						}
						break;
					case '(':
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						$str = $this->readInlineStatement($chars, $i, $end_i);
						if($statement instanceof Statement)
						{
							if($statement->acceptsValues())
							{
								$ret = $this->parseAndExecute($str, $local_vars);
								if($ret instanceof ReturnStatement)
								{
									$ret = $ret->value;
								}
								if(!$ret instanceof VariableStatement)
								{
									throw new InvalidCodeException("Return of inline code is unusable: ".gettype($ret));
								}
								$statement->acceptValue($ret, $this, $local_vars);
							}
							else
							{
								$statement->acceptLiteral($str, $this, $local_vars);
							}
							$this->processStatement($statement, $local_vars, $ret);
						}
						else
						{
							$statement = $this->parseAndExecute($str, $local_vars);
						}
						break;
					case '[':
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						$arr = $this->parseAndExecuteWithWritableLocalVars("array ".$this->readArray($chars, $i, $end_i), $local_vars);
						assert($arr instanceof ArrayStatement);
						if($statement instanceof Statement)
						{
							if($statement->acceptsValues())
							{
								$statement->acceptValue($arr, $this, $local_vars);
							}
							else
							{
								$statement->acceptLiteral($arr->toLiteral(), $this, $local_vars);
							}
						}
						else
						{
							$statement = $arr;
						}
						break;
					case ';':
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						if($statement instanceof Statement)
						{
							if(!$statement->isExecutable())
							{
								throw new InvalidCodeException(get_class($statement)." is not executable");
							}
							$ret = $statement->execute($this, $local_vars);
							$statement = null;
							if($ret instanceof ExitStatement)
							{
								break 2;
							}
						}
						break;
					case ' ':
					case "\t":
					case "\n":
						$this->processLiteral($literal, $statement, $local_vars, $ret);
						break;
					case "\r":
						break;
					default:
						$literal .= $chars[$i];
				}
			}
			$this->processLiteral($literal, $statement, $local_vars, $ret);
			if($statement instanceof Statement && ($statement !== $ret || ($statement instanceof VariableStatement && $statement->action != 0)))
			{
				if(!$statement->isExecutable())
				{
					throw new IncompleteCodeException(get_class($statement)." is not executable");
				}
				$ret = $statement->execute($this, $local_vars);
			}
			if($root_script && $ret instanceof ExitStatement && $ret->value)
			{
				if($this->debug)
				{
					$this->say("<output>".Utopia::strval($ret->value)."</output>");
				}
				else
				{
					$this->say(Utopia::strval($ret->value));
				}
			}
			if($this->debug)
			{
				$this->say("</block>");
			}
			return $ret === null ? new NullStatement() : $ret;
		}
		finally
		{
			if($root_script)
			{
				$this->last_execution_time = microtime(true) - $this->execute_start - $this->input_time;
				$this->execute_start = null;
			}
		}
	}

	/**
	 * @param string $str
	 */
	function say(string $str)
	{
		if($this->output_handler)
		{
			($this->output_handler)($str);
		}
	}

	/**
	 * @param array $chars
	 * @param int $i
	 * @param int $end_i
	 */
	function processComment(array &$chars, int &$i, int &$end_i)
	{
		if($this->debug)
		{
			$this->say("<comment/>\r\n");
		}
		while(++$i < $end_i)
		{
			if($chars[$i] == "\n")
			{
				return;
			}
		}
	}

	/**
	 * @param string $character
	 * @param string $literal
	 * @param        $statement
	 * @param array $local_vars
	 * @param        $ret
	 * @throws InvalidCodeException
	 */
	function specialCharacter(string $character, string &$literal, &$statement, array &$local_vars, &$ret)
	{
		$this->processLiteral($literal, $statement, $local_vars, $ret);
		$literal = $character;
		$this->processLiteral($literal, $statement, $local_vars, $ret);
	}

	/**
	 * @param string $literal
	 * @param        $statement
	 * @param array $local_vars
	 * @param        $ret
	 * @throws InvalidCodeException
	 */
	function processLiteral(string &$literal, &$statement, array &$local_vars, &$ret)
	{
		if($literal == '')
		{
			return;
		}
		$literal = strtolower($literal);
		if($statement instanceof Statement)
		{
			if($statement->acceptsValues())
			{
				if(array_key_exists($literal, $local_vars))
				{
					$statement->acceptValue(clone $local_vars[$literal]->value, $this, $local_vars);
				}
				else if(array_key_exists($literal, $this->vars))
				{
					$statement->acceptValue(clone $this->vars[$literal]->value, $this, $local_vars);
				}
				else if(self::is_numeric($literal))
				{
					$statement->acceptValue(new NumberStatement(self::numval($literal)), $this, $local_vars);
				}
				else
				{
					$statement->acceptLiteral($literal, $this, $local_vars);
				}
			}
			else
			{
				$statement->acceptLiteral($literal, $this, $local_vars);
			}
			$this->processStatement($statement, $local_vars, $ret);
		}
		else if(array_key_exists($literal, $this->statements))
		{
			$statement = new $this->statements[$literal]();
			$this->processStatement($statement, $local_vars, $ret);
		}
		else if(array_key_exists($literal, $local_vars))
		{
			$statement = clone $local_vars[$literal]->value;
		}
		else if(array_key_exists($literal, $this->vars))
		{
			$statement = clone $this->vars[$literal]->value;
		}
		else if(self::is_numeric($literal))
		{
			$statement = new NumberStatement(self::numval($literal));
		}
		else
		{
			$type = self::getCanonicalType($literal);
			if($type !== null)
			{
				$statement = new FunctionDeclarationStatement($type);
			}
			else
			{
				throw new InvalidCodeException("Unknown statement or variable: ".$literal);
			}
		}
		$literal = '';
	}

	/**
	 * @param Statement $statement
	 * @param array $local_vars
	 * @param           $ret
	 */
	function processStatement(Statement &$statement, array &$local_vars, &$ret)
	{
		if($statement->isFinished())
		{
			$ret = $statement->execute($this, $local_vars);
			if($ret instanceof VariableStatement)
			{
				$statement = $ret;
				if($statement instanceof StringStatement)
				{
					$statement->exec = false;
				}
			}
			else
			{
				$statement = null;
			}
		}
	}

	/**
	 * @param string $type
	 * @return string|null
	 */
	static function getCanonicalType(string $type)
	{
		switch($type)
		{
			case 'nil':
			case 'void':
			case 'none':
			case 'null':
				return 'null';
			case 'num':
			case 'number':
			case 'int':
			case 'integer':
				return 'number';
			case 'str':
			case 'string':
				return 'string';
			case 'func':
			case 'routine':
			case 'function':
				return 'function';
			case 'arr':
			case 'array':
				return 'array';
			case 'bool':
			case 'boolean':
				return 'boolean';
			case 'mixed':
			case 'anytype':
			case 'any_type':
				return 'any_type';
		}
		return null;
	}

	/**
	 * @param string $delimiter
	 * @param array $chars
	 * @param int $i
	 * @param int $end_i
	 * @return string
	 * @throws IncompleteCodeException
	 */
	function readString(string $delimiter, array &$chars, int &$i, int &$end_i)
	{
		if($this->debug)
		{
			$this->say("<string>");
		}
		$str = '';
		while(++$i < $end_i)
		{
			if($chars[$i] == $delimiter)
			{
				if($this->debug)
				{
					$this->say("</string>".$chars[$i]);
				}
				return $str;
			}
			if($this->debug)
			{
				$this->say($chars[$i]);
			}
			$str .= $chars[$i];
		}
		throw new IncompleteCodeException("Code unexpectedly ended whilst reading {$delimiter} string");
	}

	/**
	 * @param array $chars
	 * @param int $i
	 * @param int $end_i
	 * @return string
	 * @throws IncompleteCodeException
	 */
	function readBracketString(array &$chars, int &$i, int &$end_i)
	{
		if($this->debug)
		{
			$this->say("<string>");
		}
		$depth = 0;
		$str = '';
		while(++$i < $end_i)
		{
			switch($chars[$i])
			{
				case '{':
					$depth++;
					$str .= '{';
					if($this->debug)
					{
						$this->say($chars[$i]."<depth>");
					}
					break;
				/** @noinspection PhpMissingBreakStatementInspection */ case '}':
				if($depth == 0)
				{
					if($this->debug)
					{
						$this->say("</string>".$chars[$i]);
					}
					return $str;
				}
				if($this->debug)
				{
					$this->say("</depth>");
				}
				$depth--;
				default:
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= $chars[$i];
			}
		}
		throw new IncompleteCodeException("Code unexpectedly ended whilst reading bracket string");
	}

	/**
	 * @param array $chars
	 * @param int $i
	 * @param int $end_i
	 * @return string
	 * @throws IncompleteCodeException
	 */
	function readInlineStatement(array &$chars, int &$i, int &$end_i)
	{
		if($this->debug)
		{
			$this->say("<inline>");
		}
		$depth = 0;
		$str = '';
		while(++$i < $end_i)
		{
			switch($chars[$i])
			{
				case '{':
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= '{'.$this->readBracketString($chars, $i, $end_i).'}';
					break;
				case '"':
				case '\'':
				case '`':
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= $chars[$i].$this->readString($chars[$i], $chars, $i, $end_i).$chars[$i];
					break;
				case '(':
					$depth++;
					$str .= '(';
					if($this->debug)
					{
						$this->say($chars[$i]."<depth>");
					}
					break;
				/** @noinspection PhpMissingBreakStatementInspection */ case ')':
				if($depth == 0)
				{
					if($this->debug)
					{
						$this->say("</inline>".$chars[$i]);
					}
					return $str;
				}
				if($this->debug)
				{
					$this->say("</depth>");
				}
				$depth--;
				default:
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= $chars[$i];
			}
		}
		throw new IncompleteCodeException("Code unexpectedly ended whilst reading inline statement");
	}

	/**
	 * @param array $chars
	 * @param int $i
	 * @param int $end_i
	 * @return string
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 */
	function readArray(array &$chars, int &$i, int &$end_i)
	{
		if($this->debug)
		{
			$this->say("<array>");
		}
		$depth = 0;
		$str = '';
		while(++$i < $end_i)
		{
			switch($chars[$i])
			{
				case '{':
					if($this->debug)
					{
						$this->say('{');
					}
					$str .= '{'.$this->readBracketString($chars, $i, $end_i).'}';
					break;
				case '"':
				case '\'':
				case '`':
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= $chars[$i].$this->readString($chars[$i], $chars, $i, $end_i).$chars[$i];
					break;
				case ';':
					throw new InvalidCodeException("Unexpected semicolon in array declaration");
				case '[':
					$depth++;
					$str .= '[';
					if($this->debug)
					{
						$this->say($chars[$i]."<depth>");
					}
					break;
				/** @noinspection PhpMissingBreakStatementInspection */ case ']':
				if($depth == 0)
				{
					if($this->debug)
					{
						$this->say("</array>".$chars[$i]);
					}
					return $str;
				}
				if($this->debug)
				{
					$this->say("</depth>");
				}
				$depth--;
				default:
					if($this->debug)
					{
						$this->say($chars[$i]);
					}
					$str .= $chars[$i];
			}
		}
		throw new IncompleteCodeException("Code unexpectedly ended whilst reading array");
	}

	static function strval($value): string
	{
		return $value instanceof VariableStatement ? $value->__toString() : strval(Utopia::externalize($value));
	}

	/**
	 * @param Statement $value
	 * @return mixed
	 */
	static function externalize($value)
	{
		$value = self::unwrap($value);
		return $value instanceof VariableStatement ? $value->externalize() : $value;
	}

	/**
	 * @param string $str
	 */
	function complain(string $str)
	{
		if($this->error_output_handler)
		{
			($this->error_output_handler)($str);
		}
	}

	/**
	 * @param string|Extension $extension
	 * @throws InvalidArgumentException
	 */
	function loadExtension($extension)
	{
		if(gettype($extension) == "string")
		{
			$extension = new $extension();
		}
		if(!$extension instanceof Extension)
		{
			throw new InvalidArgumentException("Parameter is not a valid extension");
		}
		$this->statements = array_merge($this->statements, $extension->getStatements());
	}

	/**
	 * @param string $name
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function scrutinizeVariableName(string $name, array $local_vars = [])
	{
		if(array_key_exists($name, $this->statements))
		{
			throw new InvalidCodeException("Can't overwrite statement: ".$name);
		}
		if(array_key_exists($name, $local_vars))
		{
			if($local_vars[$name]->final)
			{
				throw new InvalidCodeException("Can't overwrite final: ".$name);
			}
		}
		else if(array_key_exists($name, $this->vars))
		{
			if($this->vars[$name]->final)
			{
				throw new InvalidCodeException("Can't overwrite constant: ".$name);
			}
		}
		if(self::getCanonicalType($name) !== null || self::is_numeric($name))
		{
			throw new InvalidCodeException("Invalid variable name: ".$name);
		}
	}
}
