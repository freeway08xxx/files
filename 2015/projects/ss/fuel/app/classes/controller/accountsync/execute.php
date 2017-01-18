<?php

require_once APPPATH . "/const/accountsync/accountsync.php";

class Controller_Accountsync_Execute extends Controller_Accountsync_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/accountsync/execute";

	/**
	 * メイン処理の前処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function before() {

		parent::before();

		// 入力パラメータを取得
		$this->client_id = Input::post("client_id");
		$this->account_id_list = Input::post("account_id_list");
		$this->account_search_except = Input::post("account_search_except");
		$this->account_search_broad = Input::post("account_search_broad");
		$this->account_search_id_only = Input::post("account_search_id_only");
		$this->account_search_type = Input::post("account_search_type");
		$this->account_search_text = Input::post("account_search_text");
		$this->account_sync_type = Input::post("account_sync_type");
		$this->account_sync_execute = Input::post("account_sync_execute");
		$this->account_sync_reserve = Input::post("account_sync_reserve");
		$this->account_sync_date_from = Input::post("account_sync_date_from");
		$this->account_sync_date_to = Input::post("account_sync_date_to");
		$this->account_sync_time = Input::post("account_sync_time");
		$this->account_sync_minutes = Input::post("account_sync_minutes");
		$this->account_sync_content = Input::post("account_sync_content");
		$this->account_sync_sync = Input::post("account_sync_sync");
		$this->account_sync_review = Input::post("account_sync_review");
		$this->account_sync_out_format = Input::post("account_sync_out_format");
		$this->account_sync_eagle = Input::post("account_sync_eagle");
		$this->account_sync_eagle_on = Input::post("account_sync_eagle_on");
		$this->account_sync_eagle_off = Input::post("account_sync_eagle_off");
		$this->account_sync_mail_address = Input::post("account_sync_mail_address");
		$this->account_sync_row = Input::post("account_sync_row");
		$this->account_sync_select_row = Input::post("account_sync_select_row");
		$this->action_type = Input::post("action_type");

		if (Input::post("scroll_x")) {

			$this->scroll_x = Input::post("scroll_x");

		} else {

			$this->scroll_x = 0;
		}

		if (Input::post("scroll_y")) {

			$this->scroll_y = Input::post("scroll_y");

		} else {

			$this->scroll_y = 0;
		}
	}

	/**
	 * メイン処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_index() {

		// 担当クライアント一覧を取得
		$all_client_list = array();
		$client_list = Model_Mora_Client::get_for_user();

		foreach ($client_list as $client) {

			// クライアント名（表示用）
			if ($client["client_name"]) {

				$client["client_name_disp"] = $client["company_name"]
											. "//"
											. $client["client_name"];

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
				$account["account_pk"] = $account["media_id"]
									   . "//"
									   . $account["account_id"];

				// アカウント名（表示用）
				$account["account_name_disp"] = "["
											  . $account["account_id"]
											  . "] "
											  . $account["account_name"];

				$all_account_list[] = $account;
			}
		}

		// 完了メール宛先の初期値を設定
		if (!isset($this->account_sync_mail_address)) {

			$user_list = Model_Mora_User::get_user_by_id(\Session::get("user_id_sem"));
			$this->account_sync_mail_address = $user_list["mail_address"];
		}

		// アカウント同期の実行結果一覧を取得
		$all_account_sync_list = array();

		if ($this->account_id_list) {

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				// アカウント同期一覧を取得
				$account_sync_list = Model_Data_AccountSyncSchedule::get($media_id, $account_id);

				// アカウント名を取得
				$account_name = "";

				foreach ($all_account_list as $account) {

					if ($account_id === $account["account_id"]) {

						$account_name = $account["account_name"];
						break;
					}
				}

				foreach ($account_sync_list as $account_sync) {

					// アカウント名（表示用）
					$account_sync["account_name_disp"] = "["
													   . $account_sync["account_id"]
													   . "] "
													   . $account_name;

					// 実行日時
					$account_sync["action_date_time"] = $account_sync["action_date"]
													  . " "
													  . $account_sync["action_time"]
													  . ":"
													  . $account_sync["action_minutes"];

					// 実行内容
					$account_sync["account_sync_content"] = $GLOBALS["account_sync_content_list"][$account_sync["approval_flg"]];

					// 出力フォーマット
					if ($account_sync["approval_flg"] === ACCOUNT_SYNC_CONTENT_SYNC) {

						$account_sync["output_format_disp"] = $GLOBALS["account_sync_out_format_list"][$account_sync["output_format"]];

					} else {

						$account_sync["output_format_disp"] = "--";
					}

					// EAGLE実行
					$account_sync["account_sync_eagle"] = $GLOBALS["account_sync_eagle_list"][$account_sync["eagle_flg"]];

					$all_account_sync_list[] = $account_sync;
				}
			}
		}

		// Viewを実行
		$this->view->set("client_id", $this->client_id);
		$this->view->set("client_list", $all_client_list);
		$this->view->set("account_id_list", $this->account_id_list);
		$this->view->set("account_list", $all_account_list);
		$this->view->set("account_search_except", $this->account_search_except);
		$this->view->set("account_search_broad", $this->account_search_broad);
		$this->view->set("account_search_id_only", $this->account_search_id_only);
		$this->view->set("account_search_type", $this->account_search_type);
		$this->view->set("account_search_text", $this->account_search_text);
		$this->view->set("account_sync_type", $this->account_sync_type);
		$this->view->set("account_sync_execute", $this->account_sync_execute);
		$this->view->set("account_sync_reserve", $this->account_sync_reserve);
		$this->view->set("account_sync_date_from", $this->account_sync_date_from);
		$this->view->set("account_sync_date_to", $this->account_sync_date_to);
		$this->view->set("account_sync_time", $this->account_sync_time);
		$this->view->set("account_sync_minutes", $this->account_sync_minutes);
		$this->view->set("account_sync_content", $this->account_sync_content);
		$this->view->set("account_sync_sync", $this->account_sync_sync);
		$this->view->set("account_sync_review", $this->account_sync_review);
		$this->view->set("account_sync_out_format", $this->account_sync_out_format);
		$this->view->set("account_sync_eagle", $this->account_sync_eagle);
		$this->view->set("account_sync_eagle_on", $this->account_sync_eagle_on);
		$this->view->set("account_sync_eagle_off", $this->account_sync_eagle_off);
		$this->view->set("account_sync_mail_address", $this->account_sync_mail_address);
		$this->view->set("account_sync_row", $this->account_sync_row);
		$this->view->set("account_sync_list", $all_account_sync_list);
		$this->view->set("scroll_x", $this->scroll_x);
		$this->view->set("scroll_y", $this->scroll_y);

		$this->view->set_filename("accountsync/index");
	}

	/**
	 * アカウント同期を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_sync_execute() {

		if ($this->account_id_list) {

			// アカウント同期の実行情報を登録
			$account_sync_schedule_columns = array("reserve_id",
												   "media_id",
												   "account_id",
												   "action_date",
												   "action_time",
												   "action_minutes",
												   "output_format",
												   "eagle_flg",
												   "approval_flg",
												   "mail_user_list");

			$account_sync_schedule_values = array();

			// 予約IDを設定
			$reserve_id = mt_rand() . date("YmdHis");

			// 実行日時分を設定
			if ($this->account_sync_type === ACCOUNT_SYNC_TYPE_EXECUTE) {

				// 即時実行
				$action_date_list[] = array(date("Y/m/d"));
				$action_time = date("G");
				$action_minutes = date("i");

			} else {

				// 予約
				$action_date_list[] = array($this->account_sync_date_from);
				$action_date_time_from = strtotime($this->account_sync_date_from);
				$action_date_time_to = strtotime($this->account_sync_date_to);

				// From～Toの期間分、登録
				while ($action_date_time_from !== $action_date_time_to) {

					$action_date_time_from = strtotime("+1 day", $action_date_time_from);
					$action_date_list[] = date("Y/m/d", $action_date_time_from);
				}

				$action_time = $this->account_sync_time;
				$action_minutes = $GLOBALS["account_sync_minutes"][$this->account_sync_minutes];
			}

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				foreach ($action_date_list as $action_date) {

					$value = array();
					$value[$account_sync_schedule_columns[0]] = $reserve_id;
					$value[$account_sync_schedule_columns[1]] = $media_id;
					$value[$account_sync_schedule_columns[2]] = $account_id;
					$value[$account_sync_schedule_columns[3]] = $action_date;
					$value[$account_sync_schedule_columns[4]] = $action_time;
					$value[$account_sync_schedule_columns[5]] = $action_minutes;
					$value[$account_sync_schedule_columns[6]] = $this->account_sync_out_format;
					$value[$account_sync_schedule_columns[7]] = $this->account_sync_eagle;
					$value[$account_sync_schedule_columns[8]] = $this->account_sync_content;
					$value[$account_sync_schedule_columns[9]] = trim($this->account_sync_mail_address);

					$account_sync_schedule_values[] = $value;
				}
			}

			Model_Data_AccountSyncSchedule::ins($account_sync_schedule_values);

			// 即時実行の場合、アカウント同期JOBを実行、
			if ($this->account_sync_type === ACCOUNT_SYNC_TYPE_EXECUTE) {

				// Yahoo!
				$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_ACCOUNT_SYNC_YAHOO_JOB) . "/buildWithParameters?token=account_sync&reserve_id=" . $reserve_id, "curl");
				$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
				$curl->execute();

				// Google
				$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_ACCOUNT_SYNC_GOOGLE_JOB) . "/buildWithParameters?token=account_sync&reserve_id=" . $reserve_id, "curl");
				$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
				$curl->execute();
				//$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_ACCOUNT_SYNC_JOB) . "/buildWithParameters?token=account_sync&reserve_id=" . $reserve_id, "curl");
				//$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
				//$curl->execute();
			}

			$this->alert_message = ALERT_MSG_001;
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * アカウント同期予約結果をダウンロードする
	 *
	 * @param $date DLファイル作成日付
	 * @param $id 処理ID
	 * @param $account_id アカウントID
	 * @return なし
	 */
	public function action_sync_dl($date, $id, $account_id) {

		File::download(ACCOUNT_SYNC_RESULT_PATH . "/" . $date . "/" . $id . "/" . $account_id . ".zip");
	}

	/**
	 * アカウント同期予約結果を一括ダウンロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_sync_bulk_dl() {

		// 一括DL対象を取得
		$bulk_dl_list = Model_Data_AccountSyncSchedule::get_bulk_dl(explode(",", $this->account_sync_select_row));

		try {
			// ファイル圧縮
			$zip = new ZipArchive();
			$zip_file_path = ACCOUNT_SYNC_RESULT_PATH . "/tmp/" . mt_rand() . date("YmdHis") . ".zip";
			$zip_res = $zip->open($zip_file_path, ZipArchive::CREATE);

			if ($zip_res === true) {

				// 圧縮するファイルを設定
				$file_exist_flg = false;
				foreach ($bulk_dl_list as $bulk_dl) {
					if (isset($bulk_dl["out_file_path"])) {
						$file_name = $bulk_dl["id"] . "_" . $bulk_dl["account_id"] . ".zip";
						$zip->addFile($bulk_dl["out_file_path"], $file_name);
						$file_exist_flg = true;
					}
				}
				$zip->close();

				if ($file_exist_flg) {
					File::download($zip_file_path);
				} else {
					$this->alert_message = ALERT_MSG_003;
				}
			} else {
				$this->alert_message = ALERT_MSG_002;
			}
		} catch (Exception $e) {
			$this->alert_message = $e.ALERT_MSG_002;
		}

		// メイン処理を実行（エラーもしくは、DLファイルが無い場合に実行）
		$this->action_index();
	}

	/**
	 * アカウント同期予約を削除する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_sync_delete() {

		// アカウント同期予約を削除
		Model_Data_AccountSyncSchedule::del(explode(",", $this->account_sync_select_row));

		// メイン処理を実行
		$this->action_index();
	}
}
