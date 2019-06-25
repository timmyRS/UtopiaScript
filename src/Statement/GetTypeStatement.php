<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Statement\Variable\StringStatement, Statement\Variable\VariableStatement, Utopia};
class GetTypeStatement extends ConsistentArgsStatement
{
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
	 */
	function acceptValue(VariableStatement $value)
	{
		$this->arg = $value;
	}

	/**
	 * @param string $literal
	 */
	function acceptLiteral(string $literal)
	{
		$this->arg = $literal;
	}

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
