<?php
namespace UtopiaScriptPhpStatementExtension;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\OneStringParamStatement, Statement\Statement, Utopia};
class PhpStatement extends OneStringParamStatement
{
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
		$this->_execute($utopia, $local_vars);
		return Utopia::internalize(eval(Utopia::externalize($this->value)));
	}
}
