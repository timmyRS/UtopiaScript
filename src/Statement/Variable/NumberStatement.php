<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class NumberStatement extends VariableStatement
{
	const ACTION_ADD = 1;
	const ACTION_SUB = 2;
	const ACTION_MUL = 3;
	const ACTION_DIV = 4;
	const ACTION_POW = 5;
	const ACTION_MOD = 6;
	const ACTION_CONCAT = 7;
	const ACTION_FACTORIAL = 100;
	const ACTION_FLOOR = 101;
	const ACTION_ROUND = 102;
	const ACTION_CEIL = 103;

	static function getType(): string
	{
		return "number";
	}

	function isFinished(): bool
	{
		return $this->action != 0 && $this->isExecutable();
	}

	function isExecutable(): bool
	{
		return ($this->action > 0 && ($this->action > 99 || $this->action_data["b"] !== null)) || ($this->action <= 0 && parent::isExecutable());
	}

	/**
	 * @param mixed $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value, Utopia $utopia, array &$local_vars)
	{
		if($this->_acceptValue($value))
		{
			if($this->action == 0)
			{
				if($value instanceof StringStatement)
				{
					$this->action = self::ACTION_CONCAT;
					$this->action_data["b"] = $value->value;
					return;
				}
			}
			else if($this->action < 100 && ($value instanceof NumberStatement || $value instanceof ArrayStatement))
			{
				$this->action_data["b"] = $value->value;
				return;
			}
			throw new InvalidCodeException("Number doesn't accept ".$value->getType()." in this context");
		}
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars)
	{
		if($this->_acceptLiteral($literal))
		{
			if($this->action != 0)
			{
				throw new InvalidCodeException("Number doesn't accept literals in this context");
			}
			switch($literal)
			{
				case '+':
				case 'plus':
					$this->action = self::ACTION_ADD;
					break;
				case '-':
				case 'minus':
					$this->action = self::ACTION_SUB;
					break;
				case '*':
				case 'times':
					$this->action = self::ACTION_MUL;
					break;
				case '/':
				case 'divided_by':
					$this->action = self::ACTION_DIV;
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
		switch($this->action)
		{
			case self::ACTION_ADD:
				return self::add($this->value, $this->action_data["b"]);
			case self::ACTION_SUB:
				return self::sub($this->value, $this->action_data["b"]);
			case self::ACTION_MUL:
				return self::mul($this->value, $this->action_data["b"]);
			case self::ACTION_DIV:
				return self::div($this->value, $this->action_data["b"]);
			case self::ACTION_POW:
				$this->value **= $this->action_data["b"];
				break;
			case self::ACTION_MOD:
				$this->value %= $this->action_data["b"];
				break;
			case self::ACTION_CONCAT:
				return new StringStatement($this->value.$this->action_data["b"]);
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

	/**
	 * @param $a
	 * @param $b
	 * @return ArrayStatement|NumberStatement
	 * @throws InvalidCodeException
	 */
	static function add($a, $b)
	{
		return self::performArithmetic($a, $b, function(&$a, &$b)
		{
			return $a + $b;
		});
	}

	/**
	 * @param int|array $a
	 * @param int|array $b
	 * @param callable $f
	 * @return ArrayStatement|NumberStatement
	 * @throws InvalidCodeException
	 */
	static function performArithmetic(&$a, &$b, $f)
	{
		if(is_array($a))
		{
			if(is_array($b))
			{
				// array a, array b
				if(count($a) != count($b))
				{
					throw new InvalidCodeException("Can't perform arithmetic operations on arrays of different sizes");
				}
				return new ArrayStatement(array_map(function($a, $b) use (&$f)
				{
					if(!$a instanceof NumberStatement || !$b instanceof NumberStatement)
					{
						throw new InvalidCodeException("Can't perform arithmetic operations on arrays containing non-numbers");
					}
					return new NumberStatement($f($a->value, $b->value));
				}, $a, $b));
			}
			// array a, int b
			return new ArrayStatement(array_map(function($a) use (&$b, &$f)
			{
				if(!$a instanceof NumberStatement)
				{
					throw new InvalidCodeException("Can't perform arithmetic operations on arrays containing non-numbers");
				}
				return new NumberStatement($f($a->value, $b));
			}, $a));
		}
		if(is_array($b))
		{
			// int a, array b
			return new ArrayStatement(array_map(function($b) use (&$a, &$f)
			{
				if(!$b instanceof NumberStatement)
				{
					throw new InvalidCodeException("Can't perform arithmetic operations on arrays containing non-numbers");
				}
				return new NumberStatement($f($a, $b->value));
			}, $b));
		}
		// int a, int b
		return new NumberStatement($f($a, $b));
	}

	/**
	 * @param $a
	 * @param $b
	 * @return ArrayStatement|NumberStatement
	 * @throws InvalidCodeException
	 */
	static function sub($a, $b)
	{
		return self::performArithmetic($a, $b, function(&$a, &$b)
		{
			return $a - $b;
		});
	}

	/**
	 * @param $a
	 * @param $b
	 * @return ArrayStatement|NumberStatement
	 * @throws InvalidCodeException
	 */
	static function mul($a, $b)
	{
		return self::performArithmetic($a, $b, function(&$a, &$b)
		{
			return $a * $b;
		});
	}

	/**
	 * @param $a
	 * @param $b
	 * @return ArrayStatement|NumberStatement
	 * @throws InvalidCodeException
	 */
	static function div($a, $b)
	{
		return self::performArithmetic($a, $b, function(&$a, &$b)
		{
			return $a / $b;
		});
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
