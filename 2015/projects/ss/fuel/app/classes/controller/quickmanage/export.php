<?php
class Controller_QuickManage_Export extends Controller_QuickManage_Base {

	## loginユーザ権限チェック用URL MyPageで集計処理実行の為,クラス内で別URLを定義
	public $access_url = "/sem/new/quickmanage/export.php";

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/*========================================================================*/
	/* レポート出力
	/*========================================================================*/
	public function action_createfile() {

		if (Request::is_hmvc()) {
			try {
				## レポート作成に必要なデータ取得
				$form = new \Util_QuickManage_Material(Input::param());
				$form->setRequestValue();

				## 出力レポート履歴登録
				foreach ($form->date_list as $tmp_date_list) {
					$report_date[] = array("start_date" => $tmp_date_list["start_ymd"], "end_date" => $tmp_date_list["end_ymd"]);
				}
				$output_report_name = $form->report_name ? $form->report_name . ".xls" : NULL;
				$values = array("template_id" => $form->template_id,
								"report_name" => $output_report_name,
								"report_term" => $form->term_type_set,
								"report_date" => serialize($report_date),
								"status_id"   => 0);
				$ret = \Model_Data_QuickManage_History::ins($values);
				$DB_report_id = $ret[0];

				## レポートデータ取得
				$report_data = new \Util_QuickManage_ReportData($form);
				$report_data->mainReportData();
				unset($report_data);

				## レポートフォーマット指定
				$report_format = $GLOBALS["report_format_list"][$form->report_type];

				## レポート出力
				$report_create = new \Util_QuickManage_ReportCreate($form);

				if ($form->report_type === "summary") {
					$report = $report_create->createSummaryReport();
				} elseif ($form->report_type === "daily") {
					$report = $report_create->createDailyReport();
				} elseif ($form->report_type === "term_compare") {
					$report = $report_create->createTermCompareReport();
				}

				## レポート保存
				$export_file_name = "quickmanage_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".xls";
				$export_file_path = QUICKMANAGE_REPORT_CREATE_DIR . "/" . $export_file_name;
				$report->reviseFile($report_format, $export_file_name, QUICKMANAGE_REPORT_CREATE_DIR);
				unset($report);

				## 出力レポート履歴更新
				$values = array("file_path"  => $export_file_path,
								"status_id"  => 1,
								"use_memory" => memory_get_peak_usage(true));

				## 完了メール通知
				if ($form->send_mail_flg) {
					// \Util_QuickManage_SendMail::send_mail(\Session::get("user_id_sem"), TRUE);
				}

			} catch (\Exception $e) {
				logger(ERROR, 'quick manage export error. message:'.$e, 'QuickManageExport');
				## 出力レポート履歴更新
				$values = array("status_id"  => 2,
								"use_memory" => memory_get_peak_usage(true));

				## 完了メール通知
				\Util_QuickManage_SendMail::send_mail(\Session::get("user_id_sem"));
			}
			## 出力レポート履歴更新
			\Model_Data_QuickManage_History::upd($DB_report_id, $values);

			unset($form);
		}
		return;
	}

	/*========================================================================*/
	/* レポート表示
	/*========================================================================*/
	public function action_display($type = null) {
		if (Request::is_hmvc()) {
			try {
				## レポート作成に必要なデータ取得
				$form = new \Util_QuickManage_Material(Input::param());
				$form->setRequestValue();

				## レポートデータ取得
				$report_data = new \Util_QuickManage_ReportData($form);
				$report_data->mainReportData();
				unset($report_data);

			} catch (\Exception $e) {
				logger(ERROR, 'quick manage export error. message:'.$e, 'QuickManageExport');
			}

			if ($type === 'row') {
				return Response::forge(array('report' => $form->report_list, 'forecast' => $form->forecast_list));
			}

			$reportview = Request::forge("quickmanage/export/reportview", false)->execute(array($form));
			return $reportview;
		}
	}

