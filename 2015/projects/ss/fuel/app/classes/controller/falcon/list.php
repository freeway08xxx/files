<?php

/**
 * Falcon 作成済みレポート一覧 コントローラ
 *
 * @return HTML_View
 */
class Controller_Falcon_List extends Controller_Falcon_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index() {
		$this->view->set_filename('falcon/list/index');
		$this->response($this->view);
	}

	/**
	 * 作成履歴データを取得
	 *
	 * @access public
	 * @return view
	 */
	public function get_history() {
		$client_id = Input::param('client_id');

		$data = Model_Data_Falcon_History::get($client_id, 10);

		$history_list = array();
		foreach ($data as $key => $value){
			//出力中の場合は出力時間・完了日時を表示しない
			if($value['status_id'] != 0){
				$export_time = str_replace(" seconds ago", "", Date::time_ago(strtotime($value['created_at']), strtotime($value['updated_at']), 'second'));
				$export_time = round($export_time / 60, 0);
				if($export_time < 1){
					$export_time = 1;
				}
				$updated_at = str_replace("-", ".", $value['updated_at']);
			}else{
				$export_time = '--';
				$updated_at = '--';
			}

			//実際の集計期間表示用
			$dsp_report_date  = "";
			$report_date_list = unserialize($value['report_date']);
			foreach ($report_date_list as $report_date) {
				$dsp_report_date .= $report_date['start_date']."～".$report_date['end_date']."\n";
			}

			//テンプレートIDが存在しない場合はnullに更新
			$template_id = NULL;
			if(!isset($template_info_list[$value['template_id']]) && isset($value['template_id']) && !isset($skip_list[$value['template_id']])){
				$template_info = Model_Data_Falcon_Template::get_info($value['template_id']);
				if(isset($template_info['id'])){
					$template_info_list[$template_info['id']] = $template_info;
				}else{
					$skip_list[$value['template_id']] = $value['template_id'];
				}
			}
			if(isset($template_info_list[$value['template_id']])){
				$template_id = $template_info_list[$value['template_id']]['id'];
			}

			$value['created_at'] = str_replace("-", ".", $value['created_at']);

			$value['report_name'] = !empty($value['report_name']) ? $value['report_name'] : "レポート名なし";

			$history_list[] = array('id'          => $value['id'],
									'client_id'   => $value['client_id'],
									'file_path'   => $value['file_path'],
									'report_name' => $value['report_name'],
									'template_id' => $template_id,
									'status_id'   => $value['status_id'],
									'export_time' => $export_time,
									'report_term' => $value['report_term'],
									'report_date' => $dsp_report_date,
									'user_name'   => $value['user_name'],
									'created_at'  => $value['created_at'],
									'updated_at'  => $updated_at);
		}

		$this->response($history_list);
	}
}
