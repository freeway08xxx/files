<?php

/**
 * Falcon テンプレート コントローラ
 *
 * @return HTML_View
 */
class Controller_Falcon_Template extends Controller_Falcon_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index() {
		$this->view->set_filename('falcon/report/template');
		$this->response($this->view);
	}

	/**
	 * テンプレート一覧を取得
	 *
	 * @access public
	 * @return array
	 */
	public function get_list() {
		$client_id = Input::param('client_id');

		$data = Model_Data_Falcon_Template::get($client_id);

		$template_list = array();
		$exclusion_info = array();
		foreach ($data as $key => $value) {
			$template_list[] = array(
				'id'                      => $value['id'],
				'client_id'               => $value['client_id'],
				'template_name'           => $value['template_name'],
				'report_type'             => $value['report_type'],
				'device_type'             => $value['device_type'],
				'media_cost'              => $value['media_cost'],
				'category_genre_id'       => $value['category_genre_id'],
				'category_genre_name'     => $value['category_genre_name'],
				'category_type_id'        => $value['category_type_id'],
				'report_term'             => $value['report_term'],
				'term_count'              => $value['term_count'],
				'export_component_id_flg' => $value['export_component_id_flg'],
				'export_last_month_flg'   => $value['export_last_month_flg'],
				'send_mail_flg'           => $value['send_mail_flg'],
				'custom_format_file_path' => $value['custom_format_file_path'],
				'report_name'             => $value['report_name'],
				'template_memo'           => $value['template_memo'],
				'user_name'               => $value['user_name'],
				'user_id'                 => $value['user_id'],
				'datetime'                => $value['datetime']
			);

			if(!isset($exclusion_info[$value['id']])) {
				$exclusion_info[$value['id']] = Model_Data_Falcon_CampaignExclusionSetting::get_info($value['id']);
			}
		}

		$ret = array(
			'template_list'  => $template_list,
			'exclusion_info' => $exclusion_info
		);

		$this->response($ret);
	}

	/**
	 * テンプレート詳細情報を取得
	 *
	 * @access public
	 * @return array
	 */
	public function get_detail() {
		$client_id   = Input::param('client_id');
		$template_id = Input::param('template_id');

		$data = array();
		$data['account_info'] = Model_Data_Falcon_AccountSetting::get_info($client_id, $template_id);

		/**
		 * line
		 */
		$data['line_info'] = Model_Data_Falcon_LineSetting::get_info($client_id, $template_id);

		/**
		 * sheet
		 */
		$data['sheet_info'] = Model_Data_Falcon_SheetSetting::get_info($client_id, $template_id);

		/**
		 * aim
		 */
		$aim_info = Model_Data_Falcon_AimSetting::get_info($client_id, $template_id);

		/** Angular Models用に整形 */
		$arr = array();
		foreach ($aim_info as $key => $value) {
			$arr[$value["target"]][$value["element"]] = (float)$value["value"];
		}
		$data['aim_info'] = $arr;

		$this->response($data);
	}

	/**
	 * テンプレート追加
	 *
	 * @access public
	 * @return void
	 */
	public function post_add() {
		/**
		 * formdataはjsonにシリアライズされたtextが来る
		 * PostData 自体はMultipart-Form-Data で来るので、
		 * custom_format file があれば $_FILES に格納される -> material.php にてチェック
		 */
		$json_input = Input::post("form");
		$input_form = json_decode($json_input, true);

		$form = new \Util_Falcon_Material($input_form);
		$form->setRequestValue();

		/**
		 * template_id があれば上書き処理(Model で判別してるので、nullはそのままパス)
		 */
		$template_id = \Util_Falcon_TemplateRegist::set_form_setting($form->template_id, $form);

		return $this->response((int)$template_id);
	}

	/**
	 * テンプレート削除
	 *
	 * @access public
	 * @return void
	 */
	public function post_delete() {
		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		} elseif (!$this->admin_flg) {
			$checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$client_id   = Input::param('client_id');
		$template_id = Input::param('template_id');

		if ($template_id) {
			$is_deleted = \Model_Data_Falcon_Template::del_template($template_id);

			//カスタムフォーマット削除
			$file_path = '/falcon_custom_format_'.$client_id.'_'.$template_id.'.xlsx';
			\Util_Falcon_TemplateRegist::deleteCustomFormat($file_path);
		}

		return $this->response($is_deleted);
	}
}
