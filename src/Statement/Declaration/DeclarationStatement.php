<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Statement\Variable\NullStatement, Statement\Variable\VariableStatement, Utopia};
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
	 * @var boolean $execute
	 */
	public $execute = false;

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
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
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
			$this->value->acceptValue($value);
		}
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
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
			$this->value->acceptLiteral($literal);
			$this->execute = true;
		}
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @param bool $global
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function _execute(Utopia $utopia, array &$local_vars = [], bool $global = false)
	{
		$utopia->scrutinizeVariableName($this->name, $global ? [] : $local_vars);
		if($this->value === null)
		{
			$this->value = new NullStatement();
		}
		else if(gettype($this->value) == "string")
		{
			$this->value = $utopia->parseAndExecuteWithWritableLocalVars($this->value, $local_vars);
		}
		else if($this->execute || Utopia::isStatementExecutionSafe($this->value))
		{
			$this->value = $this->value->execute($utopia, $local_vars);
		}
		if(!$this->value instanceof VariableStatement)
		{
			throw new InvalidCodeException("Declaration expected variable, got ".get_class($this->value));
		}
	}
}
