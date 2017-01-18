<?php

require_once(APPPATH. "vendor/PHPExcel/PHPExcel.php");
require_once(APPPATH. "vendor/PHPExcel/PHPExcel/IOFactory.php");
require_once APPPATH . "/const/userlist.php";

class Controller_UserList_Edit extends Controller_UserList_Base {

    // ログインユーザ権限チェック用URL
    public $access_url = "/sem/new/userlist/edit/";

    /*========================================================================*/
    /* 前提共通処理
    /*========================================================================*/
    public function before() {

        // super
        parent::before();

        $this->uniqid                   = Input::post("uniqid");                // シリアライズデータファイル名
        $this->upload_filename          = Input::post("upload_filename");       // アップロードされたファイル名
        $this->rule_data_cnt            = Input::post("rule_data_cnt");         // ユーザリスト(ルール)件数
        $this->combination_data_cnt     = Input::post("combination_data_cnt");  // ユーザリスト(組み合わせ)件数

        // 登録・更新内容確認画面におけるヘッダー項目(ルールと組み合わせ)
        $this->view->set("header_list_regist_rule",         $GLOBALS["header_list_regist_rule"]);
        $this->view->set("header_list_regist_gdn_rule",     $GLOBALS["header_list_regist_gdn_rule"]);
        $this->view->set("header_list_regist_combination",  $GLOBALS["header_list_regist_combination"]);

        // 登録・更新内容結果画面におけるヘッダー項目(ルールと組み合わせ)
        $this->view->set("header_list_complete_rule",       $GLOBALS["header_list_complete_rule"]);         // YDNルール
        $this->view->set("header_list_gdn_complete_rule",   $GLOBALS["header_list_gdn_complete_rule"]);     // GDNルール
        $this->view->set("header_list_complete_combination",$GLOBALS["header_list_complete_combination"]);  // 組み合わせ

        // システム表記を表示用に変換する文字列群
        $this->view->set("ydn_rule_type",                   $GLOBALS["ydn_rule_type"]);
        $this->view->set("ydn_rule_condition",              $GLOBALS["ydn_rule_condition"]);
        $this->view->set("ydn_open_flg",                    $GLOBALS["ydn_open_flg"]);
        $this->view->set("ydn_preset_flg",                  $GLOBALS["ydn_preset_flg"]);
        $this->view->set("gdn_rule_type",                   $GLOBALS["gdn_rule_type"]);
        $this->view->set("gdn_rule_condition",              $GLOBALS["gdn_rule_condition"]);

        // フォーマットファイル
        $this->view->set("ydn_rule_format_file",            DL_FORMAT_FILE_PATH.YDN_RULE_FORMAT_FILE);
        $this->view->set("ydn_combi_format_file",           DL_FORMAT_FILE_PATH.YDN_COMBI_FORMAT_FILE);
        $this->view->set("google_rule_format_file",         DL_FORMAT_FILE_PATH.GOOGLE_RULE_FORMAT_FILE);
        $this->view->set("google_combi_format_file",        DL_FORMAT_FILE_PATH.GOOGLE_COMBI_FORMAT_FILE);

    }

    // 登録画面の表示
    public function action_index() {
        $this->view->set_filename("userlist/edit/index");
    }

