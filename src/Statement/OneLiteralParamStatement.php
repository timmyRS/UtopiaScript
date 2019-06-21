<?php
namespace UtopiaScript\Statement;
abstract class OneLiteralParamStatement extends OnlyLiteralParamsStatement
{
	/**
	 * @var string $arg
	 */
	public $arg;

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
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return $this->arg !== null;
	}

	/**
	 * @param string $literal
	 */
	function acceptLiteral(string $literal)
	{
		$this->arg = $literal;
	}
}
