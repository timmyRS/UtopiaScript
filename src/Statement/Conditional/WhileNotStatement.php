<?php
namespace UtopiaScript\Statement\Conditional;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
final class WhileNotStatement extends ConditionalStatement
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
		if(!$this->isConditionTrue($utopia, $local_vars))
		{
			do
			{
				$ret = $this->_execute($utopia, $local_vars);
			}
			while(!$this->isConditionTrue($utopia, $local_vars));
			return $ret;
		}
		return $this->false($utopia, $local_vars);
	}
}
