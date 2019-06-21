<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class FunctionStatement extends VariableStatement
{
	public $provided_args = [];
	public $required_args = [];
	public $optional_args = [];

	function __construct(string $value)
	{
		parent::__construct($value);
	}

	static function getType(): string
	{
		return "function";
	}

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return count($this->provided_args) >= count($this->required_args);
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return count($this->provided_args) == (count($this->required_args) + count($this->optional_args));
	}

	/**
	 * @param mixed $value
	 */
	function acceptValue(VariableStatement $value)
	{
		array_push($this->provided_args, $value);
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
		$ret = $this->_execute($utopia, $local_vars);
		if($ret !== null)
		{
			return $ret;
		}
		// TODO: Implement function parameters
		return Utopia::unwrap($utopia->parseAndExecute($this->value), true);
	}

	function __toString(): string
	{
		return "void {".$this->value."}";
	}
}
