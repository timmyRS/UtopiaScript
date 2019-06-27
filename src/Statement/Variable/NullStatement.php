<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class NullStatement extends VariableStatement
{
	public $function = null;

	function __construct()
	{
		parent::__construct(null);
	}

	static function getType(): string
	{
		return "null";
	}

	function acceptValue(VariableStatement $value)
	{
		throw new InvalidCodeException("Null doesn't accept values or literals");
	}

	function acceptLiteral(string $literal)
	{
		throw new InvalidCodeException("Null doesn't accept literals or values");
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
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		return $this->_execute($utopia, $local_vars) ?? $this;
	}

	function __toString(): string
	{
		return "";
	}

	function toLiteral(): string
	{
		return "null";
	}
}
