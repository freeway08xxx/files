<?php
class Controller_Axis_Export extends Controller_Axis_Base {

	############################################################################
	## 前提共通処理
	############################################################################
	public function before() {

		## super
		parent::before();
	}

	############################################################################
	## レポート出力
	############################################################################
	public function action_create() {

		try {
			## レポート作成に必要なデータ取得
			$form = new \Util_Axis_Material(Input::param());
			$form->setRequestValue();

			## 出力レポート履歴登録
			foreach ($form->date_list as $tmp_date_list) {
				$report_date[] = array("start_date" => $tmp_date_list["start_ymd"], "end_date" => $tmp_date_list["end_ymd"]);
			}
			if ($form->export_format === "excel") {
				$output_report_name = $form->report_name ? $form->report_name . ".xls" : NULL;
			} else {
				$output_report_name = $form->report_name ? $form->report_name . ".tsv" : NULL;
			}
			$values = array("client_id"   => $form->client_id,
							"template_id" => $form->template_id,
							"report_name" => $output_report_name,
							"report_term" => $form->term_type_set,
							"report_date" => serialize($report_date),
							"status_id"   => 0);
			$ret = \Model_Data_Axis_History::ins($values);
			$DB_report_id = $ret[0];

			## レポートデータ取得
			$report_data = new \Util_Axis_ReportData($form);
			$report_data->mainReportData();
			unset($report_data);

			## レポートフォーマット指定
			$report_format = $GLOBALS["report_format_list"][$form->report_type];

			## レポート出力
			$report_create = new \Util_Axis_ReportCreate($form);

			## 期間サマリ
			if ($form->report_type === "summary") {
				if ($form->export_format === "excel") {
					if ($form->device_type === "0") {
						$report = $report_create->createExcelSummaryReport();
					} else {
						$report = $report_create->createExcelSummaryDeviceReport();
					}
				} elseif ($form->export_format === "tsv") {
					if ($form->device_type === "0") {
						$report = $report_create->createTsvSummaryReport();
					} else {
						$report = $report_create->createTsvSummaryDeviceReport();
					}
				}
			## 日別推移
			} elseif ($form->report_type === "daily") {
				if ($form->export_format === "excel") {
					$report = $report_create->createExcelDailyReport();
				} elseif ($form->export_format === "tsv") {
					if ($form->device_type === "0") {
						$report = $report_create->createTsvDailyReport();
					} else {
						$report = $report_create->createTsvDailyDeviceReport();
					}
				} elseif ($form->export_format === "compare") {
					$report = $report_create->createTsvDailyReportWide();
				}
			## 期間比較
			} elseif ($form->report_type === "term_compare") {
				if ($form->export_format === "excel") {
					$report = $report_create->createExcelTermCompareReport();
				} elseif ($form->export_format === "tsv") {
					if ($form->device_type === "0") {
						$report = $report_create->createTsvTermCompareReport();
					} else {
						$report = $report_create->createTsvTermCompareDeviceReport();
					}
				} elseif ($form->export_format === "compare") {
					$report = $report_create->createTsvTermCompareReportWide();
				}
			}

			## レポート保存
			// EXCEL形式
			if ($form->export_format === "excel") {
				$export_file_name = "axis_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".xls";
				$export_file_path = AXIS_REPORT_CREATE_DIR . "/" . $export_file_name;
				$report->reviseFile($report_format, $export_file_name, AXIS_REPORT_CREATE_DIR);
			// TSV形式
			} else {
				## TSV形式に変換
				foreach ($report["report_title"] as $tmp) {
					$report_line[] = $tmp;
				}
				$report_line[] = implode("\t", $report["report_header"]);
				$report_line[] = implode("\t", str_replace("&yen;", "\\ ", $report["total_report"]));
				// 日別推移
				if (!empty($report["total_daily_report_list"])) {
					foreach ($report["total_daily_report_list"] as $total_daily_report) {
						$report_line[] = implode("\t", str_replace("&yen;", "\\ ", $total_daily_report));
					}
				}
				// 期間比較
				if (!empty($report["term_total_report_list"])) {
					foreach ($report["term_total_report_list"] as $term_total_report) {
						$report_line[] = implode("\t", str_replace("&yen;", "\\ ", $term_total_report));
					}
				}
				foreach ($report["summary_report_list"] as $summary_report) {
					$report_line[] = implode("\t", str_replace("&yen;", "\\ ", $summary_report));
				}
				$report_tsv = implode("\n", $report_line);

				$export_file_name = "axis_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".tsv";
				$export_file_path = AXIS_REPORT_CREATE_DIR . "/" . $export_file_name;
				\File::create(AXIS_REPORT_CREATE_DIR, $export_file_name, chr(255) . chr(254) . mb_convert_encoding($report_tsv, "UTF-16LE", "UTF-8"));
			}
			unset($report);

			## 出力レポート履歴更新
			$values = array("file_path"  => $export_file_path,
							"status_id"  => 1,
							"use_memory" => memory_get_peak_usage(true));

			## 完了メール通知
			if ($form->send_mail_flg) {
				\Util_Axis_SendMail::send_mail(\Session::get("user_id_sem"), TRUE);
			}

		} catch (\Exception $e) {
			logger(ERROR, 'axis export error. message:'.$e, 'AxisExport');
			## 出力レポート履歴更新
			$values = array("status_id"  => 2,
							"use_memory" => memory_get_peak_usage(true));

			## 完了メール通知
			\Util_Axis_SendMail::send_mail(\Session::get("user_id_sem"));
		}
		## 出力レポート履歴更新
		\Model_Data_Axis_History::upd($DB_report_id, $values);

		unset($form);

		return TRUE;
	}

