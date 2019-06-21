<?php
namespace UtopiaScript\Statement\Declaration;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
abstract class InitialDeclarationStatement extends DeclarationStatement
{
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
		$this->_execute($utopia, $local_vars);
		if(array_key_exists($this->name, $utopia->statements))
		{
			throw new InvalidCodeException("Can't overwrite statement: ".$this->name);
		}
		if($this->global)
		{
			$utopia->vars[$this->name] = new Variable($this->value, $this->final);
		}
		else
		{
			if(array_key_exists($this->name, $local_vars) && $local_vars[$this->name]->final)
			{
				throw new InvalidCodeException("Can't overwrite final: ".$this->name);
			}
			$local_vars[$this->name] = new Variable($this->value, $this->final);
		}
		return $this->value;
	}
}
