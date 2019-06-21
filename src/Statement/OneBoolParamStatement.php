<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Variable\BoolStatement, Utopia};
abstract class OneBoolParamStatement extends OneValueParamStatement
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
		if(!$this->value instanceof BoolStatement)
		{
			throw new InvalidCodeException(get_called_class()." only accepts numbers");
		}
	}
}
