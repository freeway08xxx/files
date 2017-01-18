<?php
class Controller_Reacquire_Index extends Controller_Reacquire_Base
{
	/**
	 * @access private
	 * @var array $report_type_name_map
	 */
	private $report_type_name_map = [
		'ACCOUNT' => 'キーワード',
		'KEYWORD' => 'キーワード',
		'AD'      => '広告'
	];

	public function get_get_difference_data() {
		$account_list      = Model_Mora_Account::get_account_by_ids(array_filter(explode(':', Input::get('account_id_list'))));
		$report_type       = Input::get('report_type');
		$FromDate          = new DateTime(Input::get('from_date'));
		$ToDate            = new DateTime(Input::get('to_date'));
		$difference_exists = (bool)Input::get('difference_exists');

		$account_report_list = Model_Susie_AccountDailyReport::getByIdsForDiff($FromDate, $ToDate, array_keys($account_list));

		if ($report_type === 'ACCOUNT' || $report_type === 'KEYWORD') {
			$comparison_report_list = Model_Listingreport_TransactionDailyKeywordReport::getByIdsForDiff($FromDate, $ToDate, $account_list);
		} else if ($report_type === 'AD') {
			$comparison_report_list = Model_Listingreport_TransactionDailyAdReport::getByIdsForDiff($FromDate, $ToDate, $account_list);
		}

		$response = [];

		foreach ($account_list as $account) {
			if (isset($account_report_list[$account['id']])) {
				$account_report = $account_report_list[$account['id']];
			} else {
				$account_report = [
					'imp'   => 0,
					'click' => 0,
					'cost'  => 0,
					'conv'  => 0
				];
			}

			if (isset($comparison_report_list[$account['id']])) {
				$comparison_report = $comparison_report_list[$account['id']];
			} else {
				$comparison_report = [
					'imp'   => 0,
					'click' => 0,
					'cost'  => 0,
					'conv'  => 0
				];
			}

			$report_difference = [
				'imp'   => $comparison_report['imp'] - $account_report['imp'],
				'click' => $comparison_report['click'] - $account_report['click'],
				'cost'  => $comparison_report['cost'] - $account_report['cost'],
				'conv'  => $comparison_report['conv'] - $account_report['conv']
			];

			if ($difference_exists) {
				if ($report_difference['imp'] === 0 && $report_difference['click'] === 0 && $report_difference['cost'] === 0 && $report_difference['conv'] === 0) {
					continue;
				}
			}

			$response[] = [
				'media_id'         => $account['media_id'],
				'media_name'       => ucfirst($GLOBALS["media_name_list"][$account['media_id']]),
				'account_id'       => $account['id'],
				'account_name'     => $account['account_name'],
				'report_list' => [
					[
						'report_type' => 'アカウント',
						'report'      => $account_report
					],
					[
						'report_type' => $this->report_type_name_map[$report_type],
						'report'      => $comparison_report
					],
					[
						'report_type' => '差分',
						'report'      => $report_difference
					]
				],
				'is_checked'       => TRUE
			];
		}

		if (count($response)) {
			return $this->response([
				'status'  => 'success',
				'message' => '',
				'data'    => $response
			]);
		} else {
			return $this->response([
				'status'  => 'success',
				'message' => '対象データが存在しません。',
				'data'    => []
			]);
		}
	}

	public function post_submit() {
		$report_type     = Input::json('report_type');
		$client_id       = Input::json('client_id');
		$FromDate        = new DateTime(Input::json('from_date'));
		$ToDate          = new DateTime(Input::json('to_date'));
		$account_id_list = array_filter(explode(':', Input::json('account_id_list')));

		try {
			$params = http_build_query([
				'token'           => JENKINS_TOKEN_REACQUIRE,
				'report_type'     => $report_type,
				'client_id'       => $client_id,
				'from_date'       => $FromDate->format('Y-m-d'),
				'to_date'         => $ToDate->format('Y-m-d'),
				'account_id_list' => str_replace('"', '\"', json_encode($account_id_list)),
				'mail_address'    => Session::get('user_mail_address')
			]);

			$curl = Request::forge('http://' . JENKINS_HOST . '/job/' . urlencode(JENKINS_JOB_REACQUIRE) . '/buildWithParameters?' . $params, 'curl');

			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();
		} catch (Exception $Exception) {
			return $this->response(['status' => 'error', 'message' => $Exception->getMessage()]);
		}

		return $this->response(['status' => 'success']);
	}
}
