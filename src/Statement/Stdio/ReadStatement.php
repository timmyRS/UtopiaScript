<?php
namespace UtopiaScript\Statement\Stdio;
use UtopiaScript\Exception\InvalidCodeException;
use UtopiaScript\Exception\InvalidEnvironmentException;
use UtopiaScript\Statement\OneOptionalLiteralParamStatement;
use UtopiaScript\Statement\Statement;
use UtopiaScript\Utopia;
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
				if($local_vars[$this->arg]->final)
				{
					throw new InvalidCodeException("Can't overwrite final: ".$this->arg);
				}
				$local_vars[$this->arg]->value = $val;
			}
			else
			{
				if(array_key_exists($this->arg, $utopia->vars))
				{
					if($utopia->vars[$this->arg]->final)
					{
						throw new InvalidCodeException("Can't overwrite const: ".$this->arg);
					}
					$utopia->vars[$this->arg]->value = $val;
				}
				else
				{
					throw new InvalidCodeException("Undefined variable: ".$this->arg);
				}
			}
		}
		return $val;
	}
}
