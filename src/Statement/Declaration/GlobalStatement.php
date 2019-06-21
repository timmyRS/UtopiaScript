<?php
namespace UtopiaScript\Statement\Declaration;
final class GlobalStatement extends InitialDeclarationStatement
{
	function __construct()
	{
		parent::__construct(true, false);
	}
}
