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
			$form = new \Util_Axis_Material(Input::json());
			$form->setRequestValue();

			## 出力レポート履歴登録
			foreach ($form->date_list as $tmp_date_list) {
				$report_date[] = array('start_date' => $tmp_date_list['start_ymd'], 'end_date' => $tmp_date_list['end_ymd']);
			}
			if ($form->export_format === 'excel') {
				$output_report_name = $form->report_name ? $form->report_name . '.xls' : NULL;
			} else {
				$output_report_name = $form->report_name ? $form->report_name . '.tsv' : NULL;
			}
			$values = array('client_id'   => $form->client_id,
							'template_id' => $form->template_id,
							'report_name' => $output_report_name,
							'report_term' => $form->term_type_set,
							'report_date' => serialize($report_date),
							'status_id'   => 0);
			$ret = \Model_Data_Axis_History::ins($values);
			$DB_report_id = $ret[0];

			## レポートデータ取得
			$report_data = new \Util_Axis_ReportData($form);
			$report_data->mainReportData();
			unset($report_data);

			## レポート出力
			$report_create = new \Util_Axis_ReportCreate($form);

			## 期間サマリ
			if ($form->report_format === 'summary') {
				if ($form->export_format === 'excel') {
					if ($form->device_type === '0') {
						$report = $report_create->createExcelSummaryReport();
					} else {
						$report = $report_create->createExcelSummaryDeviceReport();
					}
				} elseif ($form->export_format === 'tsv') {
					if ($form->device_type === '0') {
						$report = $report_create->createTsvSummaryReport();
					} else {
						$report = $report_create->createTsvSummaryDeviceReport();
					}
				}
			## 日別推移
			} elseif ($form->report_format === 'daily') {
				if ($form->export_format === 'excel') {
					$report = $report_create->createExcelDailyReport();
				} elseif ($form->export_format === 'tsv') {
					if ($form->device_type === '0') {
						$report = $report_create->createTsvDailyReport();
					} else {
						$report = $report_create->createTsvDailyDeviceReport();
					}
				} elseif ($form->export_format === 'compare') {
					$report = $report_create->createTsvDailyReportWide();
				}
			## 期間比較
			} elseif ($form->report_format === 'term_compare') {
				if ($form->export_format === 'excel') {
					$report = $report_create->createExcelTermCompareReport();
				} elseif ($form->export_format === 'tsv') {
					if ($form->device_type === '0') {
						$report = $report_create->createTsvTermCompareReport();
					} else {
						$report = $report_create->createTsvTermCompareDeviceReport();
					}
				} elseif ($form->export_format === 'compare') {
					$report = $report_create->createTsvTermCompareReportWide();
				}
			}

			## レポート保存
			// EXCEL形式
			if ($form->export_format === 'excel') {
				$export_file_name = 'axis_' . date('YmdHis') . '_' . SEM_PORTAL_PROCESS_ID . '.xls';
				$export_file_path = AXIS_REPORT_CREATE_DIR . '/' . $export_file_name;
				$report->reviseFile(AXIS_REPORT_FORMAT_FILE, $export_file_name, AXIS_REPORT_CREATE_DIR);
			// TSV形式
			} else {
				## TSV形式に変換
				foreach ($report['report_title'] as $tmp) {
					$report_line[] = $tmp;
				}
				$report_line[] = implode("\t", $report['report_header']);
				$report_line[] = implode("\t", str_replace('&yen;', '\ ', $report['total_report']));
				// 日別推移
				if (!empty($report['total_daily_report_list'])) {
					foreach ($report['total_daily_report_list'] as $total_daily_report) {
						$report_line[] = implode("\t", str_replace('&yen;', '\ ', $total_daily_report));
					}
				}
				// 期間比較
				if (!empty($report['term_total_report_list'])) {
					foreach ($report['term_total_report_list'] as $term_total_report) {
						$report_line[] = implode("\t", str_replace('&yen;', '\ ', $term_total_report));
					}
				}
				foreach ($report['summary_report_list'] as $summary_report) {
					$report_line[] = implode("\t", str_replace('&yen;', '\ ', $summary_report));
				}
				$report_tsv = implode("\n", $report_line);

				$export_file_name = 'axis_' . date('YmdHis') . '_' . SEM_PORTAL_PROCESS_ID . '.tsv';
				$export_file_path = AXIS_REPORT_CREATE_DIR . '/' . $export_file_name;
				\File::create(AXIS_REPORT_CREATE_DIR, $export_file_name, chr(255) . chr(254) . mb_convert_encoding($report_tsv, 'UTF-16LE', 'UTF-8'));
			}
			unset($report);

			## 出力レポート履歴更新
			$values = array('file_path'  => $export_file_path,
							'status_id'  => 1,
							'use_memory' => memory_get_peak_usage(true));

			## 完了メール通知
			if ($form->send_mail_flg) {
				\Util_Axis_SendMail::send_mail(\Session::get('user_id_sem'), TRUE);
			}

		} catch (\Exception $e) {
			logger(ERROR, 'axis export error. message:['.$e->getMessage().']', __METHOD__);
			## 出力レポート履歴更新
			$values = array('status_id'  => 2,
							'use_memory' => memory_get_peak_usage(true));

			## 完了メール通知
			\Util_Axis_SendMail::send_mail(\Session::get('user_id_sem'));
		}
		## 出力レポート履歴更新
		\Model_Data_Axis_History::upd($DB_report_id, $values);

		unset($form);

		return TRUE;
	}

	############################################################################
	## レポート表示 非同期
	############################################################################
	public function action_display() {

		try {
			$params = Input::json();

			## レポート作成に必要なデータ取得
			$form = new \Util_Axis_Material($params);
			$form->setRequestValue();

			## レポートデータ取得
			$report_data     = new \Util_Axis_ReportData($form);
			$report_data->mainReportData();
			$tmp_report = $report_data->form->report_list;
			unset($report_data);

			$res = array();
			$report = Request::forge('axis/export/reportview', false)->execute(array($form));
			$report = json_decode($report->controller_instance->response->body);
			$res['title']             = $report->report_title;
			$res['summary_elem_list'] = $report->summary_elem_list;
			$res['report_elem_list']  = $report->report_elem_list;

			if ($form->report_format === 'daily') {
				foreach ($tmp_report[1]['device'] as $date => $date_val) {
					if (!strpos($date , '-')) {
						$device_total[$date] = $tmp_report[1]['device'][$date];
						unset($tmp_report[1]['device'][$date]);
					}
				}
				//account_idがないもの
				foreach ($tmp_report[1]['daily'] as $date => $date_val) {
					foreach ($date_val as $id => $id_val) {
						$info_list = explode( ':', $id );
						if (empty($id_val['account_id'])) {
							$tmp_report[1]['daily'][$date][$id]['account_id'] =  $info_list[2];
						}
					}
				}
			} else {
				foreach ($tmp_report as $term_index => $term_index_val) {
					$tmp_report[$term_index]['device']['all_devices']        =  $term_index_val['summary'];
					$tmp_report[$term_index]['device']['all_devices']['ALL'] =  $term_index_val['total'];

					if ($term_index !==1) $tmp_report[$term_index]['diff']['device']['all_devices'] = $term_index_val['diff']['device']['ALL'];
					unset($tmp_report[$term_index]['diff']['device']['ALL']);
					unset($tmp_report[$term_index]['summary']);
					unset($tmp_report[$term_index]['total']);
					unset($tmp_report[$term_index]['device']['']);
				}
			}

			## 期間サマリ テーブルデータ用に整形
			if ($form->report_format === 'summary') {
				$summary = array();

				// デバイス毎にサマリ
				foreach ($tmp_report[1]['device'] as $device => $device_val) {

					$device = Arr::get($GLOBALS['device_replace_list'], $device);

					foreach ($device_val as $id => $id_val) {
						if ($id === 'ALL') {
							$summary[$device]['ALL']                 = $id_val;
							$summary[$device]['ALL']['account_name'] = '合計';

						} else {
							$summary[$device][$id]         = $id_val;
							$summary[$device][$id]['info'] = $id;
						}
					}
				}

				$res['summary']['device'] = $summary;

			## 日別推移 テーブルデータ用に整形
			} elseif ($form->report_format === 'daily') {
				$daily       = array();
				$all_devices = array();
				$graph       = array();

				##total集計
				foreach ($tmp_report[1]['total'] as $key => $value) {
					$all_devices['total'][$key]           = $value;
					$all_devices['total']['account_name'] = '合計';
					if (in_array($key, $GLOBALS['sum_delete_list'],true)) $summary[$device]['ALL'][$key] = '--';
				}

				##アカウントサマリ集計
				foreach ($tmp_report[1]['summary'] as $id => $value) {
					$all_devices[$id]         = $value;
					$all_devices[$id]['info'] = $id;
				}

				//日別集計
				foreach ($tmp_report[1]['daily'] as $date => $date_val) {
					$date = 'date_'.str_replace('-', '_', $date);
					$res['add_cell'][$date] = $date;
				}

				//デバイス毎 [$device][$date]に順番替え
				foreach ($tmp_report[1]['device'] as $date => $value) {
					foreach ($value as $device => $device_val) {
						if (empty($device)) continue;
						$tmp['daily'][$device][$date] = $device_val;
					}
				}


				foreach ($daily as $device => $value) {
					$renama_device = Arr::get($GLOBALS['device_replace_list'], $device);
					$res['daily']['device'][$renama_device] = $value;
					$res['daily']['graph'][$renama_device]  = $graph[$device];
				}

				$res['daily']['graph']['all_devices']  = $graph['all_devices'];
				$res['daily']['device']['all_devices'] = $all_devices;

			## 期間比較 テーブルデータ用に整形
			} elseif ($form->report_format === 'term_compare') {
				$term_compare = array();

				foreach ($tmp_report as $term_index => $term_index_val) {
					//js側でエラーが起きない様に文字列を変更
					$tmp_term = $form->date_list[$term_index]['start_ymd'] .'__'.$form->date_list[$term_index]['end_ymd'];
					$term     = 'term_'.str_replace('/', '_', $tmp_term);

					//動的追加セル
					$res['add_cell'][$term_index] = $term;


				}

				$res['term_compare']['device'] = $term_compare;
				ksort($res['term_compare']['device']);
			}
		} catch (\Exception $e) {
			logger(ERROR, 'axis display error. message:['.$e->getMessage().']', __METHOD__);
			$res['error'] = $e->getMessage();
			return $res;
		}

		//js側で配列で受け取る為
		$res['diff'] = array_values($res['diff']);
		$devices     = array_keys($res[$form->report_format]['device']);

		foreach ($devices as $device) {
			$res[$form->report_format]['device'][$device] = array_values($res[$form->report_format]['device'][$device]);
		}
		ksort($tmp_report[$term_index]['device']);
		unset($res[$form->report_format]['device']['other']);

		return $res;
	}

	############################################################################
	## レポート部表示（TSV出力と同内容）
	############################################################################
	public function action_reportview($form) {

		## レポート出力
		$report_create = new \Util_Axis_ReportCreate($form);

		## 期間サマリ
		if ($form->report_format === 'summary') {
			if ($form->device_type === '0') {
				$report = $report_create->createTsvSummaryReport();
			} else {
				$report = $report_create->createTsvSummaryDeviceReport();
			}

		## 日別推移 
		} elseif ($form->report_format === 'daily') {
			if ($form->device_type === '0') {
				$report = $report_create->createTsvDailyReport();
			} else {
				$report = $report_create->createTsvDailyDeviceReport();
			}

		## 期間比較
		} elseif ($form->report_format === 'term_compare') {
			if ($form->device_type === '0') {
				$report = $report_create->createTsvTermCompareReport();
			} else {
				$report = $report_create->createTsvTermCompareDeviceReport();
			}
		}

		return $report;
	}

	############################################################################
	## レポート部表示（TSV出力と同内容）
	############################################################################
	public function action_reportelem() {

		try {
			## レポート作成に必要なデータ取得
			$form = new \Util_Axis_Material(Input::json());
			$form->setRequestValue();

			## レポート出力
			$report_create = new \Util_Axis_ReportCreate($form);
			$report_elem_list = $report_create->getReportElemList();

		} catch (\Exception $e) {
			logger(ERROR, 'axis reportelem error. message:['.$e->getMessage().']', __METHOD__);
		}

		return $report_elem_list;
	}

	############################################################################
	## レポートダウンロード
	############################################################################
	public function action_download($history_id) {

		$DB_history = \Model_Data_Axis_History::get($history_id);
		File::download($DB_history['file_path'], $DB_history['report_name']);
	}
}
