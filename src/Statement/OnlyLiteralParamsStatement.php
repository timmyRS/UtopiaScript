<?php
namespace UtopiaScript\Statement;
use UtopiaScript\Statement\Variable\VariableStatement;
use UtopiaScript\Utopia;
abstract class OnlyLiteralParamsStatement extends Statement
{
	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
	{
		return false;
	}

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	function acceptValue(VariableStatement $value, Utopia $utopia, array &$local_vars)
	{
	}
}
