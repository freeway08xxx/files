<?php

/**
 * The Api Access Controller.
 */
class Controller_Api_Nav extends Controller_Api_Base
{
	public function before() {
		parent::before();
	}

	public function after($response) {
		return parent::after($response);
	}

	// グローバルナビの展開処理
	public function get_globalNav() {
		/*
		Config::load('gnav');
		$nav_arr = Config::get('gnav');
		 */
		$gnavi = Model_Data_Gnavi::get(Session::get('role_id_sem'));
		$this->response($gnavi);
	}
}
