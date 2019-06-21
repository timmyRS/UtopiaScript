<?php
namespace UtopiaScriptDebugExtension;
use UtopiaScript\
{Statement\OneBoolParamStatement, Statement\Statement, Statement\Variable\NullStatement, Utopia};
final class DebugStatement extends OneBoolParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		$utopia->debug = Utopia::externalize($this->value);
		return new NullStatement();
	}
}
