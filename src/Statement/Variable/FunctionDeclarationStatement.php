<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\Statement\Statement;
use UtopiaScript\Utopia;
class FunctionDeclarationStatement extends Statement
{
	public $required_args = [];
	public $optionals = false;
	public $optional_args = [];
	public $body = null;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->body !== null;
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		// TODO: Implement isFinished() method.
	}

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
	{
		// TODO: Implement acceptsValues() method.
	}

	/**
	 * @param VariableStatement $value
	 */
	function acceptValue(VariableStatement $value)
	{
		// TODO: Implement acceptValue() method.
	}

	/**
	 * @param string $literal
	 */
	function acceptLiteral(string $literal)
	{
		// TODO: Implement acceptLiteral() method.
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		// TODO: Implement execute() method.
	}
}
