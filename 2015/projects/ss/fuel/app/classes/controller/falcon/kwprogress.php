<?php

/**
 * falcon主要KW設定コントローラ
 **/

class Controller_Falcon_KwProgress extends Controller_Rest {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	public function before() {
		parent::before();
	}

	// データ取得
	public function get_data () {
		$kw_list = Model_Data_Falcon_KwProgress::get(Input::param('client_id'));
		return $this->response($kw_list, 200);
	}

	// 登録
	public function post_save($client_id) {

		$keyword_list = Input::json("keyword_list");

		// 削除
		Model_Data_Falcon_KwProgress::del($client_id);

		// 登録
		$values = array();
		foreach ($keyword_list as $keyword_info) {

			list($keyword, $matchtype) = explode("//", $keyword_info);

			if (!empty($keyword)) {
				$values[] = array("client_id" => $client_id,
								  "keyword" => $keyword,
								  "match_type" => $matchtype);
			}
		}
		$res = Model_Data_Falcon_KwProgress::ins($values);

		return $this->response(array($res), 200);
	}
}
