<?php

require_once APPPATH . "/const/main.php";
require_once APPPATH . "/const/editmanager/relate/relate.php";

class Controller_EditManager_Relate_Execute extends Controller_EditManager_Relate_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/editmanager/relate/execute";

	/**
	 * メイン処理の前処理を実行する
	 */
	public function before() {

		parent::before();

		// 入力パラメータを取得
		$this->client_id = Input::post("client_id");
		$this->account_pk = Input::post("account_pk");
		$this->relate_reget = Input::post("relate_reget");
		$this->relate_keyword = Input::post("relate_keyword");
		$this->action_type = Input::post("action_type");
	}

	/**
	 * メイン処理を実行する
	 */
	public function action_index() {

		// 担当クライアント一覧を取得
		$client_list = Model_Mora_Client::get_for_user();

		$all_client_list = array();
		foreach ($client_list as $client) {

			// クライアント名（表示用）
			if (!empty($client["client_name"])) {
				$client["client_name_disp"] = $client["company_name"] . "//" . $client["client_name"];
			} else {
				$client["client_name_disp"] = $client["company_name"];
			}

			$all_client_list[] = $client;
		}

		// クライアントのアカウント一覧を取得
		$all_account_list = array();
		if ($this->client_id) {

			$account_list = Model_Mora_Account::get_by_client($this->client_id);

			foreach ($account_list as $account) {

				// Yahoo! / Googleのみ対象
				if (intval($account["media_id"]) !== MEDIA_ID_YAHOO
						&& intval($account["media_id"]) !== MEDIA_ID_GOOGLE) {
					continue;
				}

				// アカウントPK
				$account["account_pk"] = $account["media_id"] . "//" . $account["account_id"];

				// アカウント名（表示用）
				$account["account_name_disp"] = "[" . $account["account_id"] . "] " . $account["account_name"];

				$all_account_list[] = $account;
			}
		}

		// Viewを実行
		$this->view->set("client_id", $this->client_id);
		$this->view->set("client_list", $all_client_list);
		$this->view->set("account_pk", $this->account_pk);
		$this->view->set("account_list", $all_account_list);
		$this->view->set("relate_reget", $this->relate_reget);
		$this->view->set("relate_keyword", $this->relate_keyword);

		$this->view->set_filename("editmanager/relate/index");
	}

	/**
	 * 関連KWを取得する
	 */
	public function action_get() {

		if ($this->action_type === "get") {

			// 取得条件を登録
			$target_columns = array("client_id", "media_id", "account_id", "relate_reget", "keyword");

			list($media_id, $account_id) = explode("//", $this->account_pk);

			$keyword_list = explode("\r\n", $this->relate_keyword);
			$keyword_list = array_filter($keyword_list, "strlen");
			$keyword = serialize($keyword_list);
			$keyword = base64_encode($keyword);

			$target_values[] = array($target_columns[0] => $this->client_id,
									 $target_columns[1] => $media_id,
									 $target_columns[2] => $account_id,
									 $target_columns[3] => isset($this->relate_reget) ? RELATE_REGET_ON : RELATE_REGET_OFF,
									 $target_columns[4] => $keyword);

			$result = Model_Data_RelateSetting::ins($target_columns, $target_values);

			// 関連KW取得バッチを実行
			$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_RELATE_JOB) . "/buildWithParameters?token=relate&id=" . $result[0] . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();

			$this->alert_message = ALERT_MSG_001;
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * 結果ファイルをダウンロードする
	 */
	public function action_result_dl($file_name) {

		Util_Common_Websocket::del_info();

		// 結果ファイルをダウンロード
		File::download(RELATE_RESULT_PATH . "/" . $file_name . ".csv", null, "text/tab-separated-values");
	}
}
