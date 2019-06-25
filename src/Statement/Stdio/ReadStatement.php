<?php
namespace UtopiaScript\Statement\Stdio;
use UtopiaScript\
{Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Statement\OneOptionalLiteralParamStatement, Statement\Statement, Utopia};
class ReadStatement extends OneOptionalLiteralParamStatement
{
	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 * @throws InvalidCodeException
	 * @throws InvalidEnvironmentException
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		if($utopia->input_stream === null)
		{
			throw new InvalidEnvironmentException("ReadStatement only works in Utopias with input streams");
		}
		$start = microtime(true);
		$val = Utopia::internalize(rtrim(fgets($utopia->input_stream), "\r\n"));
		$utopia->input_time += (microtime(true) - $start);
		if($this->arg !== null)
		{
			if(array_key_exists($this->arg, $local_vars))
			{
				$local_vars[$this->arg]->setValue($val);
			}
			else if(array_key_exists($this->arg, $utopia->vars))
			{
				$utopia->vars[$this->arg]->setValue($val);
			}
			else
			{
				throw new InvalidCodeException("Undefined variable: ".$this->arg);
			}
		}
		return $val;
	}
}
