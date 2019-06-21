<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
final class SetStatement extends DeclarationStatement
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
		if(array_key_exists($this->name, $local_vars))
		{
			if($local_vars[$this->name]->final)
			{
				throw new InvalidCodeException("Can't overwrite final: ".$this->name);
			}
			$local_vars[$this->name]->value = $this->value;
		}
		else
		{
			if(array_key_exists($this->name, $utopia->vars))
			{
				$utopia->vars[$this->name]->value = $this->value;
			}
			else
			{
				throw new InvalidCodeException("Undefined variable: ".$this->name);
			}
		}
		return $this->value;
	}
}
