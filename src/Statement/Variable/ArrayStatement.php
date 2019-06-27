<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
class ArrayStatement extends VariableStatement
{
	const ACTION_FOR_EACH = 1;
	const ACTION_VALUE_OF_KEY = 2;
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
		return $this->action != 0 && $this->isExecutable() && ($this->action != self::ACTION_FOR_EACH || $this->action_data["key_name"] !== null);
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
				return $this->action_data["key"] !== null;
		}
		return parent::isExecutable();
	}

	function acceptsValues(): bool
	{
		return !in_array($this->action, [self::ACTION_FOR_EACH]);
	}

	/**
	 * @param VariableStatement $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if($this->_acceptValue($value))
		{
			if($this->action > 99)
			{
				if(!$value instanceof ArrayStatement && !$value instanceof NumberStatement)
				{
					throw new InvalidCodeException("Array doesn't accept ".$value->getType()." in this context");
				}
				$this->action_data["b"] = $value->value;
			}
			else switch($this->action)
			{
				case self::ACTION_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						if($value instanceof FunctionStatement)
						{
							throw new InvalidCodeException("A function can't be an array key");
						}
						if(!array_key_exists($value->value, $this->value))
						{
							throw new InvalidCodeException("Array doesn't have a value with key ".$value->value);
						}
						$this->action_data["key"] = $value->value;
					}
					break;
				case 0:
					if(!$value instanceof FunctionStatement)
					{
						if(!array_key_exists($value->value, $this->value))
						{
							throw new InvalidCodeException("Array doesn't have a value with key ".$value->value);
						}
						$this->action = self::ACTION_VALUE_OF_KEY;
						$this->action_data["key"] = $value->value;
						return;
					}
					throw new InvalidCodeException("Array doesn't accept ".$value->getType()." in this context");
				default:
					throw new InvalidCodeException("Array doesn't accept values in this context");
			}
		}
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if($this->_acceptLiteral($literal))
		{
			switch($this->action)
			{
				case self::ACTION_FOR_EACH:
					if($this->action_data["var_name"] === null)
					{
						$this->action_data["var_name"] = $literal;
						return;
					}
					else if($this->action_data["func"] === null)
					{
						$this->action_data["func"] = $literal;
						return;
					}
					else if($this->action_data["key_name"] === null)
					{
						$this->action_data["key_name"] = $this->action_data["var_name"];
						$this->action_data["var_name"] = $this->action_data["func"];
						$this->action_data["func"] = $literal;
						return;
					}
					break;
				case self::ACTION_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						if(!array_key_exists($literal, $this->value))
						{
							throw new InvalidCodeException("Array doesn't have a value with key ".$literal);
						}
						$this->action_data["key"] = $literal;
						return;
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
						case '.':
						case ':':
						case 'value_of':
						case 'valueof':
						case 'get':
						case 'getvalue':
						case 'get_value':
						case 'value':
							$this->action = self::ACTION_VALUE_OF_KEY;
							$this->action_data = ["key" => null];
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
								$this->action_data = ["key" => $literal];
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
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		$ret = $this->_execute($utopia, $local_vars);
		if($ret === null)
		{
			switch($this->action)
			{
				case self::ACTION_FOR_EACH:
					if($this->action_data["var_name"] === null || $this->action_data["func"] === null)
					{
						throw new InvalidCodeException("Foreach action was not finished");
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
				case self::ACTION_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						throw new InvalidCodeException("Value of key action was not finished");
					}
					$ret = $this->value[$this->action_data["key"]];
					break;
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
			$this->action = 0;
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
			if($key != $i)
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
