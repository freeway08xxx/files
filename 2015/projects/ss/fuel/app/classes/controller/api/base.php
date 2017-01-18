<?php

class Controller_Api_Base extends Controller_Base
{
	// ======================================================================
	// 以下共通コントローラーへ移動
	// ======================================================================

	public $is_access_free = true;

	public function before()
	{
		parent::before();
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
