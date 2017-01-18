<?php
require_once APPPATH."/const/query.php";
class Controller_Query_Querytargeting extends Controller_Query_Base {

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

				'query/querytargeting.js'
			);
		}

		$this->category_name = Input::post("category_name");
		$this->keywords_name = Input::post("keywords_name");
	}

	// キーワード登録画面
	public function action_index() {

		$this->view->set("category_value", "");
		$this->view->set("keywords_value", null);
		$this->view->set_filename('query/querytargeting/index');
	}

	// キーワード登録
	public function action_minsert() {

		// 入力補助
		$this->view->set("category_value", $this->category_name);
		$this->view->set("keywords_value", $this->keywords_name);

		// 未入力アラート
		if (!$this->category_name || !$this->keywords_name) {

			$this->alert_message = BLANK_ERR_MSG;
			$this->view->set_filename('query/querytargeting/index');

		} else {

			// 複数入力キーワードを配列へ整理
			if ($this->keywords_name) {
				$keyword_tmp_list = explode("\r\n", $this->keywords_name);
			}

			// 入力カテゴリーが新規のものか
			$category_double = Model_Data_Querytargeting::get_category_double($this->category_name);

			// insertカラム
			$columns = array('keyword', 'category_name', 'last_select_datetime', 'insert_datetime');

			// キーワード重複チェック
			foreach ($keyword_tmp_list as $keyword) {
				$keyword_double = Model_Data_Querytargeting::get_keyword_double($this->category_name, $keyword);
				if ($keyword_double || !$keyword) {
					$this->alert_message = "【".$this->category_name."】で【".$keyword."】".DOUBLE_ERR_MSG;
					$this->view->set_filename('query/querytargeting/index');
					break;
				}
			}

			// バルクインサート
			if (!$keyword_double && $keyword) {
				Model_Data_Querytargeting::bulk_ins($columns, $keyword_tmp_list, $this->category_name);
				// 新規カテゴリーが登録されたならメール送信
				if (!$category_double) {
					$user_info = Model_Mora_User::get_user_by_id(\Session::get("user_id_sem"));

					// メール本文置換用
					$Mail_body_replace = array("category" => $this->category_name);
					$Mail = \Email::forge(array("is_html" => true));
					$Mail->from(MAIL_FROM_SEARCHSUITE);
					$Mail->to($user_info["mail_address"]);
					$Mail->subject(MAIL_NEW_CATEGORY_QUERYTARGET);
					$Mail_body = \View::forge("email/query/category", $Mail_body_replace);
					$Mail->body($Mail_body);
					$Mail->send();
				}
				Response::redirect('query/querytargeting/index', 'refresh', 200);
			}
		}
	}
}
