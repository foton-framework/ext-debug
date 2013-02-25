<?php


class COM_Debug extends SYS_Component
{
	//--------------------------------------------------------------------------

	function act_enable()
	{
		$this->_debug_status(TRUE);
	}

	//--------------------------------------------------------------------------

	function act_disable()
	{
		$this->_debug_status(FALSE);
	}

	//--------------------------------------------------------------------------

	function _debug_status($status)
	{
		$this->view = FALSE;
		$this->debug->status($status);
		$back_url = empty($_SERVER['HTTP_REFERER']) ? '/' : $_SERVER['HTTP_REFERER'];
		ob_get_level() && ob_clean() && header("Location: {$back_url}");
	}
}