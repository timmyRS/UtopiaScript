<?php
namespace UtopiaScript\Statement\Declaration;
final class LocalStatement extends InitialDeclarationStatement
{
	function __construct()
	{
		parent::__construct(false, false);
	}
}
