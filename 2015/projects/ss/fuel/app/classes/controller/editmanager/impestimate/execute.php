<?php

require_once APPPATH . "/const/main.php";
require_once APPPATH . "/const/editmanager/impestimate/impestimate.php";

class Controller_EditManager_ImpEstimate_Execute extends Controller_EditManager_ImpEstimate_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/editmanager/impestimate/execute";

	/**
	 * メイン処理の前処理を実行する
	 */
	public function before() {

		parent::before();

		// 入力パラメータを取得
		$this->imp_estimate_media = Input::post("imp_estimate_media");
		$this->imp_estimate_yahoo = Input::post("imp_estimate_yahoo");
		$this->imp_estimate_google = Input::post("imp_estimate_google");
		$this->imp_estimate_get = Input::post("imp_estimate_get");
		$this->imp_estimate_month = Input::post("imp_estimate_month");
		$this->imp_estimate_year = Input::post("imp_estimate_year");
		$this->imp_estimate_search = Input::post("imp_estimate_search");
		$this->imp_estimate_exact = Input::post("imp_estimate_exact");
		$this->imp_estimate_broad = Input::post("imp_estimate_broad");
		$this->imp_estimate_phrase = Input::post("imp_estimate_phrase");
		$this->imp_estimate_device = Input::post("imp_estimate_device");
		$this->imp_estimate_pc = Input::post("imp_estimate_pc");
		$this->imp_estimate_sp = Input::post("imp_estimate_sp");
		$this->imp_estimate_obj = Input::post("imp_estimate_obj");
		$this->imp_estimate_all = Input::post("imp_estimate_all");
		$this->imp_estimate_yahoo_only = Input::post("imp_estimate_yahoo_only");
		$this->imp_estimate_cpc = Input::post("imp_estimate_cpc");
		$this->imp_estimate_keyword = Input::post("imp_estimate_keyword");
		$this->action_type = Input::post("action_type");
	}

	/**
	 * メイン処理を実行する
	 */
	public function action_index() {

		// 入札価格の初期値を設定
		if (!isset($this->imp_estimate_cpc)) {
			$this->imp_estimate_cpc = IMP_ESTIMATE_CPC_DEFAULT;
		}

		// Viewを実行
		$this->view->set("imp_estimate_media", $this->imp_estimate_media);
		$this->view->set("imp_estimate_yahoo", $this->imp_estimate_yahoo);
		$this->view->set("imp_estimate_google", $this->imp_estimate_google);
		$this->view->set("imp_estimate_get", $this->imp_estimate_get);
		$this->view->set("imp_estimate_month", $this->imp_estimate_month);
		$this->view->set("imp_estimate_year", $this->imp_estimate_year);
		$this->view->set("imp_estimate_search", $this->imp_estimate_search);
		$this->view->set("imp_estimate_exact", $this->imp_estimate_exact);
		$this->view->set("imp_estimate_broad", $this->imp_estimate_broad);
		$this->view->set("imp_estimate_phrase", $this->imp_estimate_phrase);
		$this->view->set("imp_estimate_device", $this->imp_estimate_device);
		$this->view->set("imp_estimate_pc", $this->imp_estimate_pc);
		$this->view->set("imp_estimate_sp", $this->imp_estimate_sp);
		$this->view->set("imp_estimate_obj", $this->imp_estimate_obj);
		$this->view->set("imp_estimate_all", $this->imp_estimate_all);
		$this->view->set("imp_estimate_yahoo_only", $this->imp_estimate_yahoo_only);
		$this->view->set("imp_estimate_cpc", $this->imp_estimate_cpc);
		$this->view->set("imp_estimate_keyword", $this->imp_estimate_keyword);

		$this->view->set_filename("editmanager/impestimate/index");
	}

	/**
	 * 検索予測数を取得する
	 */
	public function action_get() {

		if ($this->action_type === "get") {

			// 取得条件を登録
			$target_columns = array("media", "get_method", "search_method", "device", "search_obj", "cpc", "keyword");

			$keyword_list = explode("\r\n", $this->imp_estimate_keyword);
			$keyword_list = array_filter($keyword_list, "strlen");
			$keyword_list = array_unique($keyword_list);
			$keyword = serialize($keyword_list);
			$keyword = base64_encode($keyword);

			$target_values[] = array($target_columns[0] => $this->imp_estimate_media,
									 $target_columns[1] => $this->imp_estimate_get,
									 $target_columns[2] => $this->imp_estimate_search,
									 $target_columns[3] => $this->imp_estimate_device,
									 $target_columns[4] => $this->imp_estimate_obj,
									 $target_columns[5] => $this->imp_estimate_cpc,
									 $target_columns[6] => $keyword);

			$result = Model_Data_ImpEstimateSetting::ins($target_columns, $target_values);

			// 検索予測数取得バッチを実行
			$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_IMP_ESTIMATE_JOB) . "/buildWithParameters?token=imp_estimate&id=" . $result[0] . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();

			$this->alert_message = ALERT_MSG_001 . "No: " . $result[0];
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * 結果ファイルをダウンロードする
	 */
	public function action_result_dl($media, $file_name) {

		Util_Common_Websocket::del_info();

		if ($media === IMP_ESTIMATE_MEDIA_YAHOO) {
			$file_path = YAHOO_IMP_ESTIMATE_RESULT_PATH;
		} elseif ($media === IMP_ESTIMATE_MEDIA_GOOGLE) {
			$file_path = GOOGLE_IMP_ESTIMATE_RESULT_PATH;
		}

		// 結果ファイルをダウンロード
		File::download($file_path . "/" . $file_name . ".csv", null, "text/tab-separated-values");
	}
}
