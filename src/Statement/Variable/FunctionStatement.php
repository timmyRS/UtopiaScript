<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\IncompleteCodeException, Exception\InvalidCodeException, Exception\InvalidEnvironmentException, Exception\TimeoutException, Statement\Statement, Utopia, Variable};
class FunctionStatement extends VariableStatement
{
	public $params;
	public $args = [];

	function __construct(string $value, array $params = ["required" => []])
	{
		parent::__construct($value);
		if(!array_key_exists("optionals", $params))
		{
			$params["optionals"] = [[]];
		}
		$this->params = $params;
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
		return count($this->args) >= count($this->params["required"]);
	}

	/**
	 * Returns true if the statement accepts no more parameters.
	 *
	 * @return boolean
	 */
	function isFinished(): bool
	{
		return count($this->args) == (count($this->params["required"]) + count($this->params["optionals"]));
	}

	/**
	 * @param mixed $value
	 */
	function acceptValue(VariableStatement $value)
	{
		array_push($this->args, $value);
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
		$param_type = "required";
		$local_vars_ = [];
		foreach($this->args as $param_value)
		{
			if($param_type == "required" && count($this->params["required"]) == $i)
			{
				$param_type = "optionals";
				$i = 0;
			}
			$param = $this->params[$param_type][$i];
			$utopia->scrutinizeVariableName($param["name"]);
			assert($param_value instanceof VariableStatement);
			if($param["type"] != "any_type" && $param["type"] != $param_value->getType())
			{
				throw new InvalidCodeException("Parameter ".$param["name"]." has to be of type ".$param["type"]);
			}
			$local_vars_[$param["name"]] = new Variable($param_value, true);
			$i++;
		}
		return Utopia::unwrap($utopia->parseAndExecuteWithWritableLocalVars($this->value, $local_vars_), true);
	}

	function toLiteral(): string
	{
		return "(".$this->__toString().")";
	}

	function __toString(): string
	{
		$str = 'function ';
		if(count($this->params["required"]) > 0)
		{
			$str .= self::paramsToString($this->params["required"]);
			if(count($this->params["required"]) > 0)
			{
				$str .= ' optionals '.self::paramsToString($this->params["required"]);
			}
		}
		return $str.'{'.$this->value.'}';
	}

	static function paramsToString($params)
	{
		$str = '';
		foreach($params as $param)
		{
			$str .= $param["type"].' '.$param["name"].' ';
		}
		return $str;
	}
}
