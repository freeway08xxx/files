<?php
require_once APPPATH."/const/query.php";
class Controller_Query_Execute extends Controller_Query_Base {

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

				'query/execute.js'
			);
		}

		$this->category_name = Input::post("category_name");		// カテゴリー名
		$this->keywords_name = Input::post("keywords_name");		// 単語類
	}

	// クエリターゲティング実行画面
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
		$this->view->set_filename('query/querytargeting/execute');
	}

	// 実行処理
	public function action_start() {

		if(Input::is_ajax()){
			$curl = Request::forge("http://".JENKINS_HOST."/job/".urlencode(JENKINS_JOB_QUERY_TARGETING)."/buildWithParameters?token=query_targeting&category=".$this->category_name."&user_id_sem=".\Session::get("user_id_sem"), "curl");
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();
			exit ("受付が完了しました。完了後メールでお知らせ致します。");
		}
	}
}
