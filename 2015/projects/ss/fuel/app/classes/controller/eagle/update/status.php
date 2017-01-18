<?php
require_once APPPATH . "/const/eagle.php";
require_once APPPATH . "/const/eagle/message.php";


/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Eagle_Update_Status extends Controller_Eagle_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/**
	 * 絞り込み検索結果ファイルをダウンロードする(ステータス)
	 * 
	 * @access public
	 * @return csv 
	 */
	public function action_download_filter_file() {
		
		$eagle_id = json_decode(Input::post("eagleId"),true);	
		$component = json_decode(Input::post("component"),true);
		$filter = json_decode(Input::post("filter"),true);

		if(empty($eagle_id) || empty($component) || empty($filter)){
			throw new HttpServerErrorException();
		}

		## 対象掲載情報から絞り込み検索を行う 
		$filtering_data = Util_Eagle_Filter::get_filtering_data($eagle_id, $component, $filter);
		
		## csn出力用のカラム取得
		$column_key = Util_Eagle_Update::get_status_filtering_column($component);

		## csv出力用フォーマット情報の取得を行う
		$csv_data = Util_Eagle_Common::get_format_csv($filtering_data,$column_key);

		$this->format = "csv";
		$DL_filename = EAGLE_FILTERING_FILE_NAME . '【ステータス変更】_'.date("YmdHis") . ".csv";
		$this->response->set_header('Content-Type', 'application/csv');
		$this->response->set_header("Content-Disposition", "attachment; filename=" . $DL_filename);
		return $this->response($csv_data);
	}


	/**
	 * 変更確認を行う 
	 * 
	 * @access public
	 * @return csv 
	 */
	public function action_download_confirm_save_file() {

		$is_dry_run = true;
		$request['eagleId'] = json_decode(Input::post("eagleId"),true);
		$request['component'] = json_decode(Input::post("component"),true);
		$request['update_data'] = json_decode(Input::post("update_data"),true);
		$request['activeFlg'] = json_decode(Input::post("activeFlg"),true);

		## 擬似登録を実行する
		$eagle_save = Request::forge("eagle/update/status/update",false)->execute(array($request, $is_dry_run))->response()->body();
		if(empty($eagle_save)){
			$this->response->set_header('Content-Type', 'application/txt');
			$this->response->set_header("Content-Disposition", "attachment; filename=status_confirm_error.txt");
			return $this->response('エラーが発生しました。詳細：'.ERROR_MESSAGE_APPLICATION);
			
		}elseif(!empty($eagle_save['message'])){
			$this->response->set_header('Content-Type', 'application/txt');
			$this->response->set_header("Content-Disposition", "attachment; filename=status_confirm_error.txt");
			return $this->response('エラーが発生しました。詳細：'.$eagle_save['message']);

		}

		## 結果情報の取得を行う
		$result_file = Request::forge("eagle/history/download_result_file",false)->execute(array($request['eagleId'], $eagle_save))->response();
		return $result_file;
	}

	/**
	 * ステータスの更新を行う
	 * 
	 * @access public
	 * @return array 
	 */
	public function action_update($request = null, $is_dry_run = false) {
		if (!Request::is_hmvc()) {
			$eagle_id = Input::json("eagleId");
			$component = Input::json("component");
			$update_data = Input::json("update_data");
			$active_flg = Input::json("activeFlg");
		} else {
			$eagle_id = $request['eagleId'];
			$component = $request['component'];
			$update_data = $request['update_data'];
			$active_flg = $request['activeFlg'];
		}

		if(empty($eagle_id) || empty($component) || !isset($active_flg) || empty($update_data)){
			return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
		}

		## 更新情報を配列に変換
		$columns = array("eagle_id","data");
		$eagle_target_values  = array();
		$update_array = explode("\n", $update_data);
		foreach($update_array as $key => $update){
			$update_data = Util_Eagle_Update::get_data_by_text($update, 'status', $component);
			if(empty($update_data)){
				return $this->response(array('message' => ERROR_MESSAGE_UPDATE_INPUT_TEXT),500);
			}
			if(Util_Eagle_Update::valid_formate_update_data_status($update_data)){
				$value[$columns[0]] = $eagle_id;
				$value[$columns[1]] = json_encode($update_data);
				$eagle_target_values[] = $value;
			}else{
				return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
			}
		}

		$eagle_value = array(
			'exec_code' => EXEC_CODE_STATUS,
			'status' => EAGLE_UPDATE_STATUS_RESERVE_UPDATE,
			'options' => json_encode(array('isActive' => $active_flg, 'component' => $component)),
		);

		## 登録処理
		if(!$is_dry_run){
			Model_Data_Eagle_Update_Target::insert($columns,$eagle_target_values);


			## Eagle更新処理
			Model_Data_Eagle::update_by_id($eagle_id, $eagle_value);

			## ジェンキンスでバッチ起動
			$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_UPDATE_STATUS) . "/buildWithParameters?token=eagle&id=" . $eagle_id . "&component=".$component."&user_id_sem=" . \Session::get("user_id_sem"), "curl");
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();
		}

		$response['eagle'] = array_merge($eagle_value,array('id' => $eagle_id ));
		$response['eagle_target'] = $eagle_target_values;
		return $this->response($response);
	}
}
