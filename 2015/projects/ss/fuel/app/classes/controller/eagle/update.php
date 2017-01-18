<?php
require_once APPPATH . "/const/eagle.php";
require_once APPPATH . "/const/eagle/message.php";



/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Eagle_Update extends Controller_Eagle_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index() {
		$this->view->set_filename('eagle/update/index');
		$this->view->set_safe("status", View::forge('eagle/update/status/index'));
		$this->view->set_safe("cpc", View::forge('eagle/update/cpc/index'));

		$this->response($this->view);
	}

	/**
	 * 更新に必要情報の取得 
	 * 
	 * @param mixed $eagle_id 
	 * @access public
	 * @return void
	 */
	public function get_content($eagle_id) {
		if(empty($eagle_id)){
			return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
		}

		## eagle情報
		$eagle = Model_Data_Eagle::get_one($eagle_id);

		##スキップなどの利用によりupdated_atがない場合がある
		if(empty(!$eagle['updated_at'])){
			$is_sync_skip = false;
			$limit_target_date = $eagle['updated_at'];
		}else{
			$is_sync_skip = true;
			$limit_target_date = $eagle['created_at'];
		}

		## 表示可能かチェック
		if($eagle['status'] != EAGLE_UPDATE_STATUS_SUCCESS_SYNC){
			## すでに編集が終了していないか(ステータスが編集か)
			$error['message'] = ERROR_MESSAGE_UPDATE_STATUS;
		}
		elseif(!empty($limit_target_date) && strtotime('-'.UPDATE_LIMIT_HOUR." hour") > strtotime($limit_target_date)){
			## 最終掲載同期時刻から時間が経過していないか
			$error['message'] = ERROR_MESSAGE_UPDATE_TIMEOUT;
			## 経過している場合はステータスを変更してしまう
			Model_Data_Eagle::update_status($eagle_id, EAGLE_UPDATE_STATUS_ERROR_TIMEOUT);
		}
		elseif(Session::get('role_id_sem') != ROLE_ID_ADMIN && Session::get('user_id_sem') != $eagle['created_user']){
			## 登録ユーザーでない場合は弾く
			$error['message'] = ERROR_MESSAGE_UPDATE_PERMISSION;
		}

		## クライアント情報(ID,名前)
		$client = Model_Mora_Client::get_client_name($eagle['client_id']);
		if(!empty($client['client_name'])){
			$eagle['client_name'] = $client['company_name'].'::'.$client['client_name'];
		}else{
			$eagle['client_name'] = $client['company_name'];
		}

		## 掲載取得アカウント一覧取得
		$eagle_accounts = Model_Data_Eagle_Update_Account::find_by_eagle_id_join_account($eagle_id);

		$response['eagle'] = $eagle;
		$response['eagle_accounts'] = $eagle_accounts;
		$response['is_sync_skip'] = $is_sync_skip;
		if(!empty($error)){
			$response['error'] = $error;
		}
		return $this->response($response); 
	}
}
