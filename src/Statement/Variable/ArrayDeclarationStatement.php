<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\Exception\IncompleteCodeException;
use UtopiaScript\Exception\InvalidCodeException;
use UtopiaScript\Exception\InvalidEnvironmentException;
use UtopiaScript\Exception\TimeoutException;
use UtopiaScript\Statement\Statement;
use UtopiaScript\Utopia;
class ArrayDeclarationStatement extends Statement
{
	/**
	 * @var array $arr
	 */
	public $arr = [];
	public $key = false;

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
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		$this->accept($value);
	}

	/**
	 * @param $value
	 * @throws InvalidCodeException
	 */
	private function accept($value)
	{
		if($this->key)
		{
			if(function_exists("array_key_last")) // PHP >= 7.3.0
			{
				$last_index = array_key_last($this->arr);
			}
			else
			{
				$last_index = array_keys($this->arr)[count($this->arr) - 1];
			}
			$key = $this->arr[$last_index];
			unset($this->arr[$last_index]);
			if($key instanceof VariableStatement)
			{
				if($key instanceof FunctionStatement)
				{
					throw new InvalidCodeException("A function can't be an array key.");
				}
				$key = $key->value;
			}
			$this->arr[$key] = $value;
			$this->key = false;
		}
		else
		{
			array_push($this->arr, $value);
		}
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if($literal == ':' || $literal == '=')
		{
			if(count($this->arr) == 0)
			{
				throw new InvalidCodeException("Unexpected {$literal} as first literal in array declaration");
			}
			$this->key = true;
		}
		else
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
	function execute(Utopia $utopia, array &$local_vars = []): Statement
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