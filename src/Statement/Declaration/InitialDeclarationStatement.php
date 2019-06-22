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
		$this->_execute($utopia, $local_vars, $this->global);
		if($this->global)
		{
			$utopia->vars[$this->name] = new Variable($this->value, $this->final);
		}
		else
		{
			$local_vars[$this->name] = new Variable($this->value, $this->final);
		}
		return $this->value;
	}
}
