<?php /** @noinspection PhpUndefinedMethodInspection */
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\InvalidTypeException, Exception\TimeoutException, Statement\Declaration\SetStatement, Statement\Statement, Utopia, Variable};
class ArrayStatement extends VariableStatement
{
	const ACTION_FOR_EACH = 1;
	const ACTION_VALUE_OF_KEY = 2;
	const ACTION_GET_VALUE_OF_KEY = 3;
	const ACTION_SET_VALUE_OF_KEY = 4;
	const ACTION_ADD = 100;
	const ACTION_SUB = 101;
	const ACTION_MUL = 102;
	const ACTION_DIV = 103;

	function __construct(array $value)
	{
		parent::__construct($value);
	}

	static function getType(): string
	{
		return "array";
	}

	function isFinished(): bool
	{
		if(!$this->isExecutable())
		{
			return false;
		}
		switch($this->action)
		{
			case 0:
			case self::ACTION_VALUE_OF_KEY:
			case self::ACTION_SET_VALUE_OF_KEY:
				return false;
			case self::ACTION_FOR_EACH:
				return $this->action_data["key_name"] !== null;
		}
		return true;
	}

	function isExecutable(): bool
	{
		if($this->action > 99)
		{
			return $this->action_data["b"] !== null;
		}
		switch($this->action)
		{
			case self::ACTION_FOR_EACH:
				return $this->action_data["var_name"] !== null && $this->action_data["func"] !== null;
			case self::ACTION_VALUE_OF_KEY:
			case self::ACTION_GET_VALUE_OF_KEY:
				return $this->action_data["key"] !== null;
			case self::ACTION_SET_VALUE_OF_KEY:
				return $this->action_data["key"] !== null && $this->action_data["value"] !== null;
		}
		return parent::isExecutable();
	}

