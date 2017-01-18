<?php
require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリ管理コントローラ
 */
class Controller_CategoryGenre_Category_Entrance extends Controller_CategoryGenre_Base
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
		$this->view->set_filename('categorygenre/category/index');
	}

	public function action_setting($client_id) {
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user();
		} else {
			$clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
		}

		$top = Request::forge('categorygenre/category/entrance/top', false)->execute(array($client_id));
		$this->view->set_safe('top', $top);

		$this->view->set('table', '');
		$this->view->set('clients', $clients);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/category/index');
	}

	//カテゴリジャンルモーダル画面
	public function action_top($client_id) {
		if(Request::is_hmvc()) {

			$data = Model_Data_CategoryGenre::get_for_client_id($client_id);

			$genre_list = array();
			foreach ($data as $key => $value){
				$genre_list[] = array('id'                    => $value['id'],
										'client_id'           => $value['client_id'],
										'category_genre_name' => $value['category_genre_name'],
										'user_name'           => $value['user_name'],
										'datetime'            => $value['datetime']);

			}

			$this->view->set('table', '');
			$this->view->set('genre_list',$genre_list);
			$this->view->set('client_id', $client_id);
			$this->view->set_filename('categorygenre/category/top');
			return Response::forge($this->view);
		}
	}

	public function action_table($client_id) {

		$genre_id = Input::post("genre_id");
		$category_elem = Input::post("category_elem");

		$data = Model_Data_Category::get_for_category_genre_id($category_elem, $client_id, $genre_id);

		$elem_list = array();
		foreach ($data as $key => $value){
			$elem_list[] = array('id'           => $value['id'],
								'client_id'     => $value['client_id'],
								'category_name' => $value['category_name'],
								'category_memo' => $value['category_memo'],
								'sort_order'    => $value['sort_order'],
								'user_name'     => $value['user_name'],
								'user_id'       => $value['user_id'],
								'datetime'      => $value['datetime'],
								'category_elem' => $category_elem
								);

		}
		$this->view->set('elem_list',$elem_list);
		$this->view->set('category_genre_id',$genre_id);
		$this->view->set('category_elem',$category_elem);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/category/table');
		return Response::forge($this->view);
	}

	//カテゴリ登録モーダル
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
		$category_id = Input::post("category_id");
		$category_elem = Input::post("category_elem");

		if($action_type == "view" && $category_id){
			$category_info = Model_Data_Category::get($category_elem, $category_id);
			$this->view->set('category_info',$category_info);
		}

		$elem_count = Model_Data_Category::get_count($category_elem, $client_id, $genre_id);
		$this->view->set('elem_count',$elem_count["count_elem"]);

		$this->view->set('genre_id', $genre_id);
		$this->view->set('category_elem', $category_elem);
		$this->view->set('action_type', $action_type);
		$this->view->set_filename('categorygenre/category/edit');
		return Response::forge($this->view);
	}

	//カテゴリ一括登録モーダル
	public function action_bulkform($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post("genre_id");
		$category_elem = Input::post("category_elem");

		$this->view->set('genre_id', $genre_id);
		$this->view->set('category_elem', $category_elem);
		$this->view->set_filename('categorygenre/category/edit_bulk');
		return Response::forge($this->view);
	}

	//カテゴリ登録/編集
	public function action_edit($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post("genre_id");
		$category_id = Input::post("category_id");
		$category_elem = Input::post("category_elem");
		$sort_order = Input::post("sort_order");

		if(!$sort_order){
			$res = \Model_Data_Category::get_max_sort_order($category_elem, $client_id, $genre_id);
			if(isset($res["max_count"])){
				$sort_order = $res["max_count"] + 1;
			}else{
				$sort_order = 1;
			}
		}

		$values = array();
		$values["client_id"] = $client_id;
		$values["category_genre_id"] = $genre_id;
		$values["category_name"] = Input::post("category_name");
		$values["category_memo"] = Input::post("category_memo");
		$values["sort_order"] = $sort_order;

		Model_Data_Category::ins($category_elem, $category_id, $values);

		Response::redirect("categorygenre/category/entrance/setting/" . $client_id);
		return new Response();
	}

	//カテゴリ登録/編集
	public function action_editbulk($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$category_name_list_tmp = Input::post("category_name_list");
		//改行コードを統一
		$category_name_list_tmp = str_replace(array("\r\n","\r"), "\n", $category_name_list_tmp);
		//単語毎に分割
		$category_name_list = explode("\n", $category_name_list_tmp);

		$category_id = null;
		$genre_id = Input::post("genre_id");
		$category_elem = Input::post("category_elem");

		$res = \Model_Data_Category::get_max_sort_order($category_elem, $client_id, $genre_id);

		if(isset($res["max_count"])){
			$sort_order = $res["max_count"] + 1;
		}else{
			$sort_order = 1;
		}
		foreach ($category_name_list as $name){
			if (empty($name)) continue;

			$values[] = array("id" => $category_id,
							  "client_id" => $client_id,
							  "category_genre_id" => $genre_id,
							  "category_name" => $name,
							  "sort_order" => $sort_order
							);
			$sort_order++;
		}
		\Model_Data_Category::setting($category_elem, $values);

		Response::redirect("categorygenre/category/entrance/setting/" . $client_id);
		return new Response();
	}

	//カテゴリ削除
	public function action_del($client_id) {
		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$category_id = Input::post('category_id');
		$category_elem = Input::post('category_elem');

		$category_info = \Model_Data_Category::get($category_elem, $category_id);
		if($category_id && $category_elem && isset($category_info["category_genre_id"])){
			// 変更履歴登録
			$values = array("client_id"   => $client_id,
							"category_genre_id" => $category_info["category_genre_id"],
							"delete_category_name" => $category_info["category_name"],
							"delete_category_element" => $category_elem,
							"action_type_id"  => 6,
							"status_id" => 0
							);
			$ret = \Model_Data_CategoryHistory::ins($values);
			$history_id = $ret[0];

			//削除実行
			$res = \Model_Data_Category::del($category_info["category_genre_id"], $category_elem, $category_id);

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
		Response::redirect("categorygenre/category/entrance/setting/" . $client_id);
		return new Response();
	}
}
