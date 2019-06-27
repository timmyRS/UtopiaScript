<?php
namespace UtopiaScript\Statement\Conditional;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\OnlyLiteralParamsStatement, Statement\Statement, Statement\Variable\BooleanStatement, Utopia};
abstract class ConditionalStatement extends OnlyLiteralParamsStatement
{
	public $condition;
	public $result;
	public $else = false;
	public $inverted_result;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->condition !== null && $this->result !== null && ($this->else == false || $this->inverted_result !== null);
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return false;
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 */
	function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars)
	{
		if($this->else)
		{
			$this->inverted_result = $literal;
		}
		else
		{
			if($this->result !== null)
			{
				if($this->condition === null)
				{
					$this->condition = $this->result;
				}
				else
				{
					if($literal == "else" || $literal == "otherwise" || $literal == "|")
					{
						$this->else = true;
						return;
					}
					else
					{
						$this->condition .= " ".$this->result;
					}
				}
			}
			$this->result = $literal;
		}
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return boolean
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function isConditionTrue(Utopia $utopia, array &$local_vars = [])
	{
		if($this->condition instanceof BooleanStatement)
		{
			if(!$this->condition->value)
			{
				return false;
			}
		}
		else
		{
			if(is_bool($this->condition))
			{
				if(!$this->condition)
				{
					return false;
				}
			}
			else
			{
				$ret = Utopia::externalize($utopia->parseAndExecuteWithWritableLocalVars($this->condition, $local_vars));
				if(!is_bool($ret))
				{
					throw new InvalidCodeException("Condition returned ".gettype($ret).", expected boolean");
				}
				if(!$ret)
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return BooleanStatement|Statement
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function false(Utopia $utopia, array &$local_vars = [])
	{
		return $this->inverted_result ? $utopia->parseAndExecute($this->inverted_result, $local_vars) : new BooleanStatement(false);
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
	function _execute(Utopia $utopia, array &$local_vars = [])
	{
		return $utopia->parseAndExecute($this->result, $local_vars);
	}
}
