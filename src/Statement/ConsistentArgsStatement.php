<?php
namespace UtopiaScript\Statement;
abstract class ConsistentArgsStatement extends Statement
{
	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return $this->isExecutable();
	}
}
