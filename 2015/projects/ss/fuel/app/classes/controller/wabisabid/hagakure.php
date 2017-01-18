<?php

class Controller_Wabisabid_Hagakure extends Controller_Wabisabid_Base {

	############################################################################
	## トップ画面
	############################################################################
	public function action_index() {

		$this->view->set_filename('wabisabid/hagakure/index');
		$this->response($this->view);
	}

	############################################################################
	## 設定一覧取得
	############################################################################
	public function get_setting($IN_client_id) {

		$response = \Model_Data_WabiSabiH_Setting::get_by_client_id($IN_client_id);
		return $this->response($response);
	}

	############################################################################
	## 設定取得
	############################################################################
	public function get_settingdetail($IN_wabisabi_id) {

		$weekday = [
			0 => WEEKDAY_SUN,
			1 => WEEKDAY_MON,
			2 => WEEKDAY_TUE,
			3 => WEEKDAY_WED,
			4 => WEEKDAY_THU,
			5 => WEEKDAY_FRI,
			6 => WEEKDAY_SAT,
			7 => WEEKDAY_HOLIDAY];

		$response['setting'] = \Model_Data_WabiSabiH_Setting::get($IN_wabisabi_id);
		$response['account'] = \Model_Data_WabiSabiH_SettingAccount::get_list($IN_wabisabi_id);
		$response['cvname']  = \Model_Data_WabiSabiH_SettingCvName::get_list($IN_wabisabi_id);
		$response['filter']  = \Model_Data_WabiSabiH_SettingFilter::get_list($IN_wabisabi_id);

		## 表示用に変換
		// 入札実施パターン
		$tmp_bid_days = explode(',', $response['setting']['bid_days']);
		for($i=0; $i<count($weekday); $i++) {
			if (in_array($weekday[$i], $tmp_bid_days)) {
				$bid_days[] = true;
			} else {
				$bid_days[] = false;
			}
		}
		$response['setting']['bid_days'] = $bid_days;

		// 未参照日パターン
		$tmp_no_sum_days = explode(',', $response['setting']['no_sum_days']);
		for($i=0; $i<count($weekday); $i++) {
			if (in_array($weekday[$i], $tmp_no_sum_days)) {
				$no_sum_days[] = true;
			} else {
				$no_sum_days[] = false;
			}
		}
		$response['setting']['no_sum_days'] = $no_sum_days;

		// フィルタリング
		$filters = [];
		foreach ($response['filter'] as $tmp) {

			if ($tmp['elem_type'] === ELEM_TYPE_CPN) {
				$tmp['filter_item'] = FILTER_ITEM_CPN;
			} else {
				$tmp['filter_item'] = FILTER_ITEM_ADG;
			}

			$tmp['filter_text'] = $tmp['elem_list'];

			if ($tmp['search_type'] === DB_SEARCH_TYPE_OR && $tmp['search_like'] === DB_SEARCH_LIKE_BROAD && $tmp['search_except'] === DB_SEARCH_MATCH) {
				$tmp['filter_cond'] = FILTER_COND_OR_MATCH;
			} elseif ($tmp['search_type'] === DB_SEARCH_TYPE_AND && $tmp['search_like'] === DB_SEARCH_LIKE_BROAD && $tmp['search_except'] === DB_SEARCH_MATCH) {
				$tmp['filter_cond'] = FILTER_COND_AND_MATCH;
			} elseif ($tmp['search_type'] === DB_SEARCH_TYPE_OR && $tmp['search_like'] === DB_SEARCH_LIKE_EXACT && $tmp['search_except'] === DB_SEARCH_MATCH) {
				$tmp['filter_cond'] = FILTER_COND_OR_EXACT;
			} elseif ($tmp['search_type'] === DB_SEARCH_TYPE_OR && $tmp['search_like'] === DB_SEARCH_LIKE_BROAD && $tmp['search_except'] === DB_SEARCH_EXCEPT) {
				$tmp['filter_cond'] = FILTER_COND_OR_EXCEPT;
			}
			$filters[] = $tmp;
		}
		$response['filter'] = $filters;

		// モバイル調整率の上限値
		if (isset($response['setting']['limit_mba'])) {
			$response['setting']['limit_mba'] = \Util_Common_Convert::convert_bid_modifier_to_view($response['setting']['limit_mba']);
		}

		return $this->response($response);
	}

