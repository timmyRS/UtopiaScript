<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Utopia};
class ExitStatement extends OneValueParamStatement
{
	function isExecutable(): bool
	{
		return true;
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return ExitStatement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		$this->_execute($utopia, $local_vars);
		return $this;
	}
}
