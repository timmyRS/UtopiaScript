<?php
namespace UtopiaScript\Statement\Declaration;
final class FinalStatement extends InitialDeclarationStatement
{
	function __construct()
	{
		parent::__construct(false, true);
	}
}
