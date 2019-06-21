<?php
namespace UtopiaScript\Statement\Stdio;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\OneStringParamStatement, Statement\Statement, Statement\Variable\StringStatement, Utopia};
class PrintStatement extends OneStringParamStatement
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
		$str = Utopia::strval($this->value);
		if($this instanceof PrintLineStatement)
		{
			$str .= "\r\n";
		}
		if($utopia->debug)
		{
			$utopia->say("<output>".$str."</output>");
		}
		else
		{
			$utopia->say($str);
		}
		return new StringStatement($str);
	}
}
