<?php
require_once APPPATH . "/const/eagle/status.php";

################################################################################
#
# Title : ステータス変更用コントローラ
#
#  2014/06/01  First Version
#
################################################################################

class Controller_Eagle_Status_Update extends Controller_Eagle_Status_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/new/eagle/status/update";

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();

		## 入力パラメータ
		$this->IN_data["client_id"]             = Input::get("client_id") ? Input::get("client_id") : Input::post("client_id");
		$this->IN_data["account_id_list"]       = Input::post("account_id_list");
		$this->IN_data["component"]             = Input::post("component");
		$this->IN_data["search_onlyid"]         = Input::post("search_onlyid");
		$this->IN_data["ad_search_pattern"]     = Input::post("ad_search_pattern");
		$this->IN_data["eagle_id"]              = Input::post("eagle_id");
		$this->IN_data["update_status_flg"]     = Input::post("update_status_flg");
		$this->IN_data["page"]                  = Input::get("p") ? Input::get("p") : 1;
		$this->IN_data["per_page"]              = Input::post("per_page") ? Input::post("per_page") : PER_PAGE;
		## アカウント検索
		$this->IN_data["account_search"]        = Input::post("account_search");
		$this->IN_data["account_search_type"]   = Input::post("account_search_type");
		$this->IN_data["account_search_list"]   = Input::post("account_search_list");
		## キャンペーン検索
		$this->IN_data["campaign_search"]       = Input::post("campaign_search");
		$this->IN_data["campaign_search_like"]  = Input::post("campaign_search_like");
		$this->IN_data["campaign_search_type"]  = Input::post("campaign_search_type");
		$this->IN_data["campaign_search_list"]  = Input::post("campaign_search_list");
		## キャンペーン除外検索
		$this->IN_data["campaign_except"]       = Input::post("campaign_except");
		$this->IN_data["campaign_except_like"]  = Input::post("campaign_except_like");
		$this->IN_data["campaign_except_type"]  = Input::post("campaign_except_type");
		$this->IN_data["campaign_except_list"]  = Input::post("campaign_except_list");
		## 広告グループ検索
		$this->IN_data["adgroup_search"]        = Input::post("adgroup_search");
		$this->IN_data["adgroup_search_like"]   = Input::post("adgroup_search_like");
		$this->IN_data["adgroup_search_type"]   = Input::post("adgroup_search_type");
		$this->IN_data["adgroup_search_list"]   = Input::post("adgroup_search_list");
		## 広告グループ除外検索
		$this->IN_data["adgroup_except"]        = Input::post("adgroup_except");
		$this->IN_data["adgroup_except_like"]   = Input::post("adgroup_except_like");
		$this->IN_data["adgroup_except_type"]   = Input::post("adgroup_except_type");
		$this->IN_data["adgroup_except_list"]   = Input::post("adgroup_except_list");
		## キーワード検索
		$this->IN_data["keyword_search"]        = Input::post("keyword_search");
		$this->IN_data["keyword_search_like"]   = Input::post("keyword_search_like");
		$this->IN_data["keyword_search_type"]   = Input::post("keyword_search_type");
		$this->IN_data["keyword_search_list"]   = Input::post("keyword_search_list");
		## キーワード除外検索
		$this->IN_data["keyword_except"]        = Input::post("keyword_except");
		$this->IN_data["keyword_except_like"]   = Input::post("keyword_except_like");
		$this->IN_data["keyword_except_type"]   = Input::post("keyword_except_type");
		$this->IN_data["keyword_except_list"]   = Input::post("keyword_except_list");
		## 広告検索
		$this->IN_data["ad_search"]             = Input::post("ad_search");
		$this->IN_data["ad_search_like"]        = Input::post("ad_search_like");
		$this->IN_data["ad_search_type"]        = Input::post("ad_search_type");
		$this->IN_data["ad_search_list"]        = Input::post("ad_search_list");
		## 広告除外検索
		$this->IN_data["ad_except"]             = Input::post("ad_except");
		$this->IN_data["ad_except_like"]        = Input::post("ad_except_like");
		$this->IN_data["ad_except_type"]        = Input::post("ad_except_type");
		$this->IN_data["ad_except_list"]        = Input::post("ad_except_list");
		## 絞り込み検索
		$this->IN_data["search_flg"]            = Input::post("search_flg");
		$this->IN_data["search_media"]          = Input::post("search_media");
		$this->IN_data["search_component"]      = Input::post("search_component");
		$this->IN_data["search_id"]             = Input::post("search_id");
		$this->IN_data["search_name"]           = Input::post("search_name");
		$this->IN_data["search_status"]         = Input::post("search_status");
	}

	/*========================================================================*/
	/* クライアント選択画面出力
	/*========================================================================*/
	public function action_client() {

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);

		## View
		$this->view->set_filename("eagle/status/index");
	}

	/*========================================================================*/
	/* アカウント選択画面出力
	/*========================================================================*/
	public function action_account() {

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## キャンペーン検索テーブルブロック作成
		$HMVC_campaigntable = Request::forge("eagle/status/update/campaigntable", false)->execute();

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set_safe("HMVC_campaigntable", $HMVC_campaigntable);
		$this->view->set_safe("HMVC_componenttable", "");
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/index");
	}

	/*========================================================================*/
	/* アカウント選択テーブルブロック作成
	/*========================================================================*/
	public function action_accounttable($account_id_list = null) {

		if (Request::is_hmvc()) {

			## 指定クライアントのアカウント一覧取得
			$media_list = array(MEDIA_ID_YAHOO, MEDIA_ID_GOOGLE, MEDIA_ID_IM);
			$DB_account_list = \Model_Mora_Account::get_by_client($this->IN_data["client_id"], $media_list);

			## メールのリンクから飛んできた場合
			if (!empty($account_id_list)) {
				## 選択済みアカウント配列作成
				$this->IN_data["account_id_list"] = $account_id_list;
			}

			## 出力パラメータ
			$this->view->set("DB_account_list", $DB_account_list);
			$this->view->set("OUT_data", $this->IN_data);

			## View
			$this->view->set_filename("eagle/status/accounttable");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* キャンペーン検索テーブルブロック作成
	/*========================================================================*/
	public function action_campaigntable($refresh_campaign_flg = "0", $chk_structure_flg = "0") {

		if (Request::is_hmvc()) {

			## 出力パラメータ
			$this->IN_data["refresh_campaign_flg"] = $refresh_campaign_flg;
			$this->IN_data["chk_structure_flg"]    = $chk_structure_flg;
			$this->view->set("OUT_data", $this->IN_data);

			## View
			$this->view->set_filename("eagle/status/campaigntable");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* キャンペーン一覧ダウンロード
	/*========================================================================*/
	public function action_campaigndl() {

		global $media_name_list;

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## キャンペーン検索テーブルブロック作成
		$HMVC_campaigntable = Request::forge("eagle/status/update/campaigntable", false)->execute();

		## 初期化
		$DL_contents[] = array("処理対象", "媒体", "アカウントID", "アカウント名", "キャンペーンID", "キャンペーン名", "ステータス");

		## 指定アカウント毎にループ
		foreach ($this->IN_data["account_id_list"] as $account_id) {

			## media_id, account_id 分割
			list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

			## 指定アカウント情報取得
			$DB_account = \Model_Mora_Account::get_for_account($tmp_account_id);

			## 指定アカウントのキャンペーン情報取得
			$DB_campaign_list = \Model_Data_EagleCampaign::get($tmp_media_id, $tmp_account_id);

			## アカウント情報をキャンペーン情報に付加
			foreach ($DB_campaign_list as $DB_campaign) {

				## 初期化
				$target_flg = "★";

				## キャンペーン名でフィルタリング
				if (!empty($this->IN_data["campaign_search_list"])) {
					$ret = \Util_Common_Filter::filterElements(array($DB_campaign["campaign_id"], $DB_campaign["campaign_name"]),
															   $this->IN_data["campaign_search_list"],
															   $this->IN_data["campaign_search_like"],
															   $this->IN_data["campaign_search_type"],
															   $this->IN_data["campaign_search"]);

					if (!$ret) {
						$target_flg = "";
					}
				}

				if (!empty($this->IN_data["campaign_except_list"])) {
					$ret = \Util_Common_Filter::filterElements(array($DB_campaign["campaign_id"], $DB_campaign["campaign_name"]),
															   $this->IN_data["campaign_except_list"],
															   $this->IN_data["campaign_except_like"],
															   $this->IN_data["campaign_except_type"],
															   $this->IN_data["campaign_except"]);

					if (!$ret) {
						$target_flg = "";
					}
				}

				$DL_contents[] = array($target_flg,
									   $media_name_list["$tmp_media_id"],
									   $tmp_account_id,
									   $DB_account["account_name"],
									   $DB_campaign["campaign_id"],
									   $DB_campaign["campaign_name"],
									   $DB_campaign["campaign_status"]);
			}
		}

		## ファイルダウンロード
		$this->format = "csv";
		$DL_filename = DL_EAGLE_STATUS_CPN . date("YmdHis") . ".csv";
		$response = $this->response($DL_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $DL_filename);

		return $response;
	}

	/*========================================================================*/
	/* 最新CPN取得
	/*========================================================================*/
	public function action_refreshcpn($eagle_id = null, $mail_flg = null) {

		## 初期化
		$refresh_campaign_flg = "0";
		$chk_structure_flg    = "1";

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## メールのリンクから飛んできた場合
		if (!empty($eagle_id)) {

			## 最新CPN取得済み
			$refresh_campaign_flg = "1";

			## 掲載取得アカウント一覧取得
			// 掲載取得完了メール
			if ($mail_flg === MAIL_STRUCTURE) {
				$DB_account_list = \Model_Data_EagleTargetAccount::get($eagle_id);
			} else {
			// ステータス変更完了メール
				$DB_account_list = \Model_Data_EagleStatusTarget::get_target_account_list($eagle_id);
			}

			## アカウント毎にループ
			foreach ($DB_account_list as $DB_account) {
				## 選択済みアカウント配列作成
				$account_id_list[] = $DB_account["media_id"] . "//" . $DB_account["account_id"];

				## 全アカウントの掲載取得が１時間以内に完了したか確認
				if ($chk_structure_flg === "1") {
					$DB_chk_structure_cnt = \Model_Data_EagleTargetAccount::chk_structure($eagle_id, $DB_account["account_id"], intval($DB_account["media_id"]));
					if ($DB_chk_structure_cnt[0]["count"] === "0") {
						## 全アカウントが最新掲載ではない
						$chk_structure_flg = "0";
					}
				}
			}

			## アカウント選択テーブルブロック作成
			$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute(array($account_id_list));

		} else {
			## アカウント選択テーブルブロック作成
			$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();
		}

		## アカウント選択済み
		if (!empty($this->IN_data["account_id_list"])) {

			## 指定アカウント毎にループ
			foreach ($this->IN_data["account_id_list"] as $account_id) {

				## media_id, account_id 分割
				list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

				## 最新CPN取得＆DB格納
				// Yahoo!
				if (intval($tmp_media_id) === MEDIA_ID_YAHOO) {
					$ret = \Util_Eagle_YahooApi::get_campaign($tmp_account_id);
				// YDN
				} elseif (intval($tmp_media_id) === MEDIA_ID_IM) {
					$ret = \Util_Eagle_YdnApi::get_campaign($tmp_account_id);
				// Google
				} else {
					$ret = \Util_Eagle_AdwordsApi::get_campaign($tmp_account_id);
				}
				## エラーハンドリング
				if (!$ret) {
					$this->alert_message = ERRMSG_001;
				} else {
					## 最新CPN取得済み
					$refresh_campaign_flg = "1";
				}

				## 全アカウントの掲載取得が１時間以内に完了したか確認
				if ($chk_structure_flg === "1") {
					$DB_chk_structure_cnt = \Model_Data_EagleTargetAccount::chk_structure(null, $tmp_account_id, intval($tmp_media_id));
					if ($DB_chk_structure_cnt[0]["count"] === "0") {
						## 全アカウントが最新掲載ではない
						$chk_structure_flg = "0";
					}
				}
			}
		}

		## キャンペーン検索テーブルブロック作成
		$HMVC_campaigntable = Request::forge("eagle/status/update/campaigntable", false)->execute(array($refresh_campaign_flg, $chk_structure_flg));

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set_safe("HMVC_campaigntable", $HMVC_campaigntable);
		$this->view->set_safe("HMVC_componenttable", "");
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/index");
	}

	/*========================================================================*/
	/* 掲載取得
	/*========================================================================*/
	public function action_getstructure() {

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## キャンペーン検索テーブルブロック作成
		$HMVC_campaigntable = Request::forge("eagle/status/update/campaigntable", false)->execute();

		## アカウント選択済み
		if (!empty($this->IN_data["account_id_list"])) {

			## 処理ID登録
			$columns = array("exec_code");
			$value   = array();
			$values  = array();

			$value["$columns[0]"] = EXEC_CODE_STRUCTURE;
			$values[] = $value;
			$ret = \Model_Data_EagleSetting::ins($columns, $values);
			$DB_eagle_id = $ret[0];

			## アカウント一覧登録
			$columns = array("id", "media_id", "account_id");
			$value   = array();
			$values  = array();

			## 指定アカウント毎にループ
			$i = 0;
			foreach ($this->IN_data["account_id_list"] as $account_id) {

				## media_id, account_id 分割
				list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

				## 掲載取得バッチ用
				if (intval($tmp_media_id) === MEDIA_ID_GOOGLE || intval($tmp_media_id) === MEDIA_ID_GOOGLE_CONSULTING) {
					$index = $i % MAX_G_STRUCTURE_EXEC;
					$g_account_id_list[$index][] = $tmp_account_id;
					$i++;
				}

				$value["$columns[0]"] = $DB_eagle_id;
				$value["$columns[1]"] = $tmp_media_id;
				$value["$columns[2]"] = $tmp_account_id;
				$values[] = $value;
			}
			\Model_Data_EagleTargetAccount::ins($columns, $values);

			## 掲載取得バッチ実行
			foreach (array("google","yahoo") as $product) {
				if ($product === "google") {
					if (!empty($g_account_id_list)) {
						foreach ($g_account_id_list as $g_account_ids) {
							$param = implode(",", $g_account_ids);
							$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_STRUCTURE_G_JOB)."/buildWithParameters?token=eagle&id=".$DB_eagle_id."&account_ids=".$param."&client_id=".$this->IN_data["client_id"]."&user_id_sem=".\Session::get("user_id_sem"), "curl");
							$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
							$curl->execute();
						}
					}
				} else {
					$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_STRUCTURE_Y_JOB)."/buildWithParameters?token=eagle&id=".$DB_eagle_id."&client_id=".$this->IN_data["client_id"]."&user_id_sem=".\Session::get("user_id_sem"), "curl");
					$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
					$curl->execute();
				}
			}

			$this->alert_message = ERRMSG_002;
		}

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set_safe("HMVC_campaigntable", $HMVC_campaigntable);
		$this->view->set_safe("HMVC_componenttable", "");
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/index");
	}

	/*========================================================================*/
	/* 掲載取得せず進む
	/*========================================================================*/
	public function action_ungetstructure() {

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## 処理対象検索テーブルブロック作成
		$HMVC_componenttable = Request::forge("eagle/status/update/componenttable", false)->execute();

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set_safe("HMVC_campaigntable", "");
		$this->view->set_safe("HMVC_componenttable", $HMVC_componenttable);
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/index");
	}

	/*========================================================================*/
	/* 処理対象検索テーブルブロック作成
	/*========================================================================*/
	public function action_componenttable() {

		if (Request::is_hmvc()) {

			## 出力パラメータ
			$this->view->set("OUT_data", $this->IN_data);

			## View
			$this->view->set_filename("eagle/status/componenttable");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* 処理対象一覧ダウンロード
	/*========================================================================*/
	public function action_targetdl() {

		global $media_name_list;

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## キャンペーン検索テーブルブロック作成
		$HMVC_campaigntable = Request::forge("eagle/status/update/campaigntable", false)->execute();

		## 処理対象一覧取得
		$DL_contents = \Util_Eagle_StatusUpdate::get_targetdl($this->IN_data);

		## ファイルダウンロード
		$this->format = "csv";
		$DL_filename = DL_EAGLE_STATUS_TARGET . date("YmdHis") . ".csv";
		$response = $this->response($DL_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $DL_filename);

		return $response;
	}

	/*========================================================================*/
	/* 確認画面
	/*========================================================================*/
	public function action_check() {

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## アカウント選択済み
		if (!empty($this->IN_data["account_id_list"])) {
			## 初期化
			$DL_contents     = array();
			$search_contents = array();
			$search_contents_count = 0;

			## 全掲載件数取得
			$DL_contents_count = \Util_Eagle_StatusUpdate::get_structure_count($this->IN_data);

			## 絞り込み検索
			if ($this->IN_data["search_flg"] === "1") {
				$search_list = array("component"        => $this->IN_data["component"],
									 "search_media"     => $this->IN_data["search_media"],
									 "search_component" => $this->IN_data["search_component"],
									 "search_id"        => $this->IN_data["search_id"],
									 "search_name"      => $this->IN_data["search_name"],
									 "search_status"    => $this->IN_data["search_status"]);

				## 検索掲載件数取得
				$search_contents_count = \Util_Eagle_StatusUpdate::get_search_structure_count($this->IN_data, $search_list);
				## ページング
				$offset = \Util_Common_Pagination::get($search_contents_count, $this->IN_data["per_page"], NUM_LINK, "", "eagle");
				## 検索掲載情報取得
				$search_contents = \Util_Eagle_StatusUpdate::get_search_structure($this->IN_data, $search_list, $offset, $this->IN_data["per_page"]);
			} else {
				## ページング
				$offset = \Util_Common_Pagination::get($DL_contents_count, $this->IN_data["per_page"], NUM_LINK, "", "eagle");
				## 全掲載情報取得
				$DL_contents = \Util_Eagle_StatusUpdate::get_structure($this->IN_data, $offset, $this->IN_data["per_page"]);
			}

			## 出力パラメータ
			$this->view->set("per_page", $this->IN_data["per_page"]);
			$this->view->set("search_flg", $this->IN_data["search_flg"]);
			$this->view->set("search_contents", $search_contents);
			$this->view->set("search_contents_count", $search_contents_count);
			$this->view->set("DL_contents", $DL_contents);
			$this->view->set("DL_contents_count", $DL_contents_count);
		}

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/check");
	}

	/*========================================================================*/
	/* ステータス変更
	/*========================================================================*/
	public function action_updstatus() {

		global $media_name_list;

		## 担当クライアント一覧取得
		$DB_client_list = \Model_Mora_Client::get_for_user();

		## アカウント選択テーブルブロック作成
		$HMVC_accounttable = Request::forge("eagle/status/update/accounttable", false)->execute();

		## 処理対象検索テーブルブロック作成
		$HMVC_componenttable = Request::forge("eagle/status/update/componenttable", false)->execute();

		## アカウント選択済み
		if (!empty($this->IN_data["account_id_list"])) {

			## 処理ID登録
			$columns = array("exec_code");
			$value   = array();
			$values  = array();

			$value["$columns[0]"] = EXEC_CODE_STATUS;
			$values[] = $value;
			$ret = \Model_Data_EagleSetting::ins($columns, $values);
			$DB_eagle_id = $ret[0];

			## 処理対象一覧登録
			$columns = array("id", "media_id", "account_id", "campaign_id", "adgroup_id", "target_kbn", "target_id");
			$value   = array();
			$values  = array();

			## 掲載取得(DB)
			$DL_contents = \Util_Eagle_StatusUpdate::get_targetdl($this->IN_data);

			## 掲載有り
			if (!empty($DL_contents)) {

				foreach ($DL_contents as $DL_content) {

					$value["$columns[0]"] = $DB_eagle_id;
					$value["$columns[1]"] = array_search($DL_content[0], $media_name_list);
					$value["$columns[2]"] = $DL_content[1];
					$value["$columns[3]"] = $DL_content[3];

					// 処理対象コンポーネントがキャンペーンの場合
					if ($this->IN_data["component"] === "campaign") {

						// 全ONの場合、処理対象は停止中のみ
						if ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_ON &&
							($DL_content[5] === ADWORDS_CAMPAIGN_PAUSED || $DL_content[5] === YAHOO_CAMPAIGN_PAUSED || $DL_content[5] === YDN_CAMPAIGN_PAUSED)) {
							$value["$columns[4]"] = DB::expr("NULL");
							$value["$columns[5]"] = DB::expr("NULL");
							$value["$columns[6]"] = DB::expr("NULL");
						// 全OFFの場合、処理対象は運用中のみ
						} elseif ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_OFF &&
							($DL_content[5] === ADWORDS_CAMPAIGN_ACTIVE || $DL_content[5] === YAHOO_CAMPAIGN_ACTIVE || $DL_content[5] === YDN_CAMPAIGN_ACTIVE)) {
							$value["$columns[4]"] = DB::expr("NULL");
							$value["$columns[5]"] = DB::expr("NULL");
							$value["$columns[6]"] = DB::expr("NULL");
						} else {
							unset($value);
							continue;
						}

					// 処理対象コンポーネントが広告グループの場合
					} elseif ($this->IN_data["component"] === "adgroup") {

						// 全ONの場合、処理対象は停止中のみ
						if ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_ON &&
							($DL_content[8] === ADWORDS_ADGROUP_PAUSED || $DL_content[8] === YAHOO_ADGROUP_PAUSED || $DL_content[8] === YDN_ADGROUP_PAUSED)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = DB::expr("NULL");
							$value["$columns[6]"] = DB::expr("NULL");
						// 全OFFの場合、処理対象は運用中のみ
						} elseif ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_OFF &&
							($DL_content[8] === ADWORDS_ADGROUP_ACTIVE || $DL_content[8] === YAHOO_ADGROUP_ACTIVE || $DL_content[8] === YDN_ADGROUP_ACTIVE)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = DB::expr("NULL");
							$value["$columns[6]"] = DB::expr("NULL");
						} else {
							unset($value);
							continue;
						}

					// 処理対象コンポーネントがキーワードの場合
					} elseif ($this->IN_data["component"] === "keyword") {

						// 全ONの場合、処理対象は停止中のみ
						if ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_ON &&
							($DL_content[11] === ADWORDS_CRITERION_PAUSED || $DL_content[11] === YAHOO_CRITERION_PAUSED)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = "KW";
							$value["$columns[6]"] = $DL_content[9];
						// 全OFFの場合、処理対象は運用中のみ
						} elseif ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_OFF &&
							($DL_content[11] === ADWORDS_CRITERION_ACTIVE || $DL_content[11] === YAHOO_CRITERION_ACTIVE)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = "KW";
							$value["$columns[6]"] = $DL_content[9];
						} else {
							unset($value);
							continue;
						}

					// 処理対象コンポーネントが広告の場合
					} elseif ($this->IN_data["component"] === "ad") {

						// 全ONの場合、処理対象は停止中のみ
						if ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_ON &&
							($DL_content[17] === ADWORDS_AD_PAUSED || $DL_content[17] === YAHOO_AD_PAUSED || $DL_content[17] === YDN_AD_PAUSED)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = "AD";
							$value["$columns[6]"] = $DL_content[12];
						// 全OFFの場合、処理対象は運用中のみ
						} elseif ($this->IN_data["update_status_flg"] === UPDATE_STATUS_FLG_OFF &&
							($DL_content[17] === ADWORDS_AD_ACTIVE || $DL_content[17] === YAHOO_AD_ACTIVE || $DL_content[17] === YDN_AD_ACTIVE)) {
							$value["$columns[4]"] = $DL_content[6];
							$value["$columns[5]"] = "AD";
							$value["$columns[6]"] = $DL_content[12];
						} else {
							unset($value);
							continue;
						}
					}
					$values[] = $value;
				}
				if (!empty($values)) {
					 \Model_Data_EagleStatusTarget::ins($columns, $values);

					## ステータス変更バッチ実行
					$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_EAGLE_STATUS_JOB)."/buildWithParameters?token=eagle&id=".$DB_eagle_id."&component=".$this->IN_data["component"]."&update_status_flg=".$this->IN_data["update_status_flg"]."&client_id=".$this->IN_data["client_id"]."&user_id_sem=".\Session::get("user_id_sem"), "curl");
					$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
					$curl->execute();

					$this->alert_message = ERRMSG_003;

				## 処理対象がない場合
				} else {
					$this->alert_message = ERRMSG_004;
				}
			}
		}

		## 出力パラメータ
		$this->view->set("DB_client_list", $DB_client_list);
		$this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
		$this->view->set_safe("HMVC_campaigntable", "");
		$this->view->set_safe("HMVC_componenttable", $HMVC_componenttable);
		$this->view->set("OUT_data", $this->IN_data);

		## View
		$this->view->set_filename("eagle/status/index");
	}
}
