<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
class FunctionStatement extends VariableStatement
{
	public $args;
	public $provided_args = [];

	function __construct(string $value, array $args = ["required" => []])
	{
		parent::__construct($value);
		if(!array_key_exists("optionals", $args))
		{
			$args["optionals"] = [[]];
		}
		$this->args = $args;
	}

	static function getType(): string
	{
		return "function";
	}

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return count($this->provided_args) >= count($this->args["required"]);
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return count($this->provided_args) == (count($this->args["required"]) + count($this->args["optionals"]));
	}

	/**
	 * @param mixed $value
	 */
	function acceptValue(VariableStatement $value)
	{
		array_push($this->provided_args, $value);
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
		$i = 0;
		$arg_type = "required";
		$local_vars_ = [];
		foreach($this->provided_args as $arg_value)
		{
			if($arg_type == "required" && count($this->args["required"]) == $i)
			{
				$arg_type = "optionals";
				$i = 0;
			}
			$arg = $this->args[$arg_type][$i];
			$utopia->scrutinizeVariableName($arg["name"]);
			assert($arg_value instanceof VariableStatement);
			if($arg["type"] != "any_type" && $arg["type"] != $arg_value->getType())
			{
				throw new InvalidCodeException("Parameter ".$arg["name"]." has to be of type ".$arg["type"]);
			}
			$local_vars_[$arg["name"]] = new Variable($arg_value, true);
			$i++;
		}
		return Utopia::unwrap($utopia->parseAndExecuteWithWritableLocalVars($this->value, $local_vars_), true);
	}

	static function argsToString($args)
	{
		$str = '';
		foreach($args as $arg)
		{
			$str .= $arg["type"].' '.$arg["name"].' ';
		}
		return $str;
	}

	function __toString(): string
	{
		$str = 'function ';
		if(count($this->args["required"]) == 0)
		{
			$str .= 'void ';
		}
		else
		{
			$str .= self::argsToString($this->args["required"]);
			if(count($this->args["required"]) != 0)
			{
				$str .= ' optionals '.self::argsToString($this->args["required"]);
			}
		}
		return $str.'{'.$this->value.'}';
	}
}
