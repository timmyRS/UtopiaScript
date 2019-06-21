<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia};
class StringStatement extends VariableStatement
{
	/**
	 * @var boolean $exec
	 */
	public $exec;

	function __construct(string $value, bool $exec = true)
	{
		parent::__construct($value);
		$this->exec = $exec;
	}

	/**
	 * @param mixed $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if($this->_acceptValue($value))
		{
			$this->value .= $value;
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
			$this->exec = false;
			switch($literal)
			{
				case '^':
				case 'upper':
				case 'uppercase':
				case 'toupper':
				case 'to_uppercase':
				case 'touppercase':
					$this->value = strtoupper($this->value);
					break;
				case 'v':
				case 'lower':
				case 'lowercase':
				case 'tolower':
				case 'to_lowercase':
				case 'tolowercase':
					$this->value = strtolower($this->value);
					break;
				default:
					throw new InvalidCodeException("Invalid action: ".$literal);
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
		$ret = $this->_execute($utopia, $local_vars);
		if($ret !== null)
		{
			return $ret;
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

	function toLiteral()
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

	static function getType() : string
	{
		return "string";
	}
}
