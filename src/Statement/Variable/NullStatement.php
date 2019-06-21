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
		if($this->_acceptValue($value))
		{
			if($value instanceof StringStatement)
			{
				if($this->function === null)
				{
					$this->function = $value->value;
				}
				else
				{
					$this->function .= $value->value;
				}
			}
			else
			{
				throw new InvalidCodeException("NullStatement doesn't accept values in this context");
			}
		}
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
		$res = $this->_execute($utopia, $local_vars);
		if($res !== null)
		{
			return $res;
		}
		if($this->function !== null)
		{
			return new FunctionStatement($this->function);
		}
		return $this;
	}

	function __toString(): string
	{
		return "";
	}

	function toLiteral()
	{
		return "null";
	}
}
