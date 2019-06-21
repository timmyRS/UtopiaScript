<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class NumberStatement extends VariableStatement
{
	/**
	 * @param mixed $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if($this->_acceptValue($value))
		{
			if($this->action == 0)
			{
				throw new InvalidCodeException("NumberStatement doesn't accept values in this context");
			}
			$this->finishAction($value);
		}
	}

	/**
	 * @param $action_value
	 * @throws InvalidCodeException
	 */
	private function finishAction($action_value)
	{
		if(!Utopia::is_numeric($action_value))
		{
			throw new InvalidCodeException("Expected number, got ".$action_value);
		}
		$action_value = Utopia::numval($action_value);
		switch($this->action)
		{
			case 1:
				$this->value += $action_value;
				break;
			case 2:
				$this->value -= $action_value;
				break;
			case 3:
				$this->value *= $action_value;
				break;
			case 4:
				$this->value /= $action_value;
				break;
			case 5:
				$this->value = pow($this->value, $action_value);
				break;
			case 6:
				$this->value = $this->value % $action_value;
				break;
			default:
				throw new InvalidCodeException("Invalid action: ".$action_value);
		}
		$this->action = 0;
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if($this->_acceptLiteral($literal))
		{
			if($this->action > 0)
			{
				$this->finishAction(Utopia::numval($literal));
			}
			else switch($literal)
			{
				case '+':
					$this->action = 1;
					break;
				case '-':
					$this->action = 2;
					break;
				case '*':
					$this->action = 3;
					break;
				case '/':
					$this->action = 4;
					break;
				case '^':
				case 'pow':
					$this->action = 5;
					break;
				case '%':
				case 'mod':
					$this->action = 6;
					break;
				case 'c':
				case 'ceil':
					$this->value = ceil($this->value);
					break;
				case 'r':
				case 'round':
					$this->value = round($this->value);
					break;
				case 'f':
				case 'floor':
					$this->value = floor($this->value);
					break;
				case '!':
				case 'factorial':
					$this->value = self::factorial($this->value);
					break;
				default:
					throw new InvalidCodeException("Invalid action: ".$literal);
			}
		}
	}

	private static function factorial($number)
	{
		return $number < 1 ? 1 : $number * self::factorial($number - 1);
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
		return $ret === null ? $this : $ret;
	}

	function __toString(): string
	{
		return strval($this->value);
	}

	static function getType() : string
	{
		return "number";
	}
}