	############################################################################
	## 処理結果一覧取得
	############################################################################
	public function get_result($IN_wabisabi_id) {

		$exec_type_list = [
				EXEC_TYPE_PRE  => ['disp' => '事前処理', 'label' => 'info'],
				EXEC_TYPE_MAIN => ['disp' => '当日処理', 'label' => 'success']];
		$status_list = [
				DB_STATUS_START => ['disp' => '処理中',   'label' => 'info'],
				DB_STATUS_END   => ['disp' => '完了',     'label' => 'success'],
				DB_STATUS_PASS  => ['disp' => 'スキップ', 'label' => 'warning'],
				DB_STATUS_ERROR => ['disp' => 'エラー',   'label' => 'danger']];

		$response = \Model_Data_WabiSabiH_HistoryRes::get_list($IN_wabisabi_id);

		$disp_response = [];
		foreach ($response as $tmp) {
			$tmp['exec_type_label'] = $exec_type_list[$tmp['exec_type']]['label'];
			$tmp['exec_type']       = $exec_type_list[$tmp['exec_type']]['disp'];
			$tmp['status_label']    = $status_list[$tmp['status']]['label'];
			$tmp['status']          = $status_list[$tmp['status']]['disp'];
			$disp_response[] = $tmp;
		}

		return $this->response($disp_response);
	}

	############################################################################
	## 入札履歴一覧取得
	############################################################################
	public function get_bidding($IN_wabisabi_id) {

		$response = \Model_Data_WabiSabiH_HistoryBid::get_bid_count($IN_wabisabi_id);
		return $this->response($response);
	}

	############################################################################
	## 入札履歴詳細一覧取得
	############################################################################
	public function get_biddetail($IN_wabisabi_id, $IN_target_date) {

		$response = \Model_Data_WabiSabiH_HistoryBid::get_bid_list($IN_wabisabi_id, $IN_target_date);

		$disp_response = [];
		foreach ($response as $tmp) {
			$tmp['bid_modifier']     = \Util_Common_Convert::convert_bid_modifier_to_view($tmp['bid_modifier']);
			$tmp['new_bid_modifier'] = \Util_Common_Convert::convert_bid_modifier_to_view($tmp['new_bid_modifier']);
			$disp_response[] = $tmp;
		}

		return $this->response($disp_response);
	}

	############################################################################
	## 入札履歴詳細一覧ダウンロード
	############################################################################
	public function action_biddownload($IN_wabisabi_id, $IN_target_date) {

		$response = \Model_Data_WabiSabiH_HistoryBid::get_bid_list($IN_wabisabi_id, $IN_target_date);

		foreach ($response as $tmp) {
			$tmp['media_id']         = $GLOBALS['media_name_list'][$tmp['media_id']];
			$tmp['bid_modifier']     = \Util_Common_Convert::convert_bid_modifier_to_view($tmp['bid_modifier']);
			$tmp['new_bid_modifier'] = \Util_Common_Convert::convert_bid_modifier_to_view($tmp['new_bid_modifier']);
			$disp_response[] = $tmp;
		}

		$this->format = 'csv';
		$disp_response = $this->response($disp_response);
		$disp_response->set_header('Content-Disposition', 'attachment; filename=wabisabid_hagakure_'.date('YmdHis').'.csv');

		return $disp_response;
	}

	############################################################################
	## 設定削除
	############################################################################
	public function action_delete($IN_wabisabi_id) {

		$response = \Model_Data_WabiSabiH_Setting::del_setting($IN_wabisabi_id);
		return $this->response($response);
	}

	############################################################################
	## ステータス変更
	############################################################################
	public function action_updstatus($IN_wabisabi_id, $IN_status) {

		$response = \Model_Data_WabiSabiH_Setting::upd($IN_wabisabi_id, ['status' => $IN_status]);
		return $this->response($response);
	}

