<?php
class Controller_Client_Conversion extends Controller_Client_Base {

	## loginユーザ権限チェック用URL
	public $access_url = "/sem/susie/entry_client_aim.php";

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();

		## 入力パラメータ
		$this->IN_data["client_id"] = Input::get("client_id") ? Input::get("client_id") : Input::post("client_id");
		$this->IN_data["cv_list"]   = Input::post("cv_list");
	}

	/*========================================================================*/
	/* トップ画面出力
	/*========================================================================*/
	public function action_index() {
		## View
		$this->view->set_filename("client/conversion/index");
		$this->response($this->view);
	}

	/*========================================================================*/
	/* コンバージョン設定画面出力
	/*========================================================================*/
	public function action_list() {
		global $media_conv_list;

		## 指定クライアントのコンバージョン一覧取得
		foreach ($media_conv_list as $key => $value) {
			$tmp["tool_id"] = $key;
			$tmp["cv_name"] = $value;
			$DB_conv_list[] = $tmp;
		}
		$DB_conv_list = array_merge($DB_conv_list, \Model_Susie_CvNameAccount::get_client_tool($this->IN_data["client_id"]));

		## 指定クライアントのコンバージョン設定取得
		$DB_conv_setting = \Model_Data_Client_Conversion::get($this->IN_data["client_id"]);

		## 連想配列に加工
		$setting_list = \Util_Client_Conversion::make_cv_list(unserialize($DB_conv_setting["cv_list"]));

		## 出力パラメータ
		$this->view->set("DB_conv_list", $DB_conv_list);
		$this->view->set("DB_setting_list", $setting_list);

		## View
		$this->view->set_filename("client/conversion/conv");
		$this->response($this->view);
	}


	/*========================================================================*/
	/* コンバージョン設定
	/*========================================================================*/
	public function action_setting() {

		## コンバージョン選択済み
		if (!empty($this->IN_data["cv_list"])) {

			## コンバージョン設定
			$values = array(
				"client_id" => $this->IN_data["client_id"],
				"cv_list"   => serialize($this->IN_data["cv_list"])
			);

			\Model_Data_Client_Conversion::ins($values);

			$this->alert_message = ALERT_MSG_001;
		}

		## コンバージョン選択テーブルブロック作成
		Response::redirect("client/#/cv/list?client_id=".$this->IN_data["client_id"]);
	}
}
