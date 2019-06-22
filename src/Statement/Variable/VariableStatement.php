<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
abstract class VariableStatement extends Statement
{
	const ACTION_TO_STRING = -1;
	const ACTION_EQUALS = -100;
	const ACTION_STRICT_EQUALS = -101;
	const ACTION_NOT_EQUALS = -102;
	const ACTION_NOT_STRICT_EQUALS = -103;
	const ACTION_GREATER = -104;
	const ACTION_GREATER_OR_EQUALS = -105;
	const ACTION_LESS = -106;
	const ACTION_LESS_OR_EQUALS = -107;
	public $value;
	/**
	 * @var int $action
	 */
	public $action = 0;
	public $action_data = null;

	function __construct($value)
	{
		$this->value = $value;
	}

	abstract static function getType(): string;

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->action > -100 || $this->action_data["value"] !== null;
	}

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
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
		return false;
	}

	/**
	 * @param mixed $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if($this->_acceptValue($value))
		{
			throw new InvalidCodeException(get_called_class()." doesn't accept values in this context");
		}
	}

	/**
	 * @param mixed $value
	 * @return boolean
	 * @throws InvalidCodeException
	 */
	function _acceptValue($value)
	{
		if($this->action >= 0)
		{
			return true;
		}
		if($this->action < -99)
		{
			if($this->action_data["value"] === null)
			{
				$this->action_data["value"] = $value;
			}
			else
			{
				if($this->action_data["execute"])
				{
					$this->action_data["value"] .= " ".$value;
				}
				else
				{
					$this->action_data["value"] .= $value;
				}
			}
		}
		else
		{
			throw new InvalidCodeException(get_called_class()." doesn't accept values in this context");
		}
		return false;
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if($this->_acceptLiteral($literal))
		{
			throw new InvalidCodeException(get_called_class()." doesn't accept literals in this context");
		}
	}

	/**
	 * @param string $literal
	 * @return boolean
	 * @throws InvalidCodeException
	 */
	function _acceptLiteral(string $literal)
	{
		if($this->action > 0)
		{
			return true;
		}
		else
		{
			if($this->action == 0)
			{
				switch($literal)
				{
					case '=':
					case 'equals':
						$this->action = self::ACTION_EQUALS;
						break;
					case '==':
						$this->action = self::ACTION_STRICT_EQUALS;
						break;
					case '!=':
					case 'notequals':
					case 'not_equals':
						$this->action = self::ACTION_NOT_EQUALS;
						break;
					case '!==':
						$this->action = self::ACTION_NOT_STRICT_EQUALS;
						break;
					case '>':
						$this->action = self::ACTION_GREATER;
						break;
					case '>=':
					case '=>':
						$this->action = self::ACTION_GREATER_OR_EQUALS;
						break;
					case '<':
						$this->action = self::ACTION_LESS;
						break;
					case '<=':
					case '=<':
						$this->action = self::ACTION_LESS_OR_EQUALS;
						break;
					case 'to_string':
					case 'tostring':
					case 'as_string':
					case 'string':
					case 'str':
					case 's':
						$this->action = self::ACTION_TO_STRING;
						break;
					default:
						return true;
				}
				if($this->action <= -100)
				{
					$this->action_data = [
						"value" => null,
						"execute" => false
					];
				}
			}
			else
			{
				if($this->action < 0)
				{
					if($this->action < -99)
					{
						if($this->action_data["value"] === null)
						{
							$this->action_data["value"] = $literal;
						}
						else
						{
							$this->action_data["value"] .= " ".$literal;
						}
						$this->action_data["execute"] = true;
					}
					else
					{
						throw new InvalidCodeException(get_called_class()." doesn't accept literals in this context");
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement|null
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function _execute(Utopia $utopia, array $local_vars = [])
	{
		if($this->action < -99)
		{
			if($this->action_data["execute"])
			{
				$this->action_data["value"] = $utopia->parseAndExecute($this->action_data["value"], $local_vars);
			}
			$a0 = Utopia::externalize($this->value);
			$a1 = Utopia::externalize($this->action_data["value"]);
			if($utopia->debug)
			{
				$utopia->say("<cmp mode ".$this->action." ".$a0." to ".$a1.">");
			}
			$mode = $this->action;
			$this->action = 0;
			$this->action_data = null;
			switch($mode)
			{
				case self::ACTION_EQUALS:
					return new BooleanStatement($a0 == $a1);
				case self::ACTION_STRICT_EQUALS:
					return new BooleanStatement($a0 === $a1);
				case self::ACTION_NOT_EQUALS:
					return new BooleanStatement($a0 != $a1);
				case self::ACTION_NOT_STRICT_EQUALS:
					return new BooleanStatement($a0 !== $a1);
				case self::ACTION_GREATER:
					return new BooleanStatement($a0 > $a1);
				case self::ACTION_GREATER_OR_EQUALS:
					return new BooleanStatement($a0 >= $a1);
				case self::ACTION_LESS:
					return new BooleanStatement($a0 < $a1);
				case self::ACTION_LESS_OR_EQUALS:
					return new BooleanStatement($a0 <= $a1);
			}
		}
		else
		{
			if($this->action < 0)
			{
				$mode = $this->action;
				$this->action = 0;
				switch($mode)
				{
					case self::ACTION_TO_STRING:
						return new StringStatement($this->__toString());
				}
			}
		}
		return null;
	}

	abstract function __toString(): string;

	/**
	 * @return string
	 */
	function toLiteral()
	{
		return $this->__toString();
	}

	function externalize()
	{
		return $this->value;
	}
}
