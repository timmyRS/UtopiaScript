<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Statement\Variable\VariableStatement, Utopia};
abstract class OneParamStatement extends ConsistentParamsStatement
{
	/**
	 * @var string|VariableStatement $arg
	 */
	public $arg = null;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->arg !== null;
	}

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
	{
		return true;
	}

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		$this->arg = $value;
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		$this->arg = $literal;
	}
}
