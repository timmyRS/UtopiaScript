<?php
namespace UtopiaScript\Statement\Stdio;
final class PrintErrorStatement extends PrintStatement
{
	function __construct()
	{
		parent::__construct(false, true);
	}
}
