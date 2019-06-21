<?php
namespace UtopiaScriptDebugExtension;
use UtopiaScript\
{Statement\OneValueParamStatement, Statement\Statement, Statement\Variable\NullStatement, Utopia};
final class DumpStatement extends OneValueParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		var_dump($this->value);
		return new NullStatement();
	}
}
