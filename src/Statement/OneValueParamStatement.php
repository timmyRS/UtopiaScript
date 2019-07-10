<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Variable\VariableStatement, Utopia};
abstract class OneValueParamStatement extends Statement
{
	/**
	 * @var VariableStatement|string $value
	 */
	public $value;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->value !== null;
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
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		if($this->value === null)
		{
			$this->value = $value;
		}
		else if(gettype($this->value) == "string")
		{
			$this->value .= " ".$value->toLiteral();
		}
		else
		{
			$this->value->acceptValue($value, $utopia, $local_vars);
			if($this->value->isFinished())
			{
				$this->value = $this->value->execute($utopia, $local_vars);
			}
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
		if($this->value === null)
		{
			$this->value = $literal;
		}
		else if(gettype($this->value) == "string")
		{
			$this->value .= " ".$literal;
		}
		else
		{
			$this->value->acceptLiteral($literal, $utopia, $local_vars);
			if($this->value->isFinished())
			{
				$this->value = $this->value->execute($utopia, $local_vars);
			}
		}
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function _execute(Utopia &$utopia, array &$local_vars = [])
	{
		if($this->value !== null)
		{
			if(gettype($this->value) == "string")
			{
				$this->value = $utopia->parseAndExecuteWithWritableLocalVars($this->value, $local_vars);
			}
			else if($this->value->isExecutable() && Utopia::isStatementExecutionSafe($this->value))
			{
				$this->value = $this->value->execute($utopia, $local_vars);
			}
		}
	}
}
