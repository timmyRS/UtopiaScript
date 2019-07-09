<?php
namespace UtopiaScript\Statement\Stdio;
final class PrintLineStatement extends PrintStatement
{
	function __construct()
	{
		parent::__construct(true, false);
	}
}
