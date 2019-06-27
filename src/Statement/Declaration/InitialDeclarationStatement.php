<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
abstract class InitialDeclarationStatement extends DeclarationStatement
{
	/**
	 * @var string $strict_type
	 */
	public $strict_type = "any_type";
	/**
	 * @var boolean $global
	 */
	public $global;
	/**
	 * @var boolean $final
	 */
	public $final;

	function __construct(bool $global, bool $final)
	{
		$this->global = $global;
		$this->final = $final;
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia $utopia, array &$local_vars)
	{
		if($this->name === null)
		{
			$type = Utopia::getCanonicalType($literal);
			if($type !== null)
			{
				$this->strict_type = $type;
				return;
			}
		}
		parent::acceptLiteral($literal, $utopia, $local_vars);
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 * @throws InvalidCodeException
	 * @throws IncompleteCodeException
	 * @throws InvalidEnvironmentException
	 * @throws TimeoutException
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		$utopia->scrutinizeVariableName($this->name, $this->global ? [] : $local_vars);
		$this->_execute($utopia, $local_vars);
		if($this->global)
		{
			if($this->strict_type != "any_type" && array_key_exists($this->name, $utopia->vars) && $utopia->vars[$this->name]->strict_type != "any_type" && $utopia->vars[$this->name]->strict_type != $this->strict_type)
			{
				throw new InvalidCodeException($this->name." is already bound to be ".$utopia->vars[$this->name]->strict_type.", so it can't/shouldn't be changed to ".$this->strict_type);
			}
			$utopia->vars[$this->name] = new Variable($this->value, $this->final, $this->strict_type);
		}
		else
		{
			if($this->strict_type != "any_type" && array_key_exists($this->name, $local_vars) && $local_vars[$this->name]->strict_type != "any_type" && $local_vars[$this->name]->strict_type != $this->strict_type)
			{
				throw new InvalidCodeException($this->name." is already bound to be ".$local_vars[$this->name]->strict_type.", so it can't/shouldn't be changed to ".$this->strict_type);
			}
			$local_vars[$this->name] = new Variable($this->value, $this->final, $this->strict_type);
		}
		return $this->value;
	}
}
