<?php
/**
 * The Api Access Controller.
 */
class Controller_Api_Client extends Controller_Api_Base
{

	public function before() {
		parent::before();
	}

	public function after($response) {
		return parent::after($response);
	}

	// 担当クライアントを全件取得
	public function get_all() {

		$clients = Util_Common_Client::get_clients_by_owner (Session::get('user_id_sem'), Session::get('role_id_sem'));
		$response['clients'] = array_values($clients);
		return $this->response($response); 
	}
}