	function acceptsValues(): bool
	{
		return !in_array($this->action, [self::ACTION_FOR_EACH]);
	}

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		if($this->_acceptValue($value))
		{
			if($this->action > 99)
			{
				if(!$value instanceof ArrayStatement && !$value instanceof NumberStatement)
				{
					throw new InvalidTypeException("Array doesn't accept ".$value->getType()." in this context");
				}
				$this->action_data["b"] = $value->value;
			}
			else switch($this->action)
			{
				case self::ACTION_GET_VALUE_OF_KEY:
					if(!self::isValidKey($value))
					{
						throw new InvalidTypeException($value->getType()." can't be an array key");
					}
					$this->action_data["key"] = $value->value;
					break;
				/** @noinspection PhpMissingBreakStatementInspection */ case self::ACTION_SET_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						if(!self::isValidKey($value))
						{
							throw new InvalidTypeException($value->getType()." can't be an array key");
						}
						$this->action_data["key"] = $value->value;
						break;
					}
				case self::ACTION_VALUE_OF_KEY:
					if($this->action_data["value"] instanceof Statement)
					{
						$this->action_data["value"]->acceptValue($value, $utopia, $local_vars);
						if($this->action_data["value"]->isFinished())
						{
							$this->action_data["value"] = $this->action_data["value"]->execute($utopia, $local_vars);
						}
					}
					else if($this->action_data["value"] === null)
					{
						$this->action_data["value"] = $value;
					}
					else
					{
						$this->action_data["value"] .= " ".$value->toLiteral();
					}
					break;
				case 0:
					if(self::isValidKey($value))
					{
						$this->action = self::ACTION_VALUE_OF_KEY;
						$this->action_data = [
							"key" => $value->value,
							"value" => null
						];
						return;
					}
					throw new InvalidTypeException("Array doesn't accept ".$value->getType()." in this context");
				default:
					throw new InvalidCodeException("Array doesn't accept values in this context");
			}
		}
	}

	static function isValidKey(VariableStatement $statement)
	{
		return in_array($statement->getType(), [
			"string",
			"number",
			"boolean",
			"null"
		]);
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		if($this->_acceptLiteral($literal))
		{
			switch($this->action)
			{
				case self::ACTION_FOR_EACH:
					if($this->action_data["var_name"] === null)
					{
						$this->action_data["var_name"] = $literal;
					}
					else if($this->action_data["func"] === null)
					{
						$this->action_data["func"] = $literal;
					}
					else if($this->action_data["key_name"] === null)
					{
						$this->action_data["key_name"] = $this->action_data["var_name"];
						$this->action_data["var_name"] = $this->action_data["func"];
						$this->action_data["func"] = $literal;
					}
					break;
				case self::ACTION_GET_VALUE_OF_KEY:
					$this->action_data["key"] = $literal;
					break;
				case self::ACTION_VALUE_OF_KEY:
				case self::ACTION_SET_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						$this->action_data["key"] = $literal;
					}
					else if($this->action_data["value"] instanceof Statement)
					{
						$this->action_data["value"]->acceptLiteral($literal, $utopia, $local_vars);
						if($this->action_data["value"]->isFinished())
						{
							$this->action_data["value"] = $this->action_data["value"]->execute($utopia, $local_vars);
						}
					}
					else if($this->action_data["value"] === null)
					{
						if($literal != '=' && $literal != 'as')
						{
							$this->action_data["value"] = $literal;
						}
					}
					else
					{
						$this->action_data["value"] .= " ".$literal;
					}
					break;
				case 0:
					switch($literal)
					{
						case 'for_each':
						case '@':
						case 'each':
						case 'foreach':
						case 'iterate':
							$this->action = self::ACTION_FOR_EACH;
							$this->action_data = [
								"var_name" => null,
								"key_name" => null,
								"func" => null
							];
							break;
						case 'get':
						case 'getvalue':
						case 'get_value':
							$this->action = self::ACTION_GET_VALUE_OF_KEY;
							$this->action_data = ["key" => null];
							break;
						case 'set':
						case 'setvalue':
						case 'set_value':
							$this->action = self::ACTION_SET_VALUE_OF_KEY;
							$this->action_data = [
								"key" => null,
								"value" => null
							];
							break;
						case '+':
						case 'plus':
							$this->action = self::ACTION_ADD;
							break;
						case '-':
						case 'minus':
							$this->action = self::ACTION_SUB;
							break;
						case '*':
						case 'times':
							$this->action = self::ACTION_MUL;
							break;
						case '/':
						case 'divided_by':
							$this->action = self::ACTION_DIV;
							break;
						default:
							if(array_key_exists($literal, $this->value))
							{
								$this->action = self::ACTION_VALUE_OF_KEY;
								$this->action_data = [
									"key" => $literal,
									"value" => null
								];
								break;
							}
							throw new InvalidCodeException("Invalid action or key: ".$literal);
					}
					if($this->action > 199)
					{
						$this->action_data = ["b" => null];
					}
			}
		}
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		$ret = $this->_execute($utopia, $local_vars);
		if($ret === null)
		{
			$action = $this->action;
			$this->action = 0;
			if($action == self::ACTION_VALUE_OF_KEY)
			{
				$action = $this->action_data["value"] === null ? self::ACTION_GET_VALUE_OF_KEY : self::ACTION_SET_VALUE_OF_KEY;
			}
			switch($action)
			{
				case self::ACTION_FOR_EACH:
					if($this->action_data["var_name"] === null || $this->action_data["func"] === null)
					{
						throw new IncompleteCodeException("Foreach action was not finished");
					}
					$names = ["var_name"];
					if($this->action_data["key_name"] !== null)
					{
						array_push($names, "var_name");
					}
					foreach($names as $name)
					{
						$utopia->scrutinizeVariableName($name, $local_vars);
					}
					foreach($this->value as $key => $item)
					{
						$local_vars_ = $local_vars;
						$local_vars_[$this->action_data["var_name"]] = new Variable($item, true);
						if($this->action_data["key_name"] !== null)
						{
							$local_vars_[$this->action_data["key_name"]] = new Variable(new StringStatement($key, false), true);
						}
						$utopia->parseAndExecute($this->action_data["func"], $local_vars_);
					}
					break;
				case self::ACTION_GET_VALUE_OF_KEY:
					if(!array_key_exists($this->action_data["key"], $this->value))
					{
						throw new InvalidCodeException("Array doesn't have a value with key ".$this->action_data["key"]);
					}
					$ret = clone $this->value[$this->action_data["key"]];
					break;
				case self::ACTION_SET_VALUE_OF_KEY:
					if(!$this->action_data["value"] instanceof Statement)
					{
						$this->action_data["value"] = $utopia->parseAndExecuteWithWritableLocalVars($this->action_data["value"], $local_vars);
					}
					else if($this->action_data["value"]->isExecutable() && Utopia::isStatementExecutionSafe($this->action_data["value"]))
					{
						$this->action_data["value"] = $this->action_data["value"]->execute($utopia, $local_vars);
					}
					if(!$this->action_data["value"] instanceof VariableStatement)
					{
						throw new InvalidTypeException("Expected variable, got ".get_class($this->action_data["value"]));
					}
					$ret = $this->value[$this->action_data["key"]] = $this->action_data["value"];
					$this->action_data = null;
					$set = new SetStatement();
					$set->name = $this->name;
					$set->value = $this;
					$set->execute($utopia, $local_vars);
					return $ret;
				case self::ACTION_ADD:
					$ret = NumberStatement::add($this->value, $this->action_data["b"]);
					break;
				case self::ACTION_SUB:
					$ret = NumberStatement::sub($this->value, $this->action_data["b"]);
					break;
				case self::ACTION_MUL:
					$ret = NumberStatement::mul($this->value, $this->action_data["b"]);
					break;
				case self::ACTION_DIV:
					$ret = NumberStatement::div($this->value, $this->action_data["b"]);
			}
			$this->action_data = null;
		}
		return $ret ?? $this;
	}

	function __toString(): string
	{
		$str = "array";
		$i = 0;
		foreach($this->value as $key => $item)
		{
			if($key !== $i)
			{
				$str .= " ".$key." =";
			}
			if($item instanceof ArrayStatement)
			{
				$str .= " (".$item->toLiteral().")";
			}
			else
			{
				if($item instanceof VariableStatement)
				{
					$str .= " ".$item->toLiteral();
				}
				else
				{
					$str .= " ".$item;
				}
			}
			$i++;
		}
		return $str;
	}

	function externalize()
	{
		$arr = [];
		foreach($this->value as $key => $value)
		{
			$arr[$key] = Utopia::externalize($value);
		}
		return $arr;
	}
}
