<?php

/**
 * falconレポート出力系コントローラ
 **/
class Controller_Falcon_Export extends Controller_Falcon_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	// レポート画面TOP出力
	public function action_start($client_id) {
		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		} elseif (!$this->admin_flg) {
			$checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
		}
		if (!$checker) {
			return new Response(false, 404);
		}


		try {

			/**
			 * formdataはjsonにシリアライズされたtextが来る
			 * PostData 自体はMultipart-Form-Data で来るので、
			 * custom_format file があれば $_FILES に格納される -> material.php にてチェック
			 */
			$json_input = Input::post("form");
			$input_form = json_decode($json_input, true);

			$form = new \Util_Falcon_Material($input_form);
			$form->setRequestValue();

			// 出力レポート履歴登録
			foreach ($form->date_list as $tmp_date_list) {
				$report_date[] = array("start_date" => $tmp_date_list["start_ymd"], "end_date" => $tmp_date_list["end_ymd"]);
			}
			$values = array("client_id"   => $form->client_id,
							"template_id" => $form->template_id,
							"report_name"  => $form->output_file_name,
							"report_term" => $form->term_type_set,
							"report_date" => serialize($report_date),
							"status_id"   => 0);
			$ret = \Model_Data_Falcon_History::ins($values);
			$DB_report_id = $ret[0];

			// レポートの取得
			$report_data = new \Util_Falcon_ReportData($form);
			$report_list = $report_data->getReportData();
			unset($report_data);

			// レポートフォーマット指定
			if (!empty($form->custom_format_file_path)) {
				$report_format = $form->custom_format_file_path;
			} else {
				$report_format = $GLOBALS["report_format"][$form->report_type][$form->device_type];
			}

			// 日別推移
			if ($form->report_type === "daily") {
				// 目標の取得
				$form->getClientAim();

				// レポート作成
				$report_create = new \Util_Falcon_ReportCreate($form);

				// レポート出力
				$report = $report_create->createDailyReport($report_list, $report_format);

			// 期間比較
			} elseif ($form->report_type === "term_compare") {
				// レポート作成
				$report_create = new \Util_Falcon_ReportCreate($form);

				// レポート出力
				$report = $report_create->createTermCompareReport($report_list, $report_format);
			}

			// レポート保存
			$export_file_name = "falcon_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".xlsx";
			$export_file_path = FALCON_REPORT_CREATE_DIR . "/" . $export_file_name;
			$report->save($export_file_path);
			unset($report);
			unset($report_list);

			// 出力レポート履歴更新
			$values = array("file_path"	=> $export_file_path,
							"status_id"  => 1,
							"use_memory" => memory_get_peak_usage(true));
			// 完了メール通知
			if ($form->send_mail === 1) {
				\Util_Falcon_SendMail::send_mail($form->client_id, \Session::get("user_id_sem"), TRUE);
			}
		} catch (\Exception $e) {
			logger(ERROR, 'falcon export error. message:'.$e, 'FalconExport');
			// 出力レポート履歴更新
			$values = array("file_path"	=> "",
							"status_id"	=> 2,
							"use_memory" => memory_get_peak_usage(true));
		}
		\Model_Data_Falcon_History::upd($DB_report_id, $values);

		// $client_info = \Model_Mora_Client::get_client_name($client_id);
		// $name = ($client_info['client_name']) ? $client_info['company_name']."//".$client_info['client_name'] : $client_info['company_name'];
		// $message = $name." のレポート集計が完了しています。";
		// $send_url = '/sem/new/falcon/entrance/setting/'.$client_id;
		// \Util_Common_Websocket::send_info(Session::get("user_id_sem"), $send_url, $message);

		/**
		 * 出力に利用したカスタムフォーマットファイルを削除
		 * ※ テンプレート保存していない場合
		 */
		if ($form->is_customformat_temporary_use) {
			\Util_Falcon_TemplateRegist::deleteCustomFormat($form->custom_format_file_path);
		}

		return new Response();
	}

	// ファイルダウンロード
	public function action_download($history_id) {
		$export_info = Model_Data_Falcon_History::get_export_info($history_id);

		if ($export_info && !empty($export_info['file_path'])) {
			$output_file_name = ($export_info['report_name']) ?
				$export_info['report_name'] : $export_info["client_id"]."_".date("YmdHis");

			$output_file_name = $output_file_name.'.xlsx';

			File::download($export_info['file_path'], $output_file_name);
		} else {
			return new Response(false, 404);
		}
	}

	// フォーマットファイルダウンロード
	public function action_formatdownload($output_file_name=NULL) {
		$export_file_path = Input::param("download_file");
		File::download($export_file_path, $output_file_name);
	}
}
