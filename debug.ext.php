<?php



class EXT_Debug
{
	//--------------------------------------------------------------------------

	private $_cookie_name = 'ff_debug';
	private $_status      = FALSE;

	//--------------------------------------------------------------------------

	public function __construct()
	{
		if ( ! empty($_COOKIE[$this->_cookie_name]))
		{
			$this->status(TRUE);
		}
	}

	//--------------------------------------------------------------------------

	public function render_log($status = NULL)
	{
		if ($this->status($status))
		{
			return sys::$lib->load->template(EXT_PATH . 'debug/templates/debug');
		}
	}

	//--------------------------------------------------------------------------

	public function status($status = NULL)
	{
		if ($status !== NULL)
		{
			setcookie($this->_cookie_name, $status, 0, '/');
			$_COOKIE[$this->_cookie_name] = $status;

			$this->_status = (bool)$status;
		}

		return $this->_status;
	}

	//--------------------------------------------------------------------------

}