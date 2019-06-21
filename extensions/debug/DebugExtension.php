<?php
namespace UtopiaScriptDebugExtension;
use UtopiaScript\Extension;
/** An extension providing `debug <on|off>;` to toggle debug mode on-demand. */
final class DebugExtension extends Extension
{
	function getStatements(): array
	{
		return ["debug" => DebugStatement::class];
	}
}
