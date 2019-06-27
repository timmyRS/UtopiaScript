<?php
namespace UtopiaScript\Statement;
use UtopiaScript\
{Exception\InvalidCodeException, Utopia};
abstract class NoParamStatement extends OnlyLiteralParamsStatement
{
	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return true;
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return true;
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars)
	{
		throw new InvalidCodeException(get_called_class()." doesn't accept parameters");
	}
}
