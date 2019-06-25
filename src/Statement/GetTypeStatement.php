<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Statement\Variable\StringStatement, Statement\Variable\VariableStatement, Utopia};
class GetTypeStatement extends OneParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		return new StringStatement($this->arg instanceof VariableStatement ? $this->arg->getType() : (array_key_exists($this->arg, $utopia->statements) ? "statement" : "undefined"), false);
	}
}
