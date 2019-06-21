<?php
namespace UtopiaScript\Statement\Declaration;
final class ConstStatement extends InitialDeclarationStatement
{
	function __construct()
	{
		parent::__construct(true, true);
	}
}
