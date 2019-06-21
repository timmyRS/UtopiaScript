<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Variable\NumberStatement, Statement\Variable\StringStatement, Statement\Variable\VariableStatement, Utopia};
abstract class OneStringParamStatement extends OneValueParamStatement
{
	/**
	 * @param VariableStatement $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if($this->value === null && $value instanceof NumberStatement)
		{
			$this->value = new StringStatement($value->__toString(), false);
		}
		else
		{
			parent::acceptValue($value);
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
	function _execute(Utopia $utopia, array &$local_vars = [])
	{
		parent::_execute($utopia, $local_vars);
		if(!$this->value instanceof StringStatement)
		{
			$this->value = new StringStatement($this->value->__toString(), false);
		}
	}
}
