<?php

class Controller_Knowledge_Api_Base extends Controller_Knowledge_Base
{
	// ======================================================================
	// 以下共通コントローラーへ移動
	// ======================================================================

	public function before()
	{
		parent::before();
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
