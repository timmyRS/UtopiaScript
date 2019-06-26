<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Variable\NumberStatement, Statement\Variable\StringStatement, Statement\Variable\VariableStatement, Utopia};
abstract class OneStringParamStatement extends OneValueParamStatement
{
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
