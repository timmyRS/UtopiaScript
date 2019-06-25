<?php
namespace UtopiaScript;
use UtopiaScript\
{Exception\InvalidCodeException, Statement\Variable\VariableStatement};
class Variable
{
	/**
	 * @var string $strict_type
	 */
	public $strict_type;
	/**
	 * @var VariableStatement $value
	 */
	public $value;
	/**
	 * @var boolean $final
	 */
	public $final;

	/**
	 * @param VariableStatement $value
	 * @param boolean $final
	 * @param string $strict_type
	 * @throws InvalidCodeException
	 */
	function __construct(VariableStatement $value, bool $final = false, string $strict_type = "any_type")
	{
		$this->strict_type = $strict_type;
		$this->setValue($value);
		$this->final = $final;
	}

	/**
	 * @param VariableStatement $value
	 * @throws InvalidCodeException
	 */
	function setValue(VariableStatement $value)
	{
		if($this->strict_type != "any_type" && $value->getType() != $this->strict_type)
		{
			throw new InvalidCodeException("Can't assign ".$value->getType()." to a variable that has to be of type ".$this->strict_type);
		}
		$this->value = $value;
	}
}