	/*========================================================================*/
	/* レポート部分出力
	/*========================================================================*/
	public function action_reportview($form) {
		$summary_elem_list = array();
		$report_elem_list  = array();

		if ($form->report_type === "summary") {
			## サマリ単位
			switch ($form->summary_type) {
				case "bureau":
					$summary_elem_list = array("bureau_name"  => "局");
					break;
				case "user":
					$summary_elem_list = array("bureau_name"  => "局",
											   "user_name"    => "担当");
					break;
				case "company":
					$summary_elem_list = array("company_name" => "クライアント");
					break;
				case "client":
					$summary_elem_list = array("bureau_name"  => "局",
											   "user_name"    => "担当",
											   "client_name"  => "クライアント");
					break;
				case "account":
					$summary_elem_list = array("bureau_name"  => "局",
											   "user_name"    => "担当",
											   "client_name"  => "クライアント");
					break;
				default:
					break;
			}

			if ($form->use_customer_class_summary) {
				$summary_elem_list += array("customer_class_name" => "顧客区分");
			}
			if ($form->use_business_type_summary) {
				$summary_elem_list += array("business_type_name"  => "業種");
			}
			if ($form->use_media_summary) {
				$summary_elem_list += array("media_name"          => "媒体");
			}
			if ($form->use_product_summary) {
				$summary_elem_list += array("product_name"        => "プロダクト");
			}
			if ($form->use_device_summary) {
				$summary_elem_list += array("device_name"         => "デバイス");
			}
			if ($form->use_aim_list) {
				## サマリ種別がアカウント、もしくはサマリオプションが顧客区分・デバイスの場合、目標は出力しない
				if ($form->summary_type === "account" || $form->use_customer_class_summary || $form->use_device_summary) {
					$summary_elem_list += array("-"               => "目標");
				} else {
					$summary_elem_list += array("cl_aim_budget"   => "目標");
				}
			}
			if ($form->summary_type === "account") {
				$summary_elem_list += array("account_name"        => "アカウント名",
											"account_id"          => "アカウントID",
											"budget"              => "予算");
			}

			## 実績出力項目
			if (!$form->show_only_cost) {
				$report_elem_list += array("imp"            => "Imp",
										   "click"          => "Click",
										   "ctr"            => "CTR",
										   "cpc"            => "CPC");
			}
			$report_elem_list += array("cost"               => "Cost");
			if ($form->use_discount) {
				$report_elem_list += array("cost_discount"         => "Cost(値引後)",
										   "gross_margin_discount" => "粗利(値引後)",
										   "discount_rate"         => "値引率",
										   "discount_value"        => "値引額");
			}
			if (!$form->show_only_cost) {
				$report_elem_list += array("rank"           => "Rank",
										   "conv"           => "CVs",
										   "cvr"            => "CVR",
										   "cpa"            => "CPA");
			}
			if ($form->use_ext_cv_list) {
				$report_elem_list += array("ext_cv"         => "外部CVs",
										   "ext_cvr"        => "外部CVR",
										   "ext_cpa"        => "外部CPA");
			}

			$this->view->set("FORM_date_list", $form->date_list[1]);
			$this->view->set("FORM_total_report", $form->report_list[1]["total"]);
			$this->view->set("FORM_summary_report_list", $form->report_list[1]["summary"]);
			$this->view->set("FORM_sumopt_aim_list", $form->sumopt_aim_list);

		} elseif ($form->report_type === "daily") {
			## サマリ単位
			// サマリ種別によって可変表示
			switch ($form->summary_type) {
				case "bureau":
					$summary_elem_list = array("bureau_name" => "局");
					break;
				case "user":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当");
					break;
				case "company":
					$summary_elem_list = array("company_name" => "クライアント");
					break;
				case "client":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当",
											   "client_name" => "クライアント");
					break;
				case "account":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当",
											   "client_name" => "クライアント");
					if ($form->use_customer_class_summary) {
						$summary_elem_list += array("customer_class_name" => "顧客区分");
					}
					if ($form->use_business_type_summary) {
						$summary_elem_list += array("business_type_name" => "業種");
					}
					if ($form->use_media_summary) {
						$summary_elem_list += array("media_name" => "媒体");
					}
					if ($form->use_product_summary) {
						$summary_elem_list += array("product_name" => "プロダクト");
					}
					if ($form->use_device_summary) {
						$summary_elem_list += array("device_name" => "デバイス");
					}
					$summary_elem_list += array("account_name" => "アカウント名",
												"account_id"   => "アカウントID");
					if ($form->use_aim_list) {
						// サマリ種別がアカウントの場合、目標は出力しない
						$summary_elem_list += array("-" => "目標");
					}
					break;
				default:
					break;
			}
			// サマリ種別以外によって可変表示
			if ($form->summary_type !== "account") {
				// 顧客区分
				if ($form->use_customer_class_summary) {
					$summary_elem_list += array("customer_class_name" => "顧客区分");
				}
				// 業種
				if ($form->use_business_type_summary) {
					$summary_elem_list += array("business_type_name" => "業種");
				}
				// 媒体
				if ($form->use_media_summary) {
					$summary_elem_list += array("media_name" => "媒体");
				}
				// プロダクト
				if ($form->use_product_summary) {
					$summary_elem_list += array("product_name" => "プロダクト");
				}
				// デバイス
				if ($form->use_device_summary) {
					$summary_elem_list += array("device_name" => "デバイス");
				}
				// 目標出力
				if ($form->use_aim_list) {
					// サマリオプションが顧客区分・デバイスの場合、目標は出力しない
					if ($form->use_customer_class_summary || $form->use_device_summary) {
						$summary_elem_list += array("-" => "目標");
					} else {
						$summary_elem_list += array("cl_aim_budget" => "目標");
					}
				}
			}
			// 可変表示後にALL項目表示
			$summary_elem_list += array("all" => "ALL");
			// 着地予想表示
			if ($form->use_forecast) {
				$summary_elem_list += array("forecast" => "着地予想");
			}

