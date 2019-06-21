<?php
namespace UtopiaScript\Statement;
use UtopiaScript\Statement\Variable\VariableStatement;
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
	 */
	function acceptValue(VariableStatement $value)
	{
	}
}
