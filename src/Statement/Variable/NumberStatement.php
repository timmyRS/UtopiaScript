<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class NumberStatement extends VariableStatement
{
	public $concatenation_string;

	static function getType(): string
	{
		return "number";
	}

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
				if($value instanceof StringStatement)
				{
					$this->concatenation_string = $value->value;
				}
				else
				{
					throw new InvalidCodeException("NumberStatement doesn't accept ".get_class($value));
				}
			}
			else
			{
				$this->finishAction($value);
			}
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
				throw new InvalidCodeException("Invalid action: ".$this->action);
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
				case 'plus':
					$this->action = 1;
					break;
				case '-':
				case 'minus':
					$this->action = 2;
					break;
				case '*':
				case 'times':
					$this->action = 3;
					break;
				case '/':
				case 'divided_by':
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
				case 'fact':
				case 'factorial':
					$this->value = self::factorial($this->value);
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
		if($this->action == self::ACTION_NOT)
		{
			$this->value = self::factorial($this->value);
		}
		$res = $this->_execute($utopia, $local_vars);
		if($res !== null)
		{
			return $res;
		}
		if($this->concatenation_string !== null)
		{
			return new StringStatement($this->value.$this->concatenation_string);
		}
		return $this;
	}

	function __toString(): string
	{
		return strval($this->value);
	}
}
