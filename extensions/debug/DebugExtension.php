<?php
namespace UtopiaScriptDebugExtension;
use UtopiaScript\Extension;
final class DebugExtension extends Extension
{
	function getStatements(): array
	{
		return [
			"debug" => DebugStatement::class,
			"dump" => DumpStatement::class,
			"vardump" => DumpStatement::class,
			"var_dump" => DumpStatement::class
		];
	}
}
