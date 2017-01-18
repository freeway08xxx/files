<?php
class Controller_Axis_Report extends Controller_Axis_Base {

	############################################################################
	## 前提共通処理
	############################################################################
	public function before() {

		## super
		parent::before();
	}

	############################################################################
	## メインコンテンツ出力
	############################################################################
	public function action_index($reportview = null) {

		if (isset($reportview)) {
			## レポート出力結果
			$HMVC_report = $reportview;
		} else {
			## レポート作成画面出力
			$HMVC_report = Request::forge("axis/report/report", false)->execute();
		}

		## 最新レポート履歴画面出力
		$HMVC_history = Request::forge("axis/report/history", false)->execute();

		## View
		$this->view->set_safe("HMVC_report", $HMVC_report);
		$this->view->set_safe("HMVC_history", $HMVC_history);

		$this->view->set_filename("axis/report/index");

		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}

	############################################################################
	## レポート作成画面出力
	############################################################################
	public function action_report() {

		if (Request::is_hmvc()) {
			## レポート作成フォーム出力
			$HMVC_form = Request::forge("axis/report/form", false)->execute();

			## テンプレート一覧出力
			$HMVC_template = Request::forge("axis/report/template", false)->execute();

			## 結果テンプレートテーブル出力
			$HMVC_result = Request::forge("axis/report/result", false)->execute();

			## View
			$this->view->set_safe("HMVC_form", $HMVC_form);
			$this->view->set_safe("HMVC_template", $HMVC_template);
			$this->view->set_safe("HMVC_result", $HMVC_result);


			$this->view->set_filename("axis/report/report");

			return Response::forge($this->view);
		}
	}

	############################################################################
	## レポート作成フォーム出力
	############################################################################
	public function action_form() {

		if (Request::is_hmvc()) {
			## View
			$this->view->set_filename("axis/report/form");

			return Response::forge($this->view);
		}
	}

	############################################################################
	## テンプレート出力
	############################################################################
	public function action_template() {

		if (Request::is_hmvc()) {
			## View
			$this->view->set_filename("axis/report/template");

			return Response::forge($this->view);
		}
	}

	############################################################################
	## 結果テンプレートテーブル出力
	############################################################################
	public function action_result() {

		if (Request::is_hmvc()) {
			## View
			$this->view->set_filename("axis/report/result");

			return Response::forge($this->view);
		}
	}

	############################################################################
	## テンプレート一覧出力
	############################################################################
	public function post_templatelist() {

		global $report_type_list, $summary_type_list, $report_term_list;

		if (Input::is_ajax()) {
			## 入力パラメータ
			$IN_client_id = Input::json("client_id");

			## テンプレート一覧取得
			$DB_template_list = \Model_Data_Axis_Template::get_list($IN_client_id);

			$template_list = array();
			foreach ($DB_template_list as $key => $value) {

				## アカウント一覧取得
				$DB_account_list = \Model_Data_Axis_AccountSetting::get_list($value["id"]);

				$report_type  = array(
					"value" => $value["report_type"],
					"label" => $report_type_list[$value["report_type"]]
				);
				$report_term  = array(
					"value" => $value["report_term"],
					"label" => $report_term_list[$value["report_type"]][$value["report_term"]]
				);
				$summary_type = array(
					"value" => $value["summary_type"],
					"label" => $summary_type_list[$value["summary_type"]]
				);

				$template_list[] = array(
					"id"                 => $value["id"],
					"client_id"          => $value["client_id"],
					"account_list"       => $DB_account_list,
					"template_name"      => $value["template_name"],
					"report_type"        => $report_type,
					"report_term"        => $report_term,
					"term_count"         => $value["term_count"],
					"category_type"      => $value["category_type"],
					"summary_type"       => $summary_type,
					"device_type"        => $value["device_type"],
					"media_cost"         => $value["media_cost"],
					"option_list"        => unserialize($value["option_list"]),
					"ext_cv_list"        => unserialize($value["ext_cv_list"]),
					"filter_list"        => unserialize($value["filter_list"]),
					"report_filter_list" => unserialize($value["report_filter_list"]),
					"export_format"      => $value["export_format"],
					"report_name"        => preg_replace("/\.xls$/", "", $value["report_name"]),
					"template_memo"      => $value["template_memo"],
					"send_mail_flg"      => (boolean)$value["send_mail_flg"],
					"user_name"          => $value["user_name"],
					"datetime"           => $value["datetime"],
				);
			}

			return $this->response($template_list);
		}
	}

	############################################################################
	## 最新レポート履歴画面出力
	############################################################################
	public function action_history() {

		if (Request::is_hmvc()) {
			## 最新レポート履歴取得
			$DB_history_list = \Model_Data_Axis_History::get_list(\Session::get("user_id_sem"), AXIS_HISTORY_LIMIT);

			$history_list = array();
			foreach ($DB_history_list as $key => $value) {

				## 出力中の場合は出力時間・完了日時を表示しない
				if ($value["status_id"] !== "0") {
					$export_time = str_replace(" seconds ago", "", Date::time_ago(strtotime($value["created_at"]), strtotime($value["updated_at"]), "second"));
					$export_time = round($export_time / 60, 0);
					if ($export_time < 1){
					    $export_time = 1;
					}
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

				$history_list[] = array("id"           => $value["id"],
										"report_name"  => !empty($value["report_name"]) ? preg_replace("/\.(xls|tsv)$/", "", $value["report_name"]) : "レポート名なし",
										"file_path"    => $value["file_path"],
										"company_name" => $value["company_name"],
										"client_name"  => $value["client_name"],
										"template_id"  => $value["template_id"],
										"status_id"    => $value["status_id"],
										"export_time"  => $export_time,
										"report_term"  => $value["report_term"],
										"report_date"  => $dsp_report_date,
										"user_name"    => $value["user_name"],
										"created_at"   => $value["created_at"],
										"updated_at"   => $created_at);
			}

			## View
			$this->view->set("DB_history_list", $history_list);
			$this->view->set_filename("axis/report/history");

			return Response::forge($this->view);
		}
	}

	############################################################################
	## レポート出力
	############################################################################
	public function action_export() {

		## 入力パラメータ
		$IN_client_id = Input::get("ssClient") ? Input::get("ssClient") : Input::post("ssClient");

		## レポート出力
		Request::forge("axis/export/create", false)->execute();

		## 最新レポート履歴
		Response::redirect("axis/#/report/?tab=list&ssClient=" . $IN_client_id);
	}

	############################################################################
	## レポート表示
	############################################################################
	public function action_display() {

		## レポート出力結果
		$HMVC_report = Request::forge("axis/export/display", false)->execute();

		## 最新レポート履歴画面出力
		$HMVC_history = Request::forge("axis/report/history", false)->execute();



		## View
		$this->view->set_safe("HMVC_report", $HMVC_report);
		$this->view->set_safe("HMVC_history", $HMVC_history);
		$this->view->set_filename("axis/report/index");
	}




	############################################################################
	## テンプレート登録・更新
	############################################################################
	public function post_instemplate() {

		if (Input::is_ajax()) {
			$template_id = \Util_Axis_TemplateRegist::set_form_setting(Input::json());

			return $this->response($template_id);
		}
	}

	############################################################################
	## テンプレート削除
	############################################################################
	public function post_deltemplate() {

		if (Input::is_ajax()) {
			## 入力パラメータ
			$IN_template_id = Input::json("template_id");

			$result = FALSE;
			if (isset($IN_template_id)) {
			    ## テンプレート削除
			    $result = \Model_Data_Axis_Template::delete_template($IN_template_id);
			}

			return $this->response($result);
		}
	}
}
