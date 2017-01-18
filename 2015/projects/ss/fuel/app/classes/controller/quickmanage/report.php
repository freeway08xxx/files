<?php
class Controller_QuickManage_Report extends Controller_QuickManage_Base {

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/*========================================================================*/
	/* レポート画面出力
	/*========================================================================*/
	public function action_index() {

		## レポート作成画面
		$HMVC_report = Request::forge("quickmanage/report/formpane", false)->execute();

		## レポート作成履歴
		$HMVC_history = Request::forge("quickmanage/report/history", false)->execute();

		## View
		$this->view->set_safe("HMVC_report", $HMVC_report);
		$this->view->set_safe("HMVC_history", $HMVC_history);
		$this->view->set_filename("quickmanage/report/index");

		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}

	/*========================================================================*/
	/* レポート作成画面出力
	/*========================================================================*/
	public function action_formpane() {

		if (Request::is_hmvc()) {

			$HMVC_form = Request::forge("quickmanage/report/form", false)->execute();

			## テンプレート一覧
			$HMVC_template = Request::forge("quickmanage/report/template", false)->execute();

			## View
			$this->view->set_safe("HMVC_form", $HMVC_form);
			$this->view->set_safe("HMVC_template", $HMVC_template);

			$this->view->set_filename("quickmanage/report/formpane");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* フォーム部分出力
	/*========================================================================*/
	public function action_form() {

		if (Request::is_hmvc()) {
			global $report_type_list, $summary_type_list, $summary_option_list, $position_id_list, $option_list;

			$bureau_list = array();
			$bur_tmp =  \Model_Mora_Bureau::get_list(1);
			foreach ($bur_tmp as $i) {
				if (!empty($i["bureau_name"])) {
					$bureau_list[$i["bureau_id"]] = $i["bureau_name"];
				}
			}
			$business_type_list = array();
			$bus_tmp = \Model_Mora_BusinessTypeMaster::get_list();
			foreach ($bus_tmp as $i) {
				if (!empty($i["business_type_name"])) {
					$business_type_list[$i["business_type_id"]] = $i["business_type_name"];
				}
			}

			## マスタ系
			$OUT_data["report_type_list"]    = $report_type_list;		# レポート種別
			$OUT_data["summary_type_list"]   = $summary_type_list;		# サマリ種別
			$OUT_data["summary_option_list"] = $summary_option_list;	# サマリオプション
			$OUT_data["position_id_list"]    = $position_id_list;		# 担当種別
			$OUT_data["option_list"]         = $option_list;			# オプション
			$OUT_data["bureau_list"]         = $bureau_list;			# 局一覧
			$OUT_data["business_type_list"]  = $business_type_list;		# 業種一覧

			## 出力パラメータ
			$this->view->set($OUT_data);

			## View
			$this->view->set_filename("quickmanage/report/form");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* テンプレート一覧出力
	/*========================================================================*/
	public function action_template() {

		if (Request::is_hmvc()) {
			## View
			$this->view->set_filename("quickmanage/report/template");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* 作成済みレポート一覧出力
	/*========================================================================*/
	public function action_history() {

		if (Request::is_hmvc()) {

			## 最新レポート履歴取得
			$DB_history_list = \Model_Data_QuickManage_History::get_list(HISTORY_LIMIT);

			$history_list = array();
			foreach ($DB_history_list as $key => $value) {

				## 出力中の場合は出力時間・完了日時を表示しない
				if ($value["status_id"] != 0) {
					$export_time = str_replace(" seconds ago", "", Date::time_ago(strtotime($value["created_at"]), strtotime($value["updated_at"]), "second"));
					$created_at = $value["updated_at"];
				} else {
					$export_time = "--";
					$created_at = "--";
				}

				## 実際の集計期間表示用
				$dsp_report_date  = "";
				$report_date_list = unserialize($value["report_date"]);
				foreach ($report_date_list as $report_date) {
					$dsp_report_date .= $report_date["start_date"]."～".$report_date["end_date"]."\n";
				}

				$history_list[] = array("id"          => $value["id"],
										"report_name" => !empty($value["report_name"]) ? preg_replace("/\.xls$/", "", $value["report_name"]) : "レポート名なし",
										"file_path"   => $value["file_path"],
										"template_id" => $value["template_id"],
										"status_id"   => $value["status_id"],
										"export_time" => $export_time,
										"report_term" => $value["report_term"],
										"report_date" => $dsp_report_date,
										"user_name"   => $value["user_name"],
										"created_at"  => $value["created_at"],
										"updated_at"  => $created_at);
			}

			## 出力パラメータ
			$this->view->set("history_list", $history_list);

			## View
			$this->view->set_filename("quickmanage/report/history");
			return Response::forge($this->view);
		}
	}

 	/*========================================================================*/
	/* レポート出力結果を表示
	/*========================================================================*/
	public function action_display(){

		## レポート結果テーブル
		$HMVC_report = Request::forge("quickmanage/export/display", false)->execute();

		$HMVC_history = Request::forge("quickmanage/report/history", false)->execute();

		## View
		$this->view->set_safe("HMVC_report", $HMVC_report);
		$this->view->set_safe("HMVC_history", $HMVC_history);
		$this->view->set_filename("quickmanage/report/index");
	}

	/*========================================================================*/
	/* レポートを作成
	/*========================================================================*/
	public function action_export() {
		Request::forge("quickmanage/export/createfile", false)->execute();

		// 作成済みリストを表示
		Response::redirect("quickmanage/#/report/?tab=list");
	}

	/*========================================================================*/
	/* テンプレート情報取得
	/*========================================================================*/
	public function get_templatelist() {

		global $report_type_list, $summary_type_list, $report_term_list, $summary_option_list, $option_list;

		## テンプレート一覧取得
		$DB_template_list = \Model_Data_QuickManage_Template::get_list();

		$template_list = array();
		foreach ($DB_template_list as $key => $value) {

			## 表示名称とdata valueをセット
			$report_type_arr  = array(
				"value" => $value["report_type"],
				"label" => $report_type_list[$value["report_type"]]
			);
			$summary_type_arr = array(
				"value" => $value["summary_type"],
				"label" => $summary_type_list[$value["summary_type"]]
			);
			$report_term_arr  = array(
				"value" => $value["report_term"],
				"label" => $report_term_list[$value["report_type"]][$value["report_term"]]
			);

			## デシリアライズ
			$summary_option_saved = unserialize($value["summary_option_list"]);
			$option_saved         = unserialize($value["option_list"]);
			$filter_arr           = unserialize($value["filter_list"]);

			# オプションパラメータをAngular用に整形
			$summary_option_arr = array();
			$option_arr         = array();
			foreach ($summary_option_list as $key => $v) {
				$summary_option_arr[$key] = (!empty($summary_option_saved[$key])) ? true : false;
			}
			foreach ($option_list as $key => $v) {
				$option_arr[$key] = (!empty($option_saved[$key])) ? true: false;
			}

			$report_name = "";
			if (!empty($value["report_name"])) {
				$report_name = preg_replace("/\.xls$/", "", $value["report_name"]);
			}

			$template_list[] = array(
				"id"            => $value["id"],
				"template_name" => $value["template_name"],
				"report_type"   => $report_type_arr,
				"summary_type"  => $summary_type_arr,
				"report_term"   => $report_term_arr,

				"position_id"   => $value["position_id"],
				"term_count"    => $value["term_count"],

				"summary_option" => $summary_option_arr,
				"option"         => $option_arr,
				"filter"         => $filter_arr,

				"report_name"   => $report_name,
				"send_mail_flg" => (boolean)$value["send_mail_flg"],

				"template_memo" => $value["template_memo"],
				"user_name"     => $value["user_name"],
				"datetime"      => $value["datetime"]
			);
		}

		return $this->response($template_list);
	}

	/*========================================================================*/
	/* テンプレート追加・更新
	/*========================================================================*/
	public function post_instemplate() {
		$form = Input::json();

		$template_id = \Util_QuickManage_TemplateRegist::set_form_setting($form["id"], $form);

		return $this->response($template_id);
	}

	/*========================================================================*/
	/* テンプレート削除
	/*========================================================================*/
	public function post_deltemplate() {

		## 入力パラメータ
		$IN_template_id = Input::json("id");

		$result = "";
		if (isset($IN_template_id)) {
			## テンプレート削除
			$result = \Model_Data_QuickManage_Template::del($IN_template_id);
		}
		return $this->response($result);
	}
}
