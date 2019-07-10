<?php
namespace UtopiaScript\Statement\Variable\Action;
use UtopiaScript\
{Statement\OneNumberParamStatement, Statement\Statement, Statement\Variable\NumberStatement, Utopia};
class FloorStatement extends OneNumberParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		return new NumberStatement(floor(Utopia::externalize($this->value)));
	}
}
