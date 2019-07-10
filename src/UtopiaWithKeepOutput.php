<?php
namespace UtopiaScript;
class UtopiaWithKeepOutput extends Utopia
{
	public $last_output;
	public $last_error_output;
	public $last_combined_output;

	function __construct($input_stream = null)
	{
		parent::__construct($input_stream, function($str)
		{
			$this->last_output .= $str;
			$this->last_combined_output .= $str;
		}, function($str)
		{
			$this->last_error_output .= $str;
			$this->last_combined_output .= $str;
		});
		$this->reset_handler = function()
		{
			$this->last_output = "";
			$this->last_error_output = "";
			$this->last_combined_output = "";
		};
	}
}