    // 登録データのファイルをサーバに保存し確認画面を表示
    public function action_confirm() {

        // ファイルアップロードの初期設定
        $config = array(
            "ext_whitelist" => $GLOBALS["ext_whitelist"],
            "path"          => UPLOAD_FILE_PATH,
            "suffix"        => "_" .Session::get("user_id_sem")."_".date("YmdHis"),
        );

        Upload::process($config);

        if(Upload::is_valid()){

            Upload::save();

            $fileinfo = Upload::get_files();
            $upload_file_name = $fileinfo[0]["saved_as"]; // アップロードされたファイル名

            // アップロードされたファイルからユーザリストの処理種別を求める(YDNルール or YDN組み合わせ or Google組み合わせ or Googleルール)
            $kind = Util_UserList_Edit::getMediaProcessKind($upload_file_name);
            if(strpos($kind, "YDN") !== FALSE && strpos($kind, "ルール") !== FALSE){
                $userlist_date = Util_UserList_Edit::getYdnContentsRule($upload_file_name);
                $save_data["rule"] = $userlist_date;
            }else if((strpos($kind, "YDN") !== FALSE && strpos($kind, "組み合わせ") !== FALSE) ){
                $userlist_date = Util_UserList_Edit::getYdnContentsCombination($upload_file_name);
                $save_data["combination"] = $userlist_date;
            }else if((strpos($kind, "GDN") !== FALSE && strpos($kind, "組み合わせ") !== FALSE)){
                $userlist_date = Util_UserList_Edit::getGdnContentsCombination($upload_file_name);
                $save_data["combination"] = $userlist_date;
            }else if((strpos($kind, "GDN") !== FALSE && strpos($kind, "ルール") !== FALSE)){
                $userlist_date = Util_UserList_Edit::getGdnContentsRule($upload_file_name);
                $save_data["rule"] = $userlist_date;
            }else{
                $this->alert_message .= ALERT_MSG_003;
            }
        }else{
            foreach (Upload::get_errors() as $error_info) {
                foreach ($error_info["errors"] as $error) {
                    $this->alert_message .= $error["message"] . "</br>";
                }
            }
        }

        if(isset($userlist_date)){
            if($userlist_date["status"] === false){
                $this->alert_message .= $userlist_date["contents"] . "</br>";
            }
            if(count($userlist_date["contents"]) === 0){
                $this->alert_message .= ALERT_MSG_005;
            }
        }
        if($this->alert_message){
            $this->view->set_filename("userlist/edit/index");
            return;
        }

        // 配列内容をファイルに出力する際のファイル名に使用するユニークな文字列
        $uniqid = uniqid(rand());

        // 取得したユーザリストデータをファイルへ一時的に保存
        $file_name = $uniqid.".".SERIALIZE_FILE_EXT;
        Util_UserList_Edit::setSerializeData($save_data,SERIALIZE_FILE_PATH,$file_name);

        if(isset($save_data["rule"]["contents"]["YDN"])){
            $this->view->set("ret_ydn_rule_data_list",  $save_data["rule"]["contents"]["YDN"]);             // YDNユーザリスト(ルール)データ
            $this->view->set("process_operation",       $save_data["rule"]["process_operation"]);           // ユーザリスト(ルール)処理操作 登録or更新
            $this->view->set("rule_data_cnt",           $save_data["rule"]["data_cnt"]);                    // 登録・更新対象データ件数
        }
        if(isset($save_data["rule"]["contents"]["Google"])){
            $this->view->set("ret_gdn_rule_data_list",  $save_data["rule"]["contents"]["Google"]);          // Googleユーザリスト(ルール)データ
            $this->view->set("process_operation",       $save_data["rule"]["process_operation"]);           // ユーザリスト(ルール)処理操作 登録or更新
            $this->view->set("rule_data_cnt",           $save_data["rule"]["data_cnt"]);                    // 登録・更新対象データ件数
        }
        if(isset($save_data["combination"]["contents"]["YDN"])){
            $this->view->set("ret_combi_data_list",     $save_data["combination"]["contents"]["YDN"]);     // YDNユーザリスト(組み合わせ)データ
            $this->view->set("process_operation",       $save_data["combination"]["process_operation"]);   // ユーザリスト(組み合わせ)処理操作 登録or更新
            $this->view->set("combination_data_cnt",    $save_data["combination"]["data_cnt"]);            // ユーザリスト(組み合わせ)
        }
        if(isset($save_data["combination"]["contents"]["Google"])){
            $this->view->set("ret_combi_data_list",     $save_data["combination"]["contents"]["Google"]);  // Googleユーザリスト(組み合わせ)データ
            $this->view->set("process_operation",       $save_data["combination"]["process_operation"]);   // ユーザリスト(組み合わせ)処理操作 登録or更新
            $this->view->set("combination_data_cnt",    $save_data["combination"]["data_cnt"]);            // ユーザリスト(組み合わせ)
        }
        // アップロードされたファイル名
        $this->view->set("upload_filename", $upload_file_name);
        $this->view->set("uniqid", $uniqid);

        $this->view->set_filename("userlist/edit/index");
    }

