<?php
require_once APPPATH . "/const/eagle.php";


/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Eagle_History extends Controller_Eagle_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_index() {
		$this->view->set_filename('eagle/history/index');
		$this->response($this->view);
	}

	/**
	 * 履歴一覧 
	 * 
	 * @access public
	 */
	public function get_list() {
		$eagleAll = Model_Data_Eagle::get_all(0,30);

		## ユーザー全取得
		$users = Model_Mora_User::find_all();

		## クライアント情報取得
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user_by_unique();
		} else {
			$clients = Model_Mora_Client::get_for_user_by_unique(Session::get('user_id_sem'));
		}

		## 表示用にフォーマット
		foreach($eagleAll as $key => $eagle){
			$client_id = $eagle['client_id'];
			if(empty($clients[$client_id])){
				unset($eagleAll[$key]);
				continue;
			}
			$client = $clients[$client_id];

			## クライアント名追加
			if(!empty($client['client_name'])){
				$eagleAll[$key]['client_name'] = $client['company_name'].'::'.$client['client_name'];
			}else{
				$eagleAll[$key]['client_name'] = $client['company_name'];
			}

			## ユーザー名取得
			$eagleAll[$key]['created_user_name'] = $users[$eagle['created_user']] ? $users[$eagle['created_user']]['user_name'] : '';

			## 更新対象名追加
			$eagleAll[$key]['exec_name'] = Util_Eagle_Common::get_exec_name($eagle['exec_code']);

			## 実行詳細取得
			$eagleAll[$key]['detail'] = Util_Eagle_Common::get_option_to_detail(json_decode($eagle['options'],true));
		}

		$response['eagleAll'] = array_values($eagleAll);
		return $this->response($response); 
	}

	/**
	 * 更新結果ファイルをダウンロードする 
	 * 
	 * @access public
	 */
	public function action_download_result_file($eagle_id,$request=null){

		if (!Request::is_hmvc()) {
			## eagle情報
			$eagle = Model_Data_Eagle::get_one($eagle_id);
			## 更新対象情報取得
			$target_datas = Model_Data_Eagle_Update_Target::find_by_eagle_id($eagle_id);
		} else {
			## eagle情報
			$eagle = $request['eagle'];
			## 更新対象情報取得
			$target_datas = $request['eagle_target'];
		}

		if(empty($target_datas)){
			return false;
		}


		/**
		 *	1.更新対象レコードから掲載マスター情報にフィルタリングするパラメーターを作成する
		 * 	2.フィルタリング情報を元にマスター情報をフィルタリング検索
		 * 	3.両配列のハッシュにより情報マッピング
		 * 	4.csv表示用に整形
		 *	5.レスポンス
	 	*/


		## 対象情報からハッシュの作成とフィルターパラメーターの作成を行う
		$base_components = array('account','campaign','adgroup','keyword', 'ad');
		$hash_code_columns = array();
		$target_datas_by_hash_key = array();
		$filter = array();
		foreach($target_datas as $key => $value){
			$data = json_decode($value['data'], true);
			$target = $data['target'];

			if($key == 0){
				$hash_code_columns = Util_Eagle_Filter::get_hash_code_columns($target, $base_components);
				$component = str_replace('_id','', end($hash_code_columns));
			}

			## フィルター配列に追加
			$filter = Util_Eagle_Filter::get_filtering_params($target, $hash_code_columns, $filter);

			## 対象情報をHashをkeyとして生成しなおす
			$hash_key = Util_Eagle_Update::encode_hash_array($target, $hash_code_columns);
			$target_datas_by_hash_key[$hash_key] = $value;
		}


		foreach($filter as $component => $value){
			$value['search']['list'] = implode("\n", $value['search']['list']);
			$filter[$component] = $value;
		}

		## フィルタリング実行
		$filtering_array = Util_Eagle_Filter::get_filtering_data($eagle_id, $component, $filter);

		## マッピングをする
		$csv_content = array();
		foreach($filtering_array as $key => $filter_data){

			## ハッシュコード取得
			$hash_key = Util_Eagle_Update::encode_hash_array($filter_data, $hash_code_columns);
			if(empty($target_datas_by_hash_key[$hash_key])){
				unset($filtering_array[$key]);
				continue;
			}

			## ハッシュコードを元に更新情報を取得
			$target_data = $target_datas_by_hash_key[$hash_key];
			
			## フィルタリング情報と更新情報を元に表示するCSVコンテンツを取得する
			$content = Util_Eagle_Filter::create_result_file_content($eagle, $component, $filter_data, $target_data);
			$csv_content[] = $content;
		}

		## ヘッダー追加
		$csv_data = Util_Eagle_Common::join_csv_header($csv_content);

		$this->format = "csv";
		$DL_filename = EAGLE_UPDATE_RESULT_FILE_NAME.'_'.$eagle_id . ".csv";
		$this->response->set_header('Content-Type', 'application/csv');
		$this->response->set_header("Content-Disposition", "attachment; filename=" . $DL_filename);
		return $this->response($csv_data);
	}



	/**
	 * 詳細情報取得 
	 * 
	 * @param mixed $eagle_id 
	 * @access public
	 * @return void
	 */
	public function action_detail($eagle_id) {
		## eagle情報
		$eagle = Model_Data_Eagle::get_one($eagle_id);
		## クライアント情報(ID,名前)
		$client = Model_Mora_Client::get_client_name($eagle['client_id']);
		if(!empty($client['client_name'])){
			$eagle['client_name'] = $client['company_name'].'::'.$client['client_name'];
		}else{
			$eagle['client_name'] = $client['company_name'];
		}

		## 掲載取得アカウント一覧取得
		$eagle_accounts = Model_Data_Eagle_Update_Account::find_by_eagle_id_join_account($eagle_id);

		$response['eagle'] = $eagle;
		$response['eagle_accounts'] = $eagle_accounts;
		return $this->response($response); 
	}
}
