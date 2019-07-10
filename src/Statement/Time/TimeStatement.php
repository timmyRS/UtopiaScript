<?php
namespace UtopiaScript\Statement\Time;
use UtopiaScript\
{Statement\NoParamStatement, Statement\Statement, Statement\Variable\NumberStatement, Utopia};
class TimeStatement extends NoParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		return new NumberStatement(microtime(true));
	}
}
