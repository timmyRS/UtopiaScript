<?php
namespace UtopiaScriptPhpStatementExtension;
use UtopiaScript\Extension;
final class PhpStatementExtension extends Extension
{
	function getStatements(): array
	{
		return ["php" => PhpStatement::class];
	}
}
