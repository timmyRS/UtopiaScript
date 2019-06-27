<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class NumberStatement extends VariableStatement
{
	const ACTION_PLUS = 1;
	const ACTION_MINUS = 2;
	const ACTION_TIMES = 3;
	const ACTION_DIVIDED_BY = 4;
	const ACTION_POW = 5;
	const ACTION_MOD = 6;
	const ACTION_FACTORIAL = 100;
	const ACTION_FLOOR = 101;
	const ACTION_ROUND = 102;
	const ACTION_CEIL = 103;
	public $concatenation_string;

	static function getType(): string
	{
		return "number";
	}

	function isFinished(): bool
	{
		return $this->isExecutable();
	}

	function isExecutable(): bool
	{
		return $this->concatenation_string !== null || ($this->action > 0 && ($this->action > 99 || $this->action_data["b"] !== null)) || ($this->action <= 0 && parent::isExecutable());
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
					return;
				}
			}
			else if($this->action < 100 && $value instanceof NumberStatement)
			{
				$this->action_data["b"] = $value->value;
				return;
			}
			throw new InvalidCodeException("NumberStatement doesn't accept ".get_class($value)." in this context");
		}
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if($this->_acceptLiteral($literal))
		{
			if($this->action != 0)
			{
				throw new InvalidCodeException("NumberStatement doesn't accept literals in this context");
			}
			switch($literal)
			{
				case '+':
				case 'plus':
					$this->action = self::ACTION_PLUS;
					break;
				case '-':
				case 'minus':
					$this->action = self::ACTION_MINUS;
					break;
				case '*':
				case 'times':
					$this->action = self::ACTION_TIMES;
					break;
				case '/':
				case 'divided_by':
					$this->action = self::ACTION_DIVIDED_BY;
					break;
				case '^':
				case 'pow':
					$this->action = self::ACTION_POW;
					break;
				case '%':
				case 'mod':
					$this->action = self::ACTION_MOD;
					break;
				case 'fact':
				case 'factorial':
					$this->action = self::ACTION_FACTORIAL;
					break;
				case 'f':
				case 'floor':
					$this->action = self::ACTION_FLOOR;
					break;
				case 'r':
				case 'round':
					$this->action = self::ACTION_ROUND;
					break;
				case 'c':
				case 'ceil':
					$this->action = self::ACTION_CEIL;
					break;
				default:
					throw new InvalidCodeException("Invalid action: ".$literal);
			}
			if($this->action < 100)
			{
				$this->action_data = ["b" => null];
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
		if($this->action == self::ACTION_NOT)
		{
			$this->action = self::ACTION_FACTORIAL;
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
		switch($this->action)
		{
			case self::ACTION_PLUS:
				$this->value += $this->action_data["b"];
				break;
			case self::ACTION_MINUS:
				$this->value -= $this->action_data["b"];
				break;
			case self::ACTION_TIMES:
				$this->value *= $this->action_data["b"];
				break;
			case self::ACTION_DIVIDED_BY:
				$this->value /= $this->action_data["b"];
				break;
			case self::ACTION_POW:
				$this->value **= $this->action_data["b"];
				break;
			case self::ACTION_MOD:
				$this->value %= $this->action_data["b"];
				break;
			case self::ACTION_FACTORIAL:
				$this->value = self::factorial($this->value);
				break;
			case self::ACTION_FLOOR:
				$this->value = floor($this->value);
				break;
			case self::ACTION_ROUND:
				$this->value = round($this->value);
				break;
			case self::ACTION_CEIL:
				$this->value = ceil($this->value);
				break;
		}
		$this->action = 0;
		$this->action_data = [];
		return $this;
	}

	static function factorial(int $number): int
	{
		return $number < 1 ? 1 : $number * self::factorial($number - 1);
	}

	function __toString(): string
	{
		return strval($this->value);
	}
}
