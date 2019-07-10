<?php
namespace UtopiaScript\Statement\Stdio;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\OneStringParamStatement, Statement\Statement, Statement\Variable\StringStatement, Utopia};
class PrintStatement extends OneStringParamStatement
{
	/**
	 * @var boolean $line
	 */
	public $line;
	/**
	 * @var boolean $error
	 */
	public $error;

	function __construct(bool $line = false, bool $error = false)
	{
		$this->line = $line;
		$this->error = $error;
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
		$this->_execute($utopia, $local_vars);
		$str = Utopia::strval($this->value);
		if($this->line)
		{
			$str .= "\r\n";
		}
		$ret = new StringStatement($str);
		if($utopia->debug)
		{
			$str = "<output>$str</output>";
		}
		if($this->error)
		{
			$utopia->complain($str);
		}
		else
		{
			$utopia->say($str);
		}
		return $ret;
	}
}
