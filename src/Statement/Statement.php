<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Statement\Variable\VariableStatement, Utopia};
abstract class Statement
{
	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	abstract function isExecutable(): bool;

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	abstract function isFinished(): bool;

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	abstract function acceptsValues(): bool;

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	abstract function acceptValue(VariableStatement $value, Utopia $utopia, array &$local_vars);

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	abstract function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars);

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	abstract function execute(Utopia $utopia, array &$local_vars = []): Statement;
}
