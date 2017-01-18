<?php
require_once APPPATH . "/const/eagle.php";
require_once APPPATH . "/const/eagle/message.php";


/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Eagle_Update_Cpc extends Controller_Eagle_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/**
	 * 絞り込み検索結果ファイルをダウンロードする(CPC)
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
		
		## csv出力用のカラム取得
		$column_key = Util_Eagle_Update::get_cpc_filtering_column($component);

		## csv出力用フォーマット情報の取得を行う
		$csv_data = Util_Eagle_Common::get_format_csv($filtering_data, $column_key);

		$this->format = "csv";
		$DL_filename = EAGLE_FILTERING_FILE_NAME . '【CPC変更】_'.date("YmdHis") . ".csv";
		$this->response->set_header('Content-Type', 'application/csv');
		$this->response->set_header("Content-Disposition", "attachment; filename=" . $DL_filename);
		return $this->response($csv_data);
		
	}


	/**
	 * 登録確認を行う 
	 * 
	 * @access public
	 * @return csv 
	 */
	public function action_download_confirm_save_file() {

		$is_dry_run = true;
		$request['eagleId'] = json_decode(Input::post('eagleId'),true);
		$request['component'] = json_decode(Input::post("component"),true);
		$request['update_data'] = json_decode(Input::post("update_data"),true);
		$request['bulk_value'] = json_decode(Input::post("bulk_value"),true);
		$request['settings'] = json_decode(Input::post("settings"),true);

		## 擬似登録を実行する
		$eagle_save = Request::forge("eagle/update/cpc/save",false)->execute(array($request, $is_dry_run))->response()->body();
		if(empty($eagle_save)){
			//$this->view->set("message", ERROR_MESSAGE_APPLICATION);
			//$this->view->set_filename('common/error');
			//Response::redirect("eagle/#/update?eagle_id=".$request['eagleId']);

			$this->response->set_header('Content-Type', 'application/txt');
			$this->response->set_header("Content-Disposition", "attachment; filename=cpc_confirm_error.txt");
			return $this->response('エラーが発生しました。詳細：'.ERROR_MESSAGE_APPLICATION);
		}elseif(!empty($eagle_save['message'])){
			//$this->view->set("message", $eagle_save['message']);
			//$this->view->set_filename('common/error');
			//Response::redirect("eagle/#/update?eagle_id=".$request['eagleId']);

			$this->response->set_header('Content-Type', 'application/txt');
			$this->response->set_header("Content-Disposition", "attachment; filename=cpc_confirm_error.txt");
			return $this->response('エラーが発生しました。詳細：'.$eagle_save['message']);
		}else{
			## 結果情報の取得を行う
			$result_file = Request::forge("eagle/history/download_result_file",false)->execute(array($request['eagleId'], $eagle_save))->response();
			return $result_file;

		}
	}


	/**
	 * 更新登録を行う 
	 * 
	 * @access public
	 * @return array 
	 */
	public function action_save($request = null, $is_dry_run = false){

		if (!Request::is_hmvc()) {
			$eagle_id = Input::json("eagleId");
			$component = Input::json("component");
			$update_data = Input::json("update_data");
			$bulk_value = Input::json("bulk_value");
			$settings = Input::json("settings");
		} else {
			$eagle_id = $request[ "eagleId" ];
			$component = $request[ "component" ];
			$update_data = $request["update_data"];
			$bulk_value = $request["bulk_value"];
			$settings = $request["settings"];
		}

		if(empty($eagle_id) || empty($component) || empty($settings) ){
			return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
		}

		## 更新情報を配列に変換
		$columns = array("eagle_id","data");
		$eagle_target_values  = array();
		if($settings['saveType'] == 1){
			if(empty($update_data)){
				return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
			}

			## 通常指定
			$update_array = explode("\n", $update_data);
			foreach($update_array as $key => $update){
				
				## 入力されたテキストを元に配列情報作成
				$update_data = Util_Eagle_Update::get_data_by_text($update , 'cpc', $component, $settings);
				if(empty($update_data)){
					return $this->response(array('message' => ERROR_MESSAGE_UPDATE_INPUT_TEXT),500);
				}


				## 登録用の情報ににフォーマット
				$save_data = Util_Eagle_Update::get_save_format_data( $update_data );
				if(!$save_data){
					return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
				}

				## バリデーション
				if(Util_Eagle_Update::valid_formate_update_data_cpc($save_data)){
					$value[$columns[0]] = $eagle_id;
					$value[$columns[1]] = json_encode($save_data);
					$eagle_target_values[] = $value;
				}else{
					return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
				}
			}
		}else{
			if(empty($bulk_value)){
				return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
			}

			## 一括指定
			$update_array = Util_Eagle_Filter::get_filtering_data($eagle_id, $component, null);
			foreach($update_array as $key => $update){
				## 対象情報と計算された情報を取得 
				$update_data = Util_Eagle_Update::get_data_by_bulk($update, $bulk_value, $settings);
				if(empty($update_data)){
					return $this->response(array('message' => ERROR_MESSAGE_REQUEST_PARAMETER),500);
				}

				## 登録用の情報ににフォーマット
				$save_data = Util_Eagle_Update::get_save_format_data( $update_data );
				if(!$save_data){
					return $this->response(array('message' => ERROR_MESSAGE_UPDATE_INPUT_TEXT_RANGE . print_r($update,true)),500);
				}

				## バリデーション
				if(Util_Eagle_Update::valid_formate_update_data_cpc($save_data)){
					$value[$columns[0]] = $eagle_id;
					$value[$columns[1]] = json_encode($save_data);
					$eagle_target_values[] = $value;
				}else{
					return $this->response(array('message' => ERROR_MESSAGE_UPDATE_INPUT_TEXT),500);
				}
			}
		}

		## e_eagle情報
		$saveOption = array(
			'component' => $component,
			'saveType' => $settings['saveType'],
			'cpcMba' => $settings['cpcMba'],
		);
		if(	$settings['cpcMba'] == 'CPC' ){
			$saveOption['device'] = $settings['device'];
		}
		if( $settings['saveType'] == 2 ){
			$saveOption['unit'] = $settings['unit'];
			$saveOption['ud'] = $settings['ud'];
			$saveOption['bulk_value'] = $bulk_value;
		}
		$eagle_value = array(
			'exec_code' => EXEC_CODE_CPC,
			'status' => EAGLE_UPDATE_STATUS_RESERVE_UPDATE,
			'options' => json_encode($saveOption),
		);

		## 登録
		if(!$is_dry_run){
			Model_Data_Eagle_Update_Target::insert($columns,$eagle_target_values);
			Model_Data_Eagle::update_by_id($eagle_id, $eagle_value);

			// CPC変更バッチを実行
			$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_UPDATE_CPC) . "/buildWithParameters?token=eagle&id=" . $eagle_id . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();
		}

		$response['eagle'] = array_merge($eagle_value,array('id' => $eagle_id ));
		$response['eagle_target'] = $eagle_target_values;
		return $this->response($response);
	}
}
