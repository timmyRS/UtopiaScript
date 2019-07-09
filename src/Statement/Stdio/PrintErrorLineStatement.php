<?php
namespace UtopiaScript\Statement\Stdio;
final class PrintErrorLineStatement extends PrintStatement
{
	function __construct()
	{
		parent::__construct(true, true);
	}
}
