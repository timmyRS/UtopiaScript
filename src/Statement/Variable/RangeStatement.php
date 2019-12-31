<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\InvalidCodeException, Exception\InvalidTypeException, Statement\ConsistentParamsStatement, Statement\Statement, Utopia};
class RangeStatement extends ConsistentParamsStatement
{
	public $from;
	public $to;

	function isExecutable(): bool
	{
		return $this->to !== null;
	}

	function acceptsValues(): bool
	{
		return true;
	}

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		if($value->getType() != "string" && $value->getType() != "number")
		{
			throw new InvalidCodeException("Unexpected ".$value->getType()." as argument for RangeStatement");
		}
		if($this->from === null)
		{
			$this->from = $value->value;
		}
		else
		{
			$this->to = $value->value;
		}
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		if(($this->from !== null && $literal == "from") || ($this->to !== null && ($literal == "to" || $literal == "-")))
		{
			throw new InvalidCodeException("Unexpected literal '$literal' in RangeStatement");
		}
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return ArrayStatement
	 * @throws InvalidTypeException
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		if((is_string($this->from) ? 1 : 0) ^ (is_string($this->to) ? 1 : 0) == 1)
		{
			throw new InvalidTypeException("Array range has to be either string-string or number-number.");
		}
		$arr = [];
		if(is_string($this->from))
		{
			foreach(range($this->from, $this->to) as $item)
			{
				array_push($arr, new StringStatement($item));
			}
		}
		else
		{
			if(!is_numeric($this->from) || !is_numeric($this->to))
			{
				throw new InvalidTypeException("Array range has to be either string-string or number-number.");
			}
			foreach(range($this->from, $this->to) as $item)
			{
				array_push($arr, new NumberStatement($item));
			}
		}
		return new ArrayStatement($arr);
	}
}
