<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
class ArrayStatement extends VariableStatement
{
	const ACTION_FOR_EACH = 1;
	const ACTION_VALUE_OF_KEY = 2;

	function __construct(array $value)
	{
		parent::__construct($value);
	}

	static function getType(): string
	{
		return "array";
	}

	function isExecutable(): bool
	{
		return $this->action == 0 || ($this->action_data["var_name"] !== null && $this->action_data["func"] !== null);
	}

	function isFinished(): bool
	{
		switch($this->action)
		{
			case self::ACTION_FOR_EACH:
				return $this->action_data["var_name"] !== null && $this->action_data["func"] !== null && $this->action_data["key_name"] !== null;
			case self::ACTION_VALUE_OF_KEY:
				return $this->action_data["key"] !== null;
		}
		return parent::isFinished();
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
			switch($this->action)
			{
				case self::ACTION_VALUE_OF_KEY:
					if($this->action_data["key"] === null)
					{
						if(!$value instanceof VariableStatement || $value instanceof FunctionStatement)
						{
							throw new InvalidCodeException("Invalid array key: ".get_class($value));
						}
						if(!array_key_exists($value->value, $this->value))
						{
							throw new InvalidCodeException("Array doesn't have a value with key ".$value->value);
						}
						$this->action_data["key"] = $value->value;
					}
					break;
				case 0:
					if($value instanceof VariableStatement && !$value instanceof FunctionStatement)
					{
						if(!array_key_exists($value->value, $this->value))
						{
							throw new InvalidCodeException("Array doesn't have a value with key ".$value->value);
						}
						$this->action = self::ACTION_VALUE_OF_KEY;
						$this->action_data["key"] = $value->value;
						return;
					}
					throw new InvalidCodeException("ArrayStatement doesn't accept ".get_class($value)." in this context");
			}
			throw new InvalidCodeException("ArrayStatement doesn't accept values in this context");
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
							return;
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
							return;
						default:
							if(array_key_exists($literal, $this->value))
							{
								$this->action = self::ACTION_VALUE_OF_KEY;
								$this->action_data = ["key" => $literal];
								return;
							}
							throw new InvalidCodeException("Invalid action or key: ".$literal);
					}
			}
			throw new InvalidCodeException("ArrayStatement doesn't accept literals in this context");
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
		return ($this->_execute($utopia, $local_vars) ?? $this->execute_($utopia, $local_vars)) ?? $this;
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement|null
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function execute_(Utopia $utopia, array $local_vars = [])
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
					$local_vars[$this->action_data["var_name"]] = new Variable($item, true);
					if($this->action_data["key_name"] !== null)
					{
						$local_vars[$this->action_data["key_name"]] = new Variable(new StringStatement($key, false), true);
					}
					$utopia->parseAndExecute($this->action_data["func"], $local_vars);
				}
				break;
			case self::ACTION_VALUE_OF_KEY:
				if($this->action_data["key"] === null)
				{
					throw new InvalidCodeException("Value of key action was not finished");
				}
				return $this->value[$this->action_data["key"]];
		}
		$this->action = 0;
		$this->action_data = null;
		return null;
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
		foreach($this->value as $item)
		{
			array_push($arr, Utopia::externalize($item));
		}
		return $arr;
	}
}