	############################################################################
	## 新規登録
	############################################################################
	public function action_regist() {

		$weekday = [
			0 => WEEKDAY_SUN,
			1 => WEEKDAY_MON,
			2 => WEEKDAY_TUE,
			3 => WEEKDAY_WED,
			4 => WEEKDAY_THU,
			5 => WEEKDAY_FRI,
			6 => WEEKDAY_SAT,
			7 => WEEKDAY_HOLIDAY];

		$wabisabi_id = Input::json('wabisabi_id');

		$setting_values = [
			'wabisabi_name'          => Input::json('wabisabi_name'),
			'client_id'              => Input::json('ssClient'),
			'media_cost'             => Input::json('media_cost'),
			'target_budget_mode'     => Input::json('target_budget_mode'),
			'target_budget'          => Input::json('target_budget'),
			'no_bids_flg'            => Input::json('no_bids_flg'),
			'new_bid_rate_max'       => Input::json('new_bid_rate_max'),
			'new_bid_rate_min'       => Input::json('new_bid_rate_min'),
			'sum_start_date'         => Input::json('sum_start_date'),
			'reference_cost_pattern' => Input::json('reference_cost_pattern'),
			'limit_cpc'              => Input::json('limit_cpc'),
## TODO
			'limit_mba'              => \Util_Common_Convert::convert_bid_modifier_to_actual(Input::json('limit_mba')),
			'target_cpa'             => Input::json('target_cpa'),
			'extcv_exec_hour'        => Input::json('extcv_exec_hour'),
			];

		## DB保存用に置換
		// 入札実施日パターン
		$form_bid_days = Input::json('bid_days');
		for ($i=0; $i<count($form_bid_days); $i++) {
			if ($form_bid_days[$i]) {
				$bid_days[] = $weekday[$i];
			}
		}
		if (!empty($bid_days)) {
			$setting_values['bid_days'] = implode(',', $bid_days);
		}

		// 未参照日パターン
		$form_no_sum_days = Input::json('no_sum_days');
		for ($i=0; $i<count($form_no_sum_days); $i++) {
			if ($form_no_sum_days[$i]) {
				$no_sum_days[] = $weekday[$i];
			}
		}
		if (!empty($no_sum_days)) {
			$setting_values['no_sum_days'] = implode(',', $no_sum_days);
		}

		// 外部CV処理開始時間
		if (!empty(Input::json('extcv_exec_hour'))) {
			$setting_values['extcv_exec_hour'] = Input::json('extcv_exec_hour');
		}

		## 設定
		$db = Database_Connection::instance('administrator');
		$db->start_transaction();
		try {
			// 更新
			if (!empty($wabisabi_id)) {
				\Model_Data_WabiSabiH_Setting::upd($wabisabi_id, $setting_values);
				\Model_Data_WabiSabiH_SettingAccount::del($wabisabi_id);
				\Model_Data_WabiSabiH_SettingCvName::del($wabisabi_id);
				\Model_Data_WabiSabiH_SettingFilter::del($wabisabi_id);
			// 新規登録
			} else {
				$ret = \Model_Data_WabiSabiH_Setting::ins($setting_values);
				$wabisabi_id = $ret[0];
			}

			## アカウント設定
			foreach (Input::json('ssAccount') as $tmp) {
				$account_values = [
					'wabisabi_id'=> $wabisabi_id,
					'media_id'   => $tmp['media_id'],
					'account_id' => $tmp['account_id']];
				$account_values_list[] = $account_values;
			}
			\Model_Data_WabiSabiH_SettingAccount::ins($account_values_list);

			## 外部CV
			if (!empty(Input::json('extcv_list'))) {
				foreach (Input::json('extcv_list') as $tmp) {
					$tmp = explode(';', $tmp['cv_key']);
					$cvname_values = [
						'wabisabi_id' => $wabisabi_id,
						'tool_id'     => $tmp[0],
						'cv_name'     => $tmp[1]];
					$cvname_values_list[] = $cvname_values;
				}
				\Model_Data_WabiSabiH_SettingCvName::ins($cvname_values_list);
			}

			## フィルタリング
			if (!empty(Input::json('filters'))) {
				$filter_values_list = [];
				foreach (Input::json('filters') as $tmp) {

					if ($tmp['filter_cond'] === FILTER_COND_OR_MATCH) {
						$search_type    = DB_SEARCH_TYPE_OR;
						$search_like    = DB_SEARCH_LIKE_BROAD;
						$search_except  = DB_SEARCH_MATCH;
					} elseif ($tmp['filter_cond'] === FILTER_COND_AND_MATCH) {
						$search_type    = DB_SEARCH_TYPE_AND;
						$search_like    = DB_SEARCH_LIKE_BROAD;
						$search_except  = DB_SEARCH_MATCH;
					} elseif ($tmp['filter_cond'] === FILTER_COND_OR_EXACT) {
						$search_type    = DB_SEARCH_TYPE_OR;
						$search_like    = DB_SEARCH_LIKE_EXACT;
						$search_except  = DB_SEARCH_MATCH;
					} elseif ($tmp['filter_cond'] === FILTER_COND_OR_EXCEPT) {
						$search_type    = DB_SEARCH_TYPE_OR;
						$search_like    = DB_SEARCH_LIKE_BROAD;
						$search_except  = DB_SEARCH_EXCEPT;
					}

					$filter_values = [
						'wabisabi_id'   => $wabisabi_id,
						'elem_type'     => ($tmp['filter_item'] === FILTER_ITEM_CPN) ? ELEM_TYPE_CPN : ELEM_TYPE_ADG,
						'elem_list'     => $tmp['filter_text'],
						'search_like'   => $search_like,
						'search_type'   => $search_type,
						'search_except' => $search_except];
					if (!empty($tmp['filter_text'])) {
						$filter_values_list[] = $filter_values;
					}
				}
				if (!empty($filter_values_list)) \Model_Data_WabiSabiH_SettingFilter::ins($filter_values_list);
			}
		} catch (\Exception $e) {
			$db->rollback_transaction();
		}
	}
}
