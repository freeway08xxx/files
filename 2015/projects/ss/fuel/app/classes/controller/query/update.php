<?php
require_once APPPATH."/const/query.php";
class Controller_Query_Update extends Controller_Query_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/share_monitor/entry_action_schedule.php";

	// 前提共通処理
	public function before() {

		// 更新用のFORM受け取り
		parent::before(); // super

		if ( ! $this->is_restful()) {
			## ページ固有JS
			$this->js = array(
				// dummy Angular App
				'common/ng/dummy.js',

				'query/update.js'
			);
		}

		$this->category_name = Input::post("category_name");		// カテゴリー名
		$this->keywords_name = Input::post("keywords_name");		// 単語類
	}

	// キーワード追加登録画面
	public function action_index() {

		$keyword_top['keyword'] = null;
		$data['keyword_list']   = null;

		// カテゴリーリスト取得
		$data_second['category_list'] = Model_Data_Querytargeting::get_all_category();

		// 先頭カテゴリーのキーワード一覧取得
		if (count($data_second['category_list']) > 0) {
			$keyword_top['keyword'] = $data_second['category_list'][0]['category_name'];
			$data['keyword_list'] = Model_Data_Querytargeting::get_all_keyword($keyword_top['keyword']);
		}

		$this->view->set($data);
		$this->view->set($data_second);
		$this->view->set($keyword_top);
		$this->view->set("category_value", "");
		$this->view->set("keywords_value", "");
		$this->view->set_filename('query/querytargeting/update');
	}

	// キーワード追加登録
	public function action_mupdate() {

		// 入力補助
		$this->view->set("category_value", $this->category_name);
		$this->view->set("keywords_value", $this->keywords_name);

		// カテゴリーリスト取得
		$data_second['category_list'] = Model_Data_Querytargeting::get_all_category();

		// 選択されたカテゴリーのキーワード一覧取得
		if ($this->category_name) {
			$data['keyword_list']   = Model_Data_Querytargeting::get_all_keyword($this->category_name);
			$keyword_top['keyword'] = $this->category_name;
		}

		$this->view->set($data);
		$this->view->set($data_second);
		$this->view->set($keyword_top);

		// 未入力アラート
		if (!$this->category_name || !$this->keywords_name) {

			$this->alert_message = BLANK_ERR_MSG;
			$this->view->set_filename('query/querytargeting/update');

		} else {

			// 複数入力キーワードを配列へ整理
			if ($this->keywords_name) {
				$keyword_tmp_list = explode("\r\n", $this->keywords_name);
			}

			// insertカラム
			$columns = array('keyword', 'category_name', 'last_select_datetime', 'insert_datetime');

			// キーワード重複チェック
			foreach ($keyword_tmp_list as $keyword) {
				$keyword_double = Model_Data_Querytargeting::get_keyword_double($this->category_name, $keyword);
				if ($keyword_double || !$keyword) {
					$this->alert_message = "【".$this->category_name."】で【".$keyword."】".DOUBLE_ERR_MSG;
					$this->view->set_filename('query/querytargeting/update');
					break;
				}
			}

			// バルクインサート
			if (!$keyword_double && $keyword) {
				Model_Data_Querytargeting::bulk_ins($columns, $keyword_tmp_list, $this->category_name);
				Response::redirect('query/update/index', 'refresh', 200);
			}
		}
	}

	// キーワード削除
	public function action_mdelete() {
		Model_Data_Querytargeting::del_category($this->category_name);
		Response::redirect('query/querytargeting/index', 'refresh', 200);
	}

	// キーワード一覧表示変更jQuery
	public function action_keywordList() {

		// 先頭カテゴリーのキーワード一覧取得
		$keyword_list = Model_Data_Querytargeting::get_all_keyword($this->category_name);

		$html = '<span class="label label-info">'.$this->category_name.'</span> キーワード一覧';
		$html .= '<table class="keyword-table table table-striped table-bordered table-condensed table-hover">';
		foreach ($keyword_list as $key => $item) {
			if (($key+1) % 5 === 1) {
				$html .= '<tr>';
			}
			$html .= '<td class="">';
			if ($item['delete_flg'] !== '0') {
				$html .= '<span class="label label-danger">停止中</span>';
			}
			$html .= $item['keyword'];
			$html .= '</td>';
			if (($key+1) % 5 === 0) {
				$html .= '</tr>';
			}
		}
		$html .= '</tr>';
		$html .= '</table>';

		return $this->response($html, 200);
	}
}
