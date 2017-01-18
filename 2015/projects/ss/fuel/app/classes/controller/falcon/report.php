<?php

/**
 * Falcon レポート作成画面 コントローラ
 *
 * @return HTML_View
 */
class Controller_Falcon_Report extends Controller_Falcon_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return view
	 */
	public function action_index() {
		global $report_sheet_list, $report_type_list, $report_category_type_list,
			$report_category_element_list, $report_elem_list, $report_elem_type_list,
			$report_formula_cell_type_list, $report_device_line_list, $report_aim_list, $report_format;

		// Delete Non-Need Const
		unset($report_type_list['summary']);

		$data = [];
		$data['format']['report_type_list']             = $report_type_list;
		$data['format']['report_category_type_list']    = $report_category_type_list;
		$data['format']['report_category_element_list'] = $report_category_element_list;

		// view parts
		$this->view->set("view_setting_format", View::forge('falcon/report/parts/setting_format', $data['format']));
		$this->view->set("view_setting_display", View::forge('falcon/report/parts/setting_display'));
		$this->view->set("view_setting_sheet", View::forge('falcon/report/parts/setting_sheet'));
		$this->view->set("view_setting_kw", View::forge('falcon/report/parts/setting_kw'));
		$this->view->set("view_setting_aim", View::forge('falcon/report/parts/setting_aim'));
		$this->view->set("view_setting_cp", View::forge('falcon/report/parts/setting_cp'));

		$this->view->set_filename('falcon/report/index');
		$this->response($this->view);
	}

	/**
	 * テンプレート一覧画面のロード
	 *
	 * @access public
	 * @return view
	 */
	public function action_template() {
		$template_data = [];

		$this->view->set_filename('falcon/report/template');
		$this->response($this->view);
	}

	/**
	 * クライアント別レポート設定のロード
	 *
	 * @access public
	 * @return view
	 */
	public function get_clientConfig(){
		$client_id = Input::param('client_id');

		$data = Util_Falcon_Entrance::get_ext_cv($client_id);
		if ($data['ext_cv_name_list']) {
			$i = 0;
			$ext_cv_sorted_arr = array();
			foreach ($data['ext_cv_name_list'] as $key => $value) {
				$value['ext_key']           = $key;
				$value['key']               = $value['name'];
				$value['formula_cell_type'] = $value['cell_type'];
				$value['view_flg']          = '1';

				unset($value['cell_type']);
				$ext_cv_sorted_arr[$i] = $value;
				$i++;
			}
			$data['ext_cv_name_list'] = $ext_cv_sorted_arr;
		};

		$data['client_id'] = $client_id;
		$data['category_genre_list']  = Model_Data_CategoryGenre::get_for_client_id($client_id);
		$data['bigcategory_count']    = Model_Data_Category::get_category_count("category_big_id", $client_id);
		$data['middlecategory_count'] = Model_Data_Category::get_category_count("category_middle_id", $client_id);
		$data['category_count']       = Model_Data_Category::get_category_count("category_id", $client_id);

		return $this->response($data);
	}

	/**
	 * カテゴリ名リストを取得
	 *
	 * @access public
	 * @return view
	 */
	public function get_categoryList() {
		$client_id         = Input::param('client_id');
		$category_genre_id = Input::param('category_genre_id');

		$data = array();

		$data['category_big_id']    = Model_Data_Category::get_category_list("category_big_id", $client_id, $category_genre_id);
		$data['category_middle_id'] = Model_Data_Category::get_category_list("category_middle_id", $client_id, $category_genre_id);
		$data['category_id']       = Model_Data_Category::get_category_list("category_id", $client_id, $category_genre_id);

		return $this->response($data);
	}
}
