<?php
namespace UtopiaScript;
use UtopiaScript\Statement\Variable\VariableStatement;
class Variable
{
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
	 */
	function __construct(VariableStatement $value, bool $final = false)
	{
		$this->value = $value;
		$this->final = $final;
	}
}