			## 実績出力項目
			// COSTのみ表示でない
			if (!$form->show_only_cost) {
				$report_elem_list += array("imp"   => "Imp",
										   "click" => "Click",
										   "ctr"   => "CTR",
										   "cpc"   => "CPC");
			}
			$report_elem_list += array("cost" => "Cost");
			// 値引適用
			if ($form->use_discount) {
				$report_elem_list += array("cost_discount"         => "Cost(割引後)",
										   "gross_margin_discount" => "粗利(値引後)",
										   "discount_rate"         => "割引率",
										   "discount_value"        => "割引額");
			}
			// COSTのみ表示でない
			if (!$form->show_only_cost) {
				$report_elem_list += array("rank" => "Rank",
										   "conv" => "CVs",
										   "cvr"  => "CVR",
										   "cpa"  => "CPA");
			}
			// COSTのみ表示でない and 外部CV
			if (!$form->show_only_cost && $form->use_ext_cv_list) {
				$report_elem_list += array("ext_cv"  => "外部CVs",
										   "ext_cvr" => "外部CVR",
										   "ext_cpa" => "外部CPA");
			}

			## 集計期間
			$term_date_list = array();
			foreach($form->term_date_list as $term_date) {
				$week_day = date("w", strtotime($term_date));
				$term_date_list += array($term_date => str_replace("-", "/", substr($term_date, 5, 5)) . "(" . \Util_Common_Date::get_weekday_name($week_day, false) . ")");
			}
			$this->view->set("term_date_list", $term_date_list);

