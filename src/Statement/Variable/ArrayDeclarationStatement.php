<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\InvalidTypeException, Exception\TimeoutException, Statement\Statement, Utopia};
class ArrayDeclarationStatement extends Statement
{
	const STATE_KEY = 1;
	const STATE_RANGE = 2;
	/**
	 * @var array $arr
	 */
	public $arr = [];
	public $state = 0;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return true;
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return false;
	}

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
	{
		return true;
	}

	/**
	 * @param mixed $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		$this->accept($value);
	}

	/**
	 * @param $value
	 * @throws InvalidTypeException
	 */
	function accept($value)
	{
		if($this->state > 0)
		{
			if(function_exists("array_key_last")) // PHP >= 7.3.0
			{
				$last_index = array_key_last($this->arr);
			}
			else
			{
				$last_index = array_keys($this->arr)[count($this->arr) - 1];
			}
			$last_value = $this->arr[$last_index];
			unset($this->arr[$last_index]);
			if($last_value instanceof VariableStatement)
			{
				$last_value = $last_value->value;
			}
			if($this->state == self::STATE_RANGE)
			{
				if($value instanceof VariableStatement)
				{
					$value = $value->value;
				}
				if((is_string($last_value) ? 1 : 0) ^ (is_string($value) ? 1 : 0) == 1)
				{
					throw new InvalidTypeException("Array range has to be either string-string or number-number.");
				}
				if(empty($this->arr))
				{
					$this->arr = [];
				}
				if(is_string($value))
				{
					foreach(range($last_value, $value) as $item)
					{
						array_push($this->arr, new StringStatement($item));
					}
				}
				else
				{
					if(!is_numeric($value) || !is_numeric($last_value))
					{
						throw new InvalidTypeException("Array range has to be either string-string or number-number.");
					}
					foreach(range($last_value, $value) as $item)
					{
						array_push($this->arr, new NumberStatement($item));
					}
				}
			}
			else // $this->state == self::STATE_KEY
			{
				if(!ArrayStatement::isValidKey($last_value))
				{
					throw new InvalidTypeException($last_value->getType()." can't be an array key");
				}
				$this->arr[$last_value] = $value;
			}
			$this->state = 0;
		}
		else
		{
			array_push($this->arr, $value);
		}
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		if($literal == ":" || $literal == "=")
		{
			if(count($this->arr) == 0)
			{
				throw new InvalidCodeException("Unexpected {$literal} as first literal in array declaration");
			}
			$this->state = self::STATE_KEY;
		}
		else if($literal == "-" || $literal == "to")
		{
			if(count($this->arr) == 0)
			{
				throw new InvalidCodeException("Unexpected {$literal} as first literal in array declaration");
			}
			$this->state = self::STATE_RANGE;
		}
		else if($literal != ",")
		{
			$this->accept($literal);
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
		$arr = [];
		foreach($this->arr as $key => $item)
		{
			if(gettype($item) == "string")
			{
				$item = $utopia->parseAndExecuteWithWritableLocalVars($item, $local_vars);
			}
			assert($item instanceof VariableStatement);
			$arr[$key] = $item;
		}
		return new ArrayStatement($arr);
	}
}