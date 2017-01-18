<?php

/**
 * The Welcome Controller.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Base extends Controller_Hybrid
{
	public $css           = array();
	public $js            = array();
	public $alert_message = '';
	public $admin_flg     = '';
	public $content_nav   = ''; // title横 コンテンツナビゲーション
	public $app_const 	  = array();

	public $is_access_free = false; // 権限チェックしない機能用

	public $user_id = '';

	// 前提共通処理
	public function before()
	{
		logger(DEBUG, "controller:[".Request::active()->controller."] action:[".Request::active()->action."]", 'START');

		## Viewのインスタンス
		$this->view = View::forge();

		if ($this->is_access_free) {
			$this->admin_flg = true;
		} else {
			## 全ログインチェック
			$this->admin_flg = \Util_Common_Login::check_user_access();
		}

		$this->user_id = Session::get('user_id_sem');

		// super
		parent::before();
	}

	// 後共通処理
	public function after($response)
	{
		logger(DEBUG, "controller:[".Request::active()->controller."] action:[".Request::active()->action."]", 'END');

		if ( ! $this->is_restful()) {
			$this->template->set_global('admin_flg', $this->admin_flg);
			$this->template->set_global('alert_message', $this->alert_message);

			$this->template->css = $this->css;
			$this->template->js  = $this->js;

			$this->template->content_nav = $this->content_nav;
			$this->template->content     = $this->view;

			$this->template->set_global('ss_const', $this->_get_ss_const());
			$this->template->set_global('app_const', $this->app_const);
		}

		// super
		return parent::after($response);
	}

	private function _get_ss_const() {
		return array(
			'user' => array(
				'id' => Session::get('user_id_sem'),	
				'user_name' => Session::get('user_name'),	
				'role_id' => Session::get('role_id_sem'), 
			)
		);
	}
}
