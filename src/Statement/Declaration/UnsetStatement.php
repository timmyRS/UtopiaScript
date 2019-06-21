<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\InvalidCodeException, Statement\OneLiteralParamStatement, Statement\Statement, Statement\Variable\NullStatement, Utopia};
class UnsetStatement extends OneLiteralParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 * @throws InvalidCodeException
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		if(array_key_exists($this->arg, $local_vars))
		{
			if($local_vars[$this->arg]->final)
			{
				throw new InvalidCodeException("Can't unset final: ".$this->arg);
			}
			unset($local_vars[$this->arg]);
		}
		else
		{
			if(array_key_exists($this->arg, $utopia->vars))
			{
				if($utopia->vars[$this->arg]->final)
				{
					throw new InvalidCodeException("Can't unset const: ".$this->arg);
				}
				unset($utopia->vars[$this->arg]);
			}
			else
			{
				throw new InvalidCodeException("Undefined variable: ".$this->arg);
			}
		}
		return new NullStatement();
	}
}