    // APIにて登録
    public function action_api() {

        // シリアライズしたファイル内容の取得
        $file_path = SERIALIZE_FILE_PATH.$this->uniqid.".".SERIALIZE_FILE_EXT;
        $unserialize_data = Util_UserList_Edit::getSerializeData($file_path);
        if(!isset($unserialize_data) || $unserialize_data === false){
            $this->alert_message = ALERT_MSG_001;
            $this->view->set_filename("userlist/edit/index");
            return;
        }

        // データ量からバックグラウンド処理を行うべきかを判断する
        if($this->rule_data_cnt > BG_PRCESS_DATA_CNT_THRESHOLD || $this->combination_data_cnt > BG_PRCESS_DATA_CNT_THRESHOLD){

            // ユーザリスト登録・更新バッチ実行
            $user_id = \Session::get("user_id_sem");

            if(isset($unserialize_data["rule"]["contents"]["YDN"]) || isset($unserialize_data["combination"]["contents"]["YDN"])){
                $curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_USERLIST_YDN_CREATE_JOB) . "/buildWithParameters?token=userlist&uniqid=" . $this->uniqid . "&user_id_sem=" . $user_id, "curl");
                $curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
                $curl->execute();
            }
            if(isset($unserialize_data["rule"]["contents"]["Google"]) || isset($unserialize_data["combination"]["contents"]["Google"])){
                $curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_USERLIST_GOOGLE_CREATE_JOB) . "/buildWithParameters?token=userlist&uniqid=" . $this->uniqid . "&user_id_sem=" . $user_id, "curl");
                $curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
                $curl->execute();
            }

            $this->view->set_filename("userlist/edit/batch");
            return;
        }

        $ok_cnt = $ng_cnt = 0;

        // YDNユーザリスト(ルールベース)API入稿
        if(isset($unserialize_data["rule"]["contents"]["YDN"])){
            $process_operation = $unserialize_data["rule"]["process_operation"];
            foreach ($unserialize_data["rule"]["contents"]["YDN"] as $account_id => $rule_data_list) {
                $ret_api = Util_UserList_Edit::api_ydn($account_id,$rule_data_list,"RULE",$process_operation);
                // 登録OK件数および登録NG件数の取得
                $ret_cnt = Util_UserList_Edit::getProcessResultCount($ret_api);
                $ok_cnt += $ret_cnt["OK"];
                $ng_cnt += $ret_cnt["NG"];
                $ret_rule_data_list[$account_id][] = $ret_api;
            }
            // YDNユーザリスト(ルール)データ
            $this->view->set("ret_rule_data_list",$ret_rule_data_list);
        }

        // YDNユーザリスト(組み合わせ)API入稿
        if(isset($unserialize_data["combination"]["contents"]["YDN"])){
            $process_operation = $unserialize_data["combination"]["process_operation"];
            foreach ($unserialize_data["combination"]["contents"]["YDN"] as $account_id => $combination_data_list) {
                $ret_api = Util_UserList_Edit::api_ydn($account_id,$combination_data_list,"COMBINATION",$process_operation);
                // 登録OK件数および登録NG件数の取得
                $ret_cnt = Util_UserList_Edit::getProcessResultCount($ret_api);
                $ok_cnt += $ret_cnt["OK"];
                $ng_cnt += $ret_cnt["NG"];
                $ret_combi_data_list[$account_id][] = $ret_api;
            }
            // YDNユーザリスト(組み合わせ)データ
            $this->view->set("ret_combi_data_list",$ret_combi_data_list);
        }

        // Googleユーザリスト(組み合わせ)API入稿
        if(isset($unserialize_data["combination"]["contents"]["Google"])){
            $process_operation = $unserialize_data["combination"]["process_operation"];
            foreach ($unserialize_data["combination"]["contents"]["Google"] as $account_id => $combination_data_list) {
                $ret_api = Util_UserList_Edit::api_google($account_id,$combination_data_list,"COMBINATION",$process_operation);
                // 登録OK件数および登録NG件数の取得
                $ret_cnt = Util_UserList_Edit::getProcessResultCount($ret_api);
                $ok_cnt += $ret_cnt["OK"];
                $ng_cnt += $ret_cnt["NG"];
                $ret_combi_data_list[$account_id][] = $ret_api;
            }
            // Googleユーザリスト(組み合わせ)データ
            $this->view->set("ret_combi_data_list",$ret_combi_data_list);
        }

        // Googleユーザリスト(ルールベース)API入稿
        if(isset($unserialize_data["rule"]["contents"]["Google"])){
            $process_operation = $unserialize_data["rule"]["process_operation"];
            foreach ($unserialize_data["rule"]["contents"]["Google"] as $account_id => $rule_data_list) {
                $ret_api = Util_UserList_Edit::api_google($account_id,$rule_data_list,"RULE",$process_operation);
                // 登録OK件数および登録NG件数の取得
                $ret_cnt = Util_UserList_Edit::getProcessResultCount($ret_api);
                $ok_cnt += $ret_cnt["OK"];
                $ng_cnt += $ret_cnt["NG"];
                $ret_rule_data_list[$account_id][] = $ret_api;
            }
            // Googleユーザリスト(組み合わせ)データ
            $this->view->set("ret_gdn_rule_data_list",$ret_rule_data_list);
        }

        $this->view->set("process_result_cnt",array("OK" => $ok_cnt, "NG" => $ng_cnt));
        $this->view->set("process_operation",$process_operation);
        $this->view->set_filename("userlist/edit/complete");
        return;
    }

    // バッチの処理結果をダウンロードする
    public function action_download() {

        $file_name = \Crypt::decode(Input::get("f"));
        $file_path = BATCH_RESULT_PATH.$file_name;
        
        File::download($file_path, $file_name);
    }

}
