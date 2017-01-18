<?php
require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリジャンル管理コントローラ
 */
class Controller_CategoryGenre_Genre_Entrance extends Controller_CategoryGenre_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	// 前提共通処理
	public function before() {
		// super
		parent::before();

	}

	// カテゴリジャンル管理TOP
	public function action_index() {
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user();
		} else {
			$clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
		}
		$this->view->set('table', '');
		$this->view->set('clients', $clients);
		$this->view->set('client_id', '');
		$this->view->set_filename('categorygenre/genre/index');
	}

	public function action_setting($client_id) {
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user();
		} else {
			$clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
		}
		$this->view->set('table', '');
		$this->view->set('clients', $clients);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/genre/index');
	}

	public function action_table($client_id) {

		$data = Model_Data_CategoryGenre::get_for_client_id($client_id);

		$categorygenre_list = array();
		foreach ($data as $key => $value){
			$categorygenre_list[] = array('id'            => $value['id'],
									'client_id'           => $value['client_id'],
									'category_genre_name' => $value['category_genre_name'],
									'category_genre_memo' => $value['category_genre_memo'],
									'user_name'           => $value['user_name'],
									'user_id'             => $value['user_id'],
									'datetime'            => $value['datetime']);

		}

		$this->view->set('categorygenre_list',$categorygenre_list);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/genre/table');
		return Response::forge($this->view);
	}

	//カテゴリジャンル登録モーダル
	public function action_form($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$action_type = Input::post("action_type");
		$genre_id = Input::post("genre_id");

		if($action_type == "view" && $genre_id){
			$genre_info = Model_Data_CategoryGenre::get($genre_id);
			$this->view->set('genre_info',$genre_info);
		}

		$this->view->set('action_type', $action_type);
		$this->view->set_filename('categorygenre/genre/edit');
		return Response::forge($this->view);
	}

	//カテゴリジャンル登録/編集
	public function action_edit($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post("genre_id");
		$values = array();
		$values["client_id"] = $client_id;
		$values["category_genre_name"] = Input::post("category_genre_name");
		$values["category_genre_memo"] = Input::post("category_genre_memo");

		Model_Data_CategoryGenre::ins($genre_id, $values);

		Response::redirect("categorygenre/genre/entrance/setting/" . $client_id);
		return new Response();
	}

	//カテゴリジャンル削除
	public function action_del($client_id) {
		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post('genre_id');
		$genre_info = Model_Data_CategoryGenre::get($genre_id);

		if($genre_info){

			if($genre_id){
				// 変更履歴登録
				$values = array("client_id"   => $client_id,
								"category_genre_id" => $genre_id,
								// 履歴表示用に、変更履歴に削除するジャンル名を登録
								"delete_category_genre_name" => $genre_info["category_genre_name"],
								"action_type_id"  => 5,
								"status_id" => 0
								);
				$ret = \Model_Data_CategoryHistory::ins($values);
				$history_id = $ret[0];

				//削除実行
				$res = \Model_Data_CategoryGenre::del($genre_id);

				if($res){
					// 変更履歴更新
					$values = array("file_path" => "",
									"status_id" => 1
									);
				}else{
					// 変更履歴更新
					$values = array("file_path" => "",
									"status_id" => 2
									);
				}
				\Model_Data_CategoryHistory::upd($history_id, $values);
			}

		}
		Response::redirect("categorygenre/genre/entrance/setting/" . $client_id);
		return new Response();
	}
}
