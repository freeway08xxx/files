<?php

class Controller_UserList_Get extends Controller_UserList_Base {

    // ログインユーザ権限チェック用URL
    public $access_url = "/sem/new/userlist/get/";

    /*========================================================================*/
    /* 前提共通処理
    /*========================================================================*/
    public function before() {

        // super
        parent::before();

        // 入力パラメータ
        $this->IN_data["client_id"]             = Input::get("client_id") ? Input::get("client_id") : Input::post("client_id");
        $this->IN_data["account_id_list"]       = Input::post("account_id_list");
    }

    // クライアント一覧を表示
    public function action_index() {

        // 担当クライアント一覧取得
        $DB_client_list = Model_Mora_Client::get_for_user();

        // 出力パラメータ
        $this->view->set("DB_client_list", $DB_client_list);

        $this->view->set_filename('userlist/get/index');
    }

    // クライアントに紐づくアカウントID一覧を表示
    public function action_account() {

        // 担当クライアント一覧取得
        $DB_client_list = \Model_Mora_Client::get_for_user();

        // アカウント選択テーブルブロック作成
        $HMVC_accounttable = Request::forge("userlist/get/accounttable", false)->execute();

        // 出力パラメータ
        $this->view->set("DB_client_list", $DB_client_list);
        $this->view->set_safe("HMVC_accounttable", $HMVC_accounttable);
        $this->view->set("OUT_data", $this->IN_data);

        // View
        $this->view->set_filename("userlist/get/index");
    }

    // アカウント選択テーブルブロック作成
    public function action_accounttable() {
        if (Request::is_hmvc()) {

            // 指定クライアントのアカウント一覧取得
            $media_list = array(MEDIA_ID_YAHOO, MEDIA_ID_GOOGLE, MEDIA_ID_IM);
            $DB_account_list = \Model_Mora_Account::get_by_client($this->IN_data["client_id"], $media_list);

            // 出力パラメータ
            $this->view->set("DB_account_list", $DB_account_list);
            $this->view->set("OUT_data", $this->IN_data);

            // View
            $this->view->set_filename("userlist/get/accounttable");
            return Response::forge($this->view);
        }
    }


    // リマーケティングリスト取得結果を表示
    public function action_result() {

        // ユーザリストデータ取得
        $HMVC_userlist = Request::forge("userlist/get/api", false)->execute();

        $this->view->set_safe("HMVC_userlist", $HMVC_userlist);

        // View
        $this->view->set_filename("userlist/get/result");
    }
    
    public function action_api() {
        if (Request::is_hmvc()) {

            // アカウントIDを媒体単位でグルーピング
            $account_id_list = Util_UserList_Get::getAccountIdListByMedia($this->IN_data["account_id_list"]);

            $targetlist_data_list = NULL;
            if(!empty($account_id_list[MEDIA_ID_IM])){
                // YDN:ターゲティングリスト取得
                $ret = Util_UserList_Get::targetListData($account_id_list[MEDIA_ID_IM]);
                if(isset($ret["RULE"])){
                    $this->view->set("userlist_ydn_rule", $ret["RULE"]);
                }
                if(isset($ret["COMBINATION"])){
                    $this->view->set("userlist_ydn_combination", $ret["COMBINATION"]);
                }
            }

            if(!empty($account_id_list[MEDIA_ID_GOOGLE])){
                // Google:ユーザリスト取得
                $ret = Util_UserList_Get::userlistData($account_id_list[MEDIA_ID_GOOGLE]);
                if(isset($ret["OTHER_COMBINATION"])){
                    $this->view->set("userlist_google_other_combination", $ret["OTHER_COMBINATION"]);
                }
                if(isset($ret["COMBINATION"])){
                    $this->view->set("userlist_google_combination", $ret["COMBINATION"]);
                }
            }

            $this->view->set("header_list_google", $GLOBALS["header_list_google"]);
            $this->view->set("header_list_ydn_rule", $GLOBALS["header_list_ydn_rule"]);
            $this->view->set("header_list_ydn_combination", $GLOBALS["header_list_ydn_combination"]);
            $this->view->set("header_list_google_combination", $GLOBALS["header_list_ydn_combination"]);
            $this->view->set_filename("userlist/get/userlist");
            return Response::forge($this->view);
        }
    }
}
