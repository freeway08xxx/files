<?php
require_once APPPATH . "/const/eagle.php";
require_once APPPATH . "/const/eagle/message.php";


/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Eagle_Reserve extends Controller_Eagle_Base
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
		$this->view->set_filename('eagle/reserve/index');
		$this->response($this->view);
	}

	/**
	 * 予約の登録を行う 
	 * 
	 * @param mixed $data 
	 * @access public
	 * @return array 
	 */
	public function action_save($data=null) {

		if (!Request::is_hmvc()) {
			$client_id = Input::json('clientId');
			$accounts = Input::json('accounts');
			$is_sync_skip = Input::json('isSyncSkip');
		} else {
			$client_id = $data['clientId'];
			$accounts = $data['accounts'];
			$is_sync_skip = $data['isSyncSkip']; 
		}

		if(!$client_id && !$accounts){
			return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
		}

		## Insert to eagle
		$eagle_status = $is_sync_skip ? EAGLE_UPDATE_STATUS_SUCCESS_SYNC : EAGLE_UPDATE_STATUS_RESERVE_SYNC;
		$eagle = Model_Data_Eagle::insert(EXEC_CODE_STRUCTURE, $client_id, $eagle_status);
		$eagle_id = $eagle[0];

		$columns = array("eagle_id", "media_id", "account_id");
		$values  = array();
		## bulk Insert to eagle_target_account
		foreach($accounts as $key => $account){
			$account_id = $account['account_id'];
			$media_id = $account['media_id'];

			## 掲載取得バッチ用
			if ((intval($media_id) === MEDIA_ID_GOOGLE)) {
				## googleの場合、複数バッチを立てる
				$index = $key % MAX_G_STRUCTURE_EXEC;
				$google_account_ids[$index][] = $account_id;
			}elseif((intval($media_id) === MEDIA_ID_YAHOO || intval($media_id) === MEDIA_ID_IM)){
				$yahoo_account_ids[] = $account_id;
			}else{
				continue;
			}

			$value   = array();
			$value["$columns[0]"] = $eagle_id;
			$value["$columns[1]"] = $media_id;
			$value["$columns[2]"] = $account_id;
			$values[] = $value;
		}

		if(!empty($values)){
			$eagleTargetAccount = Model_Data_Eagle_Update_Account::ins($columns,$values);
		}

		if (!$is_sync_skip){
			## 掲載取得バッチ実行
			if (!empty($google_account_ids)) {
				foreach ($google_account_ids as $google_account_id) {
					$param = implode(",", $google_account_id);
					Log::info(print_r($param,true));
					$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_ACCOUNTSYNC_GOOGLE_JOB)."/buildWithParameters?token=eagle&id=".$eagle_id."&account_ids=".$param."&client_id=".$client_id."&user_id_sem=".$this->user_id, "curl");
					$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
					$curl->execute();
				}
			}
			if(!empty($yahoo_account_ids)){
				$param = implode(",",$yahoo_account_ids);
				$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_ACCOUNTSYNC_YAHOO_JOB)."/buildWithParameters?token=eagle&id=".$eagle_id."&client_id=".$client_id."&user_id_sem=".$this->user_id, "curl");
				$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
				$curl->execute();
			}
		}

		$response['eagle_id'] = $eagle_id;
		return $this->response($response); 
	}


	/**
	 * Eagleのコピーを行う 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_copy() {
		$eagle_id = Input::json('eagle_id');
		if(empty($eagle_id)){
			return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
		}

		## eagle情報
		$eagle = Model_Data_Eagle::get_one($eagle_id);
		## 対象アカウント情報
		$accounts = Model_Data_Eagle_Update_Account::find_by_eagle_id($eagle_id);

		$post_data = array(
			'clientId' => $eagle['client_id'],
			'accounts' => $accounts,
			'isSyncSkip' => true
		);

		return Request::forge("eagle/reserve/save",false)->execute(array($post_data))->response();
	}
}