			$this->view->set("FORM_term_date_list", $form->term_date_list);
			$this->view->set("FORM_use_forecast", $form->use_forecast);
			$this->view->set("FORM_total_report", $form->report_list[1]["total"]);
			$this->view->set("FORM_summary_report_list", $form->report_list[1]["summary"]);
			$this->view->set("FORM_daily_report_list", $form->report_list[1]["daily"]);
			$this->view->set("FORM_sumopt_aim_list", $form->sumopt_aim_list);
			$this->view->set("FORM_forecast_list", $form->forecast_list);

		} elseif ($form->report_type === "term_compare") {
			## サマリ単位
			// サマリ種別によって可変表示
			switch ($form->summary_type) {
				case "bureau":
					$summary_elem_list = array("bureau_name" => "局");
					break;
				case "user":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当");
					break;
				case "company":
					$summary_elem_list = array("company_name" => "クライアント");
					break;
				case "client":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当",
											   "client_name" => "クライアント");
				break;
				case "account":
					$summary_elem_list = array("bureau_name" => "局",
											   "user_name"   => "担当",
											   "client_name" => "クライアント");
					if ($form->use_customer_class_summary) {
						$summary_elem_list += array("customer_class_name" => "顧客区分");
					}
					if ($form->use_business_type_summary) {
						$summary_elem_list += array("business_type_name" => "業種");
					}
					if ($form->use_media_summary) {
						$summary_elem_list += array("media_name" => "媒体");
					}
					if ($form->use_product_summary) {
						$summary_elem_list += array("product_name" => "プロダクト");
					}
					if ($form->use_device_summary) {
						$summary_elem_list += array("device_name" => "デバイス");
					}
					$summary_elem_list += array("account_name" => "アカウント名",
												"account_id"   => "アカウントID");
					if ($form->use_aim_list) {
						// サマリ種別がアカウントの場合、目標は出力しない
						$summary_elem_list += array("-" => "目標");
					}
					break;
				default:
					break;
			}
			// サマリ種別以外によって可変表示
			if ($form->summary_type !== "account") {
				// 顧客区分
				if ($form->use_customer_class_summary) {
					$summary_elem_list += array("customer_class_name" => "顧客区分");
				}
				// 業種
				if ($form->use_business_type_summary) {
					$summary_elem_list += array("business_type_name" => "業種");
				}
				// 媒体
				if ($form->use_media_summary) {
					$summary_elem_list += array("media_name" => "媒体");
				}
				// プロダクト
				if ($form->use_product_summary) {
					$summary_elem_list += array("product_name" => "プロダクト");
				}
				// デバイス
				if ($form->use_device_summary) {
					$summary_elem_list += array("device_name" => "デバイス");
				}
				// 目標出力
				if ($form->use_aim_list) {
					// サマリオプションが顧客区分・デバイスの場合、目標は出力しない
					if ($form->use_customer_class_summary || $form->use_device_summary) {
						$summary_elem_list += array("-" => "目標");
					} else {
						$summary_elem_list += array("cl_aim_budget" => "目標");
					}
				}
			}
			// 可変表示後にALL項目表示
			$summary_elem_list += array("all" => "ALL");
			// 着地予想表示
			if ($form->use_forecast) {
				$summary_elem_list += array("forecast" => "着地予想");
			}

			## 実績出力項目
			// COSTのみ表示でない
			if (!$form->show_only_cost) {
				$report_elem_list += array("imp"   => "Imp",
										   "click" => "Click",
										   "ctr"   => "CTR",
										   "cpc"   => "CPC");
			}
			$report_elem_list += array("cost" => "Cost");
			// 値引適用
			if ($form->use_discount) {
				$report_elem_list += array("cost_discount"         => "Cost(割引後)",
										   "gross_margin_discount" => "粗利(値引後)",
										   "discount_rate"         => "割引率",
										   "discount_value"        => "割引額");
			}
			// COSTのみ表示でない
			if (!$form->show_only_cost) {
				$report_elem_list += array("rank" => "Rank",
										   "conv" => "CVs",
										   "cvr"  => "CVR",
										   "cpa"  => "CPA");
			}
			// COSTのみ表示でない and 外部CV
			if (!$form->show_only_cost && $form->use_ext_cv_list) {
				$report_elem_list += array("ext_cv"  => "外部CVs",
										   "ext_cvr" => "外部CVR",
										   "ext_cpa" => "外部CPA");
			}

			$this->view->set("FORM_use_forecast", $form->use_forecast);
			$this->view->set("FORM_date_list", $form->date_list);
			$this->view->set("FORM_term_count", $form->term_count);
			$this->view->set("FORM_report_list", $form->report_list);
			$this->view->set("FORM_total_report", $form->all_term_total);
			$this->view->set("FORM_all_term_summary_list", $form->all_term_summary_list);
		}

		$this->view->set("summary_elem_list", $summary_elem_list);
		$this->view->set("report_elem_list", $report_elem_list);

		// 共通
		$this->view->set("FORM_summary_type", $form->summary_type);
		$this->view->set("FORM_use_customer_class_summary", $form->use_customer_class_summary);
		$this->view->set("FORM_use_business_type_summary", $form->use_business_type_summary);
		$this->view->set("FORM_use_media_summary", $form->use_media_summary);
		$this->view->set("FORM_use_product_summary", $form->use_product_summary);
		$this->view->set("FORM_use_device_summary", $form->use_device_summary);
		$this->view->set("FORM_use_aim_list", $form->use_aim_list);
		$this->view->set("FORM_use_ext_cv_list", $form->use_ext_cv_list);
		$this->view->set("FORM_use_discount", $form->use_discount);
		$this->view->set("FORM_show_only_cost", $form->show_only_cost);

		## View
		$this->view->set_filename("quickmanage/report/table/" . $form->report_type);
		return Response::forge($this->view);
	}

	/*========================================================================*/
	/* レポートダウンロード
	/*========================================================================*/
	public function action_download($history_id) {

		$DB_history = \Model_Data_QuickManage_History::get($history_id);
		File::download($DB_history["file_path"], $DB_history["report_name"]);
	}
}
