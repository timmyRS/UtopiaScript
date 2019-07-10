<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\InvalidCodeException, Exception\InvalidTypeException, Statement\ConsistentParamsStatement, Statement\Statement, Utopia};
class FunctionDeclarationStatement extends ConsistentParamsStatement
{
	public $params = ["required" => [[]]];
	public $body = null;

	function __construct($first_param_type = null)
	{
		if($first_param_type !== null)
		{
			$this->params["required"][0]["type"] = $first_param_type;
		}
	}

	/**
	 * Returns true if the statement can be executed.
	 * This would be false, e.g. if not enough parameters have been provided.
	 *
	 * @return boolean
	 */
	function isExecutable(): bool
	{
		return $this->body !== null;
	}

	/**
	 * Returns true if the statement accepts value. If false, everything will be treated as literal.
	 *
	 * @return boolean
	 */
	function acceptsValues(): bool
	{
		$arr = $this->params[$this->getCurrentparamType()];
		return count($arr[count($arr) - 1]) == 0;
	}

	function getCurrentparamType()
	{
		return array_key_exists("optionals", $this->params) ? "optionals" : "required";
	}

	/**
	 * @param VariableStatement $value
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidTypeException
	 */
	function acceptValue(VariableStatement $value, Utopia &$utopia, array &$local_vars)
	{
		if(get_class($value) != StringStatement::class)
		{
			throw new InvalidTypeException("Function body can't be ".$value->getType());
		}
		$this->body = $value->value;
	}

	/**
	 * @param string $literal
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal, Utopia &$utopia, array &$local_vars)
	{
		if(in_array($literal, [
			":",
			","
		]))
		{
			return;
		}
		$type = $this->getCurrentparamType();
		$arr = $this->params[$type];
		$i = count($arr) - 1;
		$param = $arr[$i];
		if(in_array($literal, [
			"?",
			"optional",
			"optionals"
		]))
		{
			if(count($param) != 0)
			{
				throw new InvalidCodeException("Unexpected token in function declaration: ".$literal);
			}
			unset($this->params[$type][$i]);
			$this->params["optionals"] = [[]];
			return;
		}
		if(!array_key_exists("type", $param))
		{
			$literal_ = Utopia::getCanonicalType($literal);
			if($literal_ != null)
			{
				$this->params[$type][$i]["type"] = $literal_;
				return;
			}
			$this->params[$type][$i] = [
				"type" => "any_type",
				"name" => $literal
			];
		}
		else if(!array_key_exists("name", $param))
		{
			$this->params[$type][$i]["name"] = $literal;
		}
		array_push($this->params[$type], []);
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia &$utopia, array &$local_vars = []): Statement
	{
		$type = $this->getCurrentparamType();
		unset($this->params[$type][count($this->params[$type]) - 1]);
		return new FunctionStatement($this->body, $this->params);
	}
}
