<?php
namespace UtopiaScript\Statement\Variable;
use UtopiaScript\
{Exception\InvalidCodeException, Statement\ConsistentArgsStatement, Statement\Statement, Utopia};
class FunctionDeclarationStatement extends ConsistentArgsStatement
{
	public $args = ["required" => [[]]];
	public $body = null;

	function __construct($first_arg_type = null)
	{
		if($first_arg_type !== null)
		{
			$this->args["required"][0]["type"] = $first_arg_type;
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
		$arr = $this->args[$this->getCurrentArgType()];
		return count($arr[count($arr) - 1]) == 0;
	}

	function getCurrentArgType()
	{
		return array_key_exists("optionals", $this->args) ? "optionals" : "required";
	}

	/**
	 * @param VariableStatement $value
	 * @throws InvalidCodeException
	 */
	function acceptValue(VariableStatement $value)
	{
		if(get_class($value) != StringStatement::class)
		{
			throw new InvalidCodeException("Function body can't be ".$value->getType());
		}
		$this->body = $value->value;
	}

	/**
	 * @param string $literal
	 * @throws InvalidCodeException
	 */
	function acceptLiteral(string $literal)
	{
		if(in_array($literal, [
			":",
			","
		]))
		{
			return;
		}
		$type = $this->getCurrentArgType();
		$arr = $this->args[$type];
		$i = count($arr) - 1;
		$arg = $arr[$i];
		if(in_array($literal, [
			"?",
			"optional",
			"optionals"
		]))
		{
			if(count($arg) != 0)
			{
				throw new InvalidCodeException("Unexpected token in function declaration: ".$literal);
			}
			unset($this->args[$type][$i]);
			$this->args["optionals"] = [[]];
			return;
		}
		if(!array_key_exists("type", $arg))
		{
			$literal_ = Utopia::getCanonicalType($literal);
			if($literal_ != null)
			{
				$this->args[$type][$i]["type"] = $literal_;
				return;
			}
			$this->args[$type][$i] = [
				"type" => "any_type",
				"name" => $literal
			];
		}
		else if(!array_key_exists("name", $arg))
		{
			$this->args[$type][$i]["name"] = $literal;
		}
		array_push($this->args[$type], []);
	}

	/**
	 * @param Utopia $utopia
	 * @param array $local_vars
	 * @return Statement
	 */
	function execute(Utopia $utopia, array &$local_vars = []): Statement
	{
		$type = $this->getCurrentArgType();
		unset($this->args[$type][count($this->args[$type]) - 1]);
		return new FunctionStatement($this->body, $this->args);
	}
}
