<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class BooleanStatement extends VariableStatement
{
	function __construct(bool $value)
	{
		parent::__construct($value);
	}

	static function getType(): string
	{
		return "boolean";
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		return $this->_execute($utopia, $local_vars) ?? $this;
	}

	function __toString(): string
	{
		return $this->value ? "true" : "false";
	}
}
