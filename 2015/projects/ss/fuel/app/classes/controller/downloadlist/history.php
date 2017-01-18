<?php

class Controller_DownloadList_history extends Controller_DownloadList_Base {

	/**
	 * レポート履歴を取得
	 */
	public function action_get_history() {
		$limit = 1000;
		$history = Model_Data_History::get($limit);

		return $history;
	}


	/**
	 * 担当クライアントを取得
	 */
	public function action_get_myclients() {
		$user_id     = Session::get('user_id_sem');
		$tmp_clients = \Model_Mora_Client::get_for_user($user_id);

		if(!empty($tmp_clients)){
			foreach ($tmp_clients as $i => $value) {
				$clients["my_clients"][] = array(
					'id'   => $value["id"],
					'name' => \Util_Common_Client::get_client_name($value["id"])
				);
			}
			$clients["from"] = $user_id;
		}else{
			return;
		}
		return $clients;
	}
}
