<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class StringStatement extends VariableStatement
{
	const ACTION_CONCAT_FUNC = 1;
	/**
	 * @var boolean $exec
	 */
	public $exec;

	function __construct(string $value, bool $exec = true)
	{
		parent::__construct($value);
		$this->exec = $exec;
	}

	static function getType(): string
	{
		return "string";
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		$this->exec = false;
		if($this->_acceptLiteral($literal))
		{
			if($this->action == self::ACTION_CONCAT_FUNC)
			{
				$this->action_data->acceptLiteral($literal, $utopia, $local_vars);
				if($this->action_data->isFinished())
				{
					$this->action = 0;
					$this->acceptValue($this->action_data->execute($utopia, $local_vars), $utopia, $local_vars);
					$this->action_data = null;
				}
			}
			else
			{
				switch($literal)
				{
					case '^':
					case 'upper':
					case 'uppercase':
					case 'toupper':
					case 'to_uppercase':
					case 'to_upper_case':
					case 'touppercase':
						$this->value = strtoupper($this->value);
						break;
					case 'v':
					case 'lower':
					case 'lowercase':
					case 'tolower':
					case 'to_lowercase':
					case 'to_lower_case':
					case 'tolowercase':
						$this->value = strtolower($this->value);
						break;
					default:
						throw new InvalidCodeException("Invalid action: ".$literal);
				}
			}
		}
	}

	/**
	 * @param mixed $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws IncompleteCodeException
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		if($this->_acceptValue($value))
		{
			if($this->action == self::ACTION_CONCAT_FUNC)
			{
				$this->action_data->acceptValue($value, $utopia, $local_vars);
				if($this->action_data->isFinished())
				{
					$this->action = 0;
					$this->acceptValue($this->action_data->execute($utopia, $local_vars), $utopia, $local_vars);
					$this->action_data = null;
				}
			}
			else if($value instanceof FunctionStatement)
			{
				if($value->isFinished())
				{
					$this->acceptValue($value->execute($utopia, $local_vars), $utopia, $local_vars);
				}
				else
				{
					$this->action = self::ACTION_CONCAT_FUNC;
					$this->action_data = $value;
				}
			}
			else
			{
				$this->value .= $value;
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
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		$ret = $this->_execute($utopia, $local_vars);
		if($ret !== null)
		{
			return $ret;
		}
		if($this->action == self::ACTION_CONCAT_FUNC)
		{
			if(!$this->action_data->isExecutable())
			{
				throw new IncompleteCodeException("String concatenation with function that is not executable");
			}
			$this->action = 0;
			$this->acceptValue($this->action_data->execute($utopia, $local_vars), $utopia, $local_vars);
			$this->action_data = null;
		}
		if($this->exec)
		{
			return Utopia::unwrap($utopia->parseAndExecute($this->value, $local_vars), true);
		}
		return $this;
	}

	function __toString(): string
	{
		return $this->value;
	}

	function toLiteral(): string
	{
		if(strpos($this->value, '"') === false)
		{
			return '"'.$this->value.'"';
		}
		if(strpos($this->value, '\'') === false)
		{
			return '\''.$this->value.'\'';
		}
		if(strpos($this->value, '`') === false)
		{
			return '`'.$this->value.'`';
		}
		return "{".$this->value."}";
	}
}
