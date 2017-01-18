<?php
/**
 * falcon媒体費個別設定系コントローラ
 **/
require_once APPPATH."/const/falcon.php";

class Controller_Client_MediaCost extends Controller_Client_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	public function before() {

		parent::before();

		$this->data["target_type"]        = Input::json('target_type');
		$this->data["media_id"]           = Input::json('media_id');
		$this->data["media_cost"]         = Input::json('media_cost');
		$this->data["input_account_list"] = Input::json('account_list');
	}

	/**
	 * Top画面表示
	 *
	 * @return view html
	 */
	public function action_index() {
		$this->view->set_filename('client/mediacost/index');
		$this->response($this->view);
	}

	/**
	 * 設定テーブル＆登録フォーム表示
	 *
	 * @param string client_id
	 * @return view html
	 */
	public function action_detail() {
		//クライアントのアカウント一覧を取得する。
		$falcon_media_id_list = array_keys(FalconConst::$falcon_media_list);
		$account_info_list    = \Model_Mora_Account::get_by_client(Input::param('client_id'), $falcon_media_id_list);

		//媒体費個別設定情報
		$targetmediacost_list = Model_Data_Falcon_TargetMediaCost::get_list(Input::param('client_id'));
		$target_media_list    = array();
		$target_account_list  = array();
		foreach ($targetmediacost_list as $key => $value){
			$value['datetime'] = str_replace('-', '.', $value['datetime']);

			if($value['target_type'] == 1){
				$target_media_list[$value['media_id']] = $value;
			}elseif($value['target_type'] == 2){
				$target_account_list[$value['account_id']] = $value;
			}
		}

		$data['target_media_list']   = $target_media_list;
		$data['target_account_list'] = $target_account_list;
		$data['account_info_list']   = $account_info_list;

		$view['view_form'] = View::forge('client/mediacost/form');
		$view['view_table'] = View::forge('client/mediacost/table', $data);

		$this->view->set($view);
		$this->view->set_filename('client/mediacost/detail');

		return Response::forge($this->view);
	}

	//重複チェック
	public function post_check($client_id) {

		//クライアントのアカウント一覧を取得する。
		$falcon_media_id_list = array_keys(FalconConst::$falcon_media_list);
		$account_list = \Model_Mora_Account::get_by_client($client_id, $falcon_media_id_list);

		$res_text = null;
		if($this->data["target_type"] == 1){
			$res =\Model_Data_Falcon_TargetMediaCost::check($this->data["target_type"], $client_id, $this->data["media_id"]);
			if($res){
				$res_text .= "媒体名：".FalconConst::$falcon_target_media_list[$this->data["media_id"]]."は既に設定されています。";
			}

		}elseif($this->data["target_type"] == 2){
			if ($this->data["input_account_list"]) {
				foreach($this->data["input_account_list"] as $input_account) {
					list($tmp_media_id, $tmp_account_id) = explode("//",  $input_account);

					foreach ($account_list as $key => $value){
						if ($tmp_account_id == $value["account_id"]) {
							$res = \Model_Data_Falcon_TargetMediaCost::check($this->data["target_type"], $client_id, $value["media_id"], $value["account_id"]);
							if($res){
								$res_text .= "[".FalconConst::$falcon_target_media_list[$value["media_id"]].":".$value["account_id"]."]".$value["account_name"]."は既に設定されています。";
							}
						}
					}
				}
			}
		}
		return $this->response(array($res_text), 200);
	}

	//新規登録
	public function post_save($client_id) {
		$values = array();
		//媒体別
		if($this->data["target_type"] == 1){
			$values[] = array("target_type" => $this->data["target_type"],
							  "client_id" => $client_id,
							  "media_id" => $this->data["media_id"],
							  "media_cost" => $this->data["media_cost"]
					);
		//アカウント別
		}elseif($this->data["target_type"] == 2){
			if ($this->data["input_account_list"]) {
				foreach($this->data["input_account_list"] as $input_account) {
					list($tmp_media_id, $tmp_account_id) = explode("//",  $input_account);

					//コンサル案件は媒体IDを統一
					if($tmp_media_id == MEDIA_ID_YAHOO_CONSULTING){
						$tmp_media_id = MEDIA_ID_YAHOO;
					}elseif($tmp_media_id == MEDIA_ID_GOOGLE_CONSULTING){
						$tmp_media_id = MEDIA_ID_GOOGLE;
					}

					$values[] = array("target_type" => $this->data["target_type"],
									  "client_id" => $client_id,
									  "media_id" => $tmp_media_id,
									  "account_id" => $tmp_account_id,
									  "media_cost" => $this->data["media_cost"]
					);
				}
			}
		}

		$res = \Model_Data_Falcon_TargetMediaCost::ins($values);

		return $this->response(array($res), 200);
	}

	//編集
	public function post_update($client_id) {
		$add_mediacost_list = Input::json('add_mediacost_list');

		foreach ($add_mediacost_list as $key => $value){
			\Model_Data_Falcon_TargetMediaCost::upd($value["id"], $value["media_cost"]);
		}

		$res = true;
		return $this->response(array($res), 200);
	}

	//編集
	public function post_delete($client_id) {
		$del_mediacost_list = Input::json('del_mediacost_list');

		$res_arr = array();
		foreach ($del_mediacost_list as $id){
			$res = \Model_Data_Falcon_TargetMediaCost::del($id);
			$res_arr[$res] = $res;
		}

		$res = true;
		return $this->response(array($res), 200);
	}

}