	############################################################################
	## レポート表示
	############################################################################
	public function action_display() {

		if(!empty(Input::param())){
			try {
				## レポート作成に必要なデータ取得
				$form = new \Util_Axis_Material(Input::param());
				$form->setRequestValue();

				## レポートデータ取得
				$report_data = new \Util_Axis_ReportData($form);

				$report_data->mainReportData();
				unset($report_data);

			} catch (\Exception $e) {
				logger(ERROR, 'axis display error. message:'.$e, 'AxisExport');
			}

			return Request::forge("axis/export/reportview", false)->execute(array($form));
			}
	}


	############################################################################
	## レポート表示 非同期
	############################################################################

	public function action_displayasync() {

		if(!empty(Input::json("params"))){
			//try {

				$param = Input::json("params");

				$link = "";
				foreach ($param as $key => $value) {
					if(!empty($GLOBALS["params_list"][$key])){

						if($key == "start_date" || $key == "end_date"){
							$link .= $key."=".$value[0]."&";
						}else if(!empty($value) && !is_array($value)){
							$link .= $key."=".$value."&";
						}
					}
				}


				## レポート作成に必要なデータ取得
				$form = new \Util_Axis_Material(Input::json("params"));
				$form->setRequestValue();

				## レポートデータ取得
				$report_data = new \Util_Axis_ReportData($form);

				$is_async = true;
				$tmp = $report_data->mainReportData($is_async);
				unset($report_data);

				$res = array();

				##サマリー
				foreach ($tmp["1"]["summary"] as $key => $value) {
					$res["summary"][$key]           = $value;
					$res["summary"][$key]["link"]   = $link.urlencode('ssAccount[0]={"account_id":"'.$value["account_id"].'"}');;
				}
				$res["summary"] = array_values($res["summary"]);


				##日別
				foreach ($tmp["1"]["daily"] as $key => $value) {
					$res["daily"][$key] = array_values($value);
				}

				##日別合計
				$res["daily"]["total"] = $tmp["1"]["total"];


				##デバイス
				if(!empty($tmp["1"]["device"])){
					foreach ($tmp["1"]["device"] as $key => $value) {
						$res[$key] = $value;
						$res[$key] = array_values($res[$key]);
					}
				}

				##テーブルセル名称
				$res["cell_name"] = $GLOBALS["summary_cell_name"];


			//} catch (\Exception $e) {
				//logger(ERROR, 'axis display error. message:'.$e, 'AxisExport');
			//}







			return $res;
		}
	}





	############################################################################
	## レポート部表示（TSV出力と同内容）
	############################################################################
	public function action_reportview($form) {

		## レポート出力
		$report_create = new \Util_Axis_ReportCreate($form);

		// サマリ項目一覧取得
		$summary_elem_list = $report_create->getSummaryElemList();
		// 出力項目一覧取得
		$report_elem_list  = $report_create->getReportElemList();

		## 期間サマリ
		if ($form->report_type === "summary") {
			if ($form->device_type === "0") {
				$report = $report_create->createTsvSummaryReport();
			} else {
				$report = $report_create->createTsvSummaryDeviceReport();
			}

		## 日別推移
		} elseif ($form->report_type === "daily") {
			if ($form->device_type === "0") {
				$report = $report_create->createTsvDailyReport();
				## View
				$this->view->set("term_date_list", $form->term_date_list);
			} else {
				$report = $report_create->createTsvDailyDeviceReport();
			}

		## 期間比較
		} elseif ($form->report_type === "term_compare") {
			if ($form->device_type === "0") {
				$report = $report_create->createTsvTermCompareReport();
				## View
				$this->view->set("term_count", $form->term_count);
				$this->view->set("date_list", $form->date_list);
			} else {
				$report = $report_create->createTsvTermCompareDeviceReport();
			}
		}

		return $report;

		## ドリルダウン用
		$link_elem_list = array("account_name"  => \Util_Axis_ReportCommon::getElemNameNo($summary_elem_list, "account_name"),
								"campaign_name" => \Util_Axis_ReportCommon::getElemNameNo($summary_elem_list, "campaign_name"),
								"ad_group_name" => \Util_Axis_ReportCommon::getElemNameNo($summary_elem_list, "ad_group_name"));

		## View
		$this->view->set("summary_elem_list", $summary_elem_list);
		$this->view->set("link_elem_list", $link_elem_list);
		$this->view->set("report_elem_list", $report_elem_list);
		$this->view->set("report", $report);

		## View
		if(!empty($form->report_type)){
			if ($form->device_type === "0") {
				$this->view->set_filename("axis/report/table/" . $form->report_type);
			} else {
				$this->view->set_filename("axis/report/table/" . $form->report_type . "_device");
			}
			return Response::forge($this->view);
		}
	}

	############################################################################
	## レポートダウンロード
	############################################################################
	public function action_download($history_id) {

		$DB_history = \Model_Data_Axis_History::get($history_id);
		File::download($DB_history["file_path"], $DB_history["report_name"]);
	}
}
