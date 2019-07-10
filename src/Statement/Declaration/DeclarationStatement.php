<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\InvalidTypeException, Exception\TimeoutException, Statement\Statement, Statement\Variable\NullStatement, Statement\Variable\VariableStatement, Utopia};
abstract class DeclarationStatement extends Statement
{
	/**
	 * @var string $name
	 */
	public $name;
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
		return $this->name !== null;
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return $this->value instanceof VariableStatement && $this->value->isFinished();
	}

	function acceptsValues(): bool
	{
		return $this->name !== null;
	}

	/**
	 * @param mixed $value
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
	function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars)
	{
		if($this->name === null)
		{
			$this->name = $literal;
		}
		else if($this->value === null)
		{
			if($literal == '=')
			{
				return;
			}
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
		if($this->value === null)
		{
			$this->value = new NullStatement();
		}
		else if(gettype($this->value) == "string")
		{
			$this->value = $utopia->parseAndExecuteWithWritableLocalVars($this->value, $local_vars);
		}
		else if($this->value->isExecutable() && Utopia::isStatementExecutionSafe($this->value))
		{
			$this->value = $this->value->execute($utopia, $local_vars);
		}
		if(!$this->value instanceof VariableStatement)
		{
			throw new InvalidTypeException("Declaration expected variable, got ".get_class($this->value));
		}
	}
}
