<?php
require_once APPPATH . "/const/yahoo.php";
require_once APPPATH . "/const/adwords.php";
/**
 * マイページ レポート画面 コントローラ
 *
 * @return HTML_View
 */
class Controller_Mypage_Report extends Controller_Mypage_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index() {
		$this->view->set_filename('mypage/report/index');
		$this->view->set_safe("information",View::forge('mypage/information'))
				   ->set_safe("mypage_nav", View::forge('mypage/report/mypage_nav'))
				   ->set_safe("keyword",    View::forge('mypage/report/keyword'));
		$this->response($this->view);
	}

	/**
	 * クライアント進捗データ取得
	 *
	 * @access public
	 * @return json
	 */
	public function get_summarydata() {
		$data = array();

        ##redisに当日データがあれば使用
        $redis              = new Util_Common_Redis();
        $data["term"]       = Input::get('term'); 
        $is_skip_redis      = Input::get('isSkipRedis');
        $data["user_name"]  = Input::get('search_user_name');
        $user_id            = Session::get('user_id_sem');
        $update_time        = 1000;

        //user_id,日付,該当月をredisのkeyに設定 ただし10:00am前のアクセスは前日扱い
        $redis_key = (date("Hi") <= $update_time) ? date("m.d-",strtotime("-1 day")).$user_id."-".$data["term"]:
                                                    date("m.d-").$user_id."-".$data["term"];

        if($is_skip_redis=='false'){
            $res = $redis->get_redis_hash($redis_key,'myreport');
            if (!$res[0]==null) return $res[0];
        }

        ## レポート結果テーブル
        $report_form  = Request::forge("quickmanage/export/display", false)->execute(array('row'))->response();


        if(!empty($report_form->body["report"][1]) && !empty($report_form->body["forecast"])){
            $data["report"]   = $report_form->body["report"][1];
            $data["forecast"] = $report_form->body["forecast"];
        }
        unset($report_form);
        $clients      = array();
        $result       = array();
        $stop_cliants = self::_get_stop_clients();

        if(!empty($data["report"]["summary"])){
            foreach( $data["report"]["summary"] AS $key => $value){
                $result["client_name"]   = $value["client_name"];
                $result["cost"]          = $value["cost"];
                $result["conv"]          = $value["conv"]; 
                $result["cpa"]           = $value["cpa"];
                $result["gross_margin"]  = $value["gross_margin"];
                $forecast                = $data["forecast"][$key];
                $result["forecast_cost"] = $forecast["cost"];
                $result["forecast_conv"] = $forecast["conv"]; 
                $result["forecast_cpa"]  = $forecast["cpa"];

                ##前日比の計算
                if(!empty($data["report"]["daily"])){
                    $diffData  = array_values(array_slice($data["report"]["daily"], -2, 2));
                    $keys      = array_keys($data["report"]["daily"]);
                    $data["period"]["first"] = min($keys);
                    $data["period"]["last"]  = max($keys);
                }
                if(!empty($diffData[1][$key]) && !empty($diffData[0][$key]) ){
                    $result["dailyDiff_cost"] = $diffData[1][$key]["cost"] - $diffData[0][$key]["cost"];
                    $result["dailyDiff_conv"] = $diffData[1][$key]["conv"] - $diffData[0][$key]["conv"];
                    $result["dailyDiff_cpa"]  = $diffData[1][$key]["cpa"]  - $diffData[0][$key]["cpa"];
                }

                $client_id    = $value["client_id"];
                $type         = self::_get_category_type($key);
                if (empty($type)) continue;

                $reverse_type = ($type == 'search') ? 'display' : 'search';
                $clients[$client_id][$type]              = $result;
                $clients[$client_id][$type]["client_id"] = $client_id;

                if(!empty($clients[$client_id]) && empty($clients[$client_id][$reverse_type])){
                    $clients[$client_id][$reverse_type]["client_name"] = $value["client_name"];
                    $clients[$client_id][$reverse_type]["cost"]          = -1;
                    $clients[$client_id][$reverse_type]["conv"]          = -1;
                    $clients[$client_id][$reverse_type]["cpa"]           = -1;
                    $clients[$client_id][$reverse_type]["gross_margin"]  = -1;
                    $clients[$client_id][$reverse_type]["forecast_cost"] = -1;
                    $clients[$client_id][$reverse_type]["forecast_conv"] = -1; 
                    $clients[$client_id][$reverse_type]["forecast_cpa"]  = -1;
                }
                ##ストップアカウントがあるクライアントとその数を格納
                if (!empty($stop_cliants)){
                    $tmpArray = array_count_values($stop_cliants);
                    $clients[$client_id][$type]["stop_account_cnt"] =((isset($tmpArray[$client_id])))? $tmpArray[$client_id]:0;
                }
                //$client_names[$client_id] = $clients[$client_id]["search"]["client_name"];
            }

            $data['graph']           = self::_get_graphdata($data,$clients);
            $data["results"]         = array_values($clients);

            unset($clients);
        }

        ## redis登録処理
        unset($data['report']);
        $expire               = 86400; //24時間
        $register             = array();
        $register["myreport"] = $data;
        $res=$redis->set_redis_hash($redis_key, $register, true, $expire);
        return $this->response($data);
    }

    /**
     * グラフ表示用データ取得
     *
     * @access private
     * @return object
     */
    private static function _get_graphdata($data,$clients) {
        ksort($data["report"]["daily"]);
        if(!empty($data["report"]["daily"])){
            $key      = array_keys($data["report"]["daily"]);
            $data["period"]["first"] = min($key);
            $data["period"]["last"]  = max($key);
        };


        ##グラフ必要データを配列$graphに格納
        $i                    =  0;
        $graph                = array();
        $totals               = array();
        $daily                = array();   
        $pre                  = array();

        if(!empty($data["report"]["daily"])){
            //dailyデータ処理
            foreach( $data["report"]["daily"] AS $key => $value){

                 //costを格納
                foreach( $value AS $category  => $cate_val){
                    //日付を格納
                    $type = self::_get_category_type($category);
                    if (empty($type)) continue;

                    $daily[$type][$i]["x"] = $key;

                    $target = $cate_val['client_name'];
                    $daily[$type][$i][$target] = $cate_val['cost'];

                    //total計算
                    if(!empty($pre[$type][$target])){
                        $totals[$type][$i][$target]  = $pre[$type][$target] + $cate_val['cost'];
                        $pre[$type][$target]        += $cate_val['cost'];
                    }else{
                        $totals[$type][$i][$target]  = $cate_val['cost'];
                        $pre[$type][$target]         = $totals[$type][$i][$target];
                    }
                    $daily[$type][$i]['totals']  = $totals[$type][$i];
                }
                $i++;
            }


            //本日以降 本日〜月最終日の日付を算出
            if($data["term"] == 'thismonth'){

                $now_YYYY_mm  = date('Y-m-');
                //残り日数 = 今月の全日数 - dailyデータの最終日
                $less = date('t') - substr($data["period"]["last"], -2);
                //当日以降表示 
                foreach( $clients AS $key => $value){
                    foreach( $value AS $category => $summary_val){
                        //search,displayをそれぞれ格納
                        if(!empty($summary_val['forecast_cost'])){

                            $target   = $summary_val['client_name'];
                            $type = self::_get_category_type($category);
                            if (empty($type)) continue;

                            //表示値 dailyの着地予想 (forecast - summary) / 残り日数 
                            $forecast = ($summary_val['forecast_cost'] - $summary_val['cost']) / $less;
                            if($forecast > 0) $daily[$type][$i][$target] = $forecast;

                            //total計算
                            if(!empty($pre[$type][$target])){
                                $totals[$type][$i][$target]     = $pre[$type][$target] + $forecast;
                                $pre[$type][$target]           += $forecast;
                                $pre[$type.'_forecast'][$target]   = $forecast;
                            }
                        }
                    }
                }
                //残り日数分の日付、数値を格納 $graphデータ$i番目に格納
                for ($j=1; $j <= 2  ; $j++) { 
                    $feature_cnt = $i;
                    $type = ($j==1) ?'search':'display';

                    if(!empty($pre[$type])){
                        for ($cnt = 1; $cnt <= $less; $cnt++) {
                            $daily[$type][$feature_cnt]["x"]  = $now_YYYY_mm.($feature_cnt+1);
                            $daily[$type][$feature_cnt+1]     = $daily[$type][$feature_cnt];

                            //totals計算
                            foreach( $pre[$type] AS $key => $value){
                                if(!empty( $pre[$type.'_forecast'][$key]) ){
                                    $totals[$type][$feature_cnt+1][$key] = $pre[$type.'_forecast'][$key] + $pre[$type][$key];
                                    $pre[$type][$key]          = $totals[$type][$feature_cnt+1][$key];
                                }
                            }
                            $daily[$type][$feature_cnt]['totals']  = $totals[$type][$feature_cnt];
                            $feature_cnt++;
                        }
                        array_pop($daily[$type]);
                    }
                }
            }

            $graph['search']  = (!empty($daily['search']))  ? $daily['search'] : array();
            $graph['display'] = (!empty($daily['display'])) ? $daily['display']: array();
            unset($data);
            unset($clients);
            unset($daily);
        }
        //Log::debug(__METHOD__."(".__LINE__.") ----> ".print_r( memory_get_usage() ,true));//7.52gb 2015 2/15
        return $graph;
    }


	/**
	* add type(Search,Display)判定
	*
	* @access private
	* @return string
	*/
	private static function _get_category_type($category) {
		if(stristr($category, "Search")){
			$type = 'search';
		}else if(stristr($category, "Display")){
			$type = 'display';
		}else{
			return '';
		}
		return $type;
	}


	/**
	* 停止中のアカウントを取得
	*
	* @access private
	* @return array
	*/
	private static function _get_stop_clients() {
		$results = array();
		$stop_form  =  \Model_Data_AccountAlert::get_stoped();
		foreach ($stop_form as $i => $value) {
			$results[$i] = $value["client_id"];
		}
		return $results;
	}


	/**
	* キーワード別データ取得 
	*
	* @access public
	* @return json
	*/
	public function get_keyword_report() {
		$media        = Input::get("media");
		$media_id     = ($media === "MEDIA_ID_GOOGLE") ? MEDIA_ID_GOOGLE: MEDIA_ID_YAHOO;
		$user_id      = Session::get('user_id_sem');
		$report_dates = array(date("Y-m-d",strtotime('-1 day')), date("Y-m-d",strtotime('-2 day')));

		## マイキーワード情報
		//登録したキーワードを取得
		$mykeyword_data = \Model_Data_Mypage_Keyword::get_mykeyword($user_id);

		//キーワードのみ抜き出して配列に格納
		if(!empty($mykeyword_data)){
			$mykeyword_data = json_decode($mykeyword_data["status"], true);
			if(!empty($mykeyword_data)) $mykeywords = \Arr::pluck($mykeyword_data, 'keyword');
		}

		## クライアント情報
		//user_idから担当クライアント取得
		$tmp_clients = \Model_Mora_Client::get_for_user($user_id);

		if(!empty($tmp_clients)){
			foreach ($tmp_clients as $i => $value) {
				$client_ids[$i]                    = $value["id"];
				$clients["my_clients"][] = array(
					'id'   => $value["id"],
					'name' => \Util_Common_Client::get_client_name($value["id"])
				);
			}
		}else{
			return;
		}

		## アカウント情報
		//client_idからアカウント情報取得
		$tmp_accounts = \Model_Mora_account::get_by_client($client_ids);
		unset($client_ids);

		//account_idのみ抜き出して配列に格納
		$accounts = \Arr::pluck($tmp_accounts, 'account_id');

		//client_idをもとに$clients["my_clients"]と$tmp_accountsを結合
		if(!empty($tmp_accounts) && !empty($clients["my_clients"])){
			foreach ($tmp_accounts as $i => $account_val) {
				foreach ($clients["my_clients"] as $j => $client_val) {
					if($account_val["client_id"] == $client_val["id"]){
						$tmp_accounts[$i]["client_name"] = $client_val["name"];
					}
				}
			}
		}


		## 表示キーワード情報
		//マイキーワードをもとにキーワード別集計データを取得
		if (!empty($mykeywords)) {
				$latest_data      = array();
				$secound_data     = array();

				$match_types = array(
					ADWORDS_REPORT_KW_MATCH_TYPE_EXACT,
					ADWORDS_REPORT_KW_MATCH_TYPE_BROAD,
					ADWORDS_REPORT_KW_MATCH_TYPE_PHRASE,
					YAHOO_BULK_KW_MATCH_TYPE_EXACT,
					YAHOO_BULK_KW_MATCH_TYPE_BROAD,
					YAHOO_BULK_KW_MATCH_TYPE_PHRASE,
				);

				$tmp_keyword_data = \Model_Listingreport_TransactionDailyKeywordReport::get_keyword_data($media_id,$accounts,$report_dates,$mykeywords,$match_types);
				unset($accounts);

				//diff計算
				$latest_data = self::_get_diff($tmp_keyword_data,$report_dates[0]);

				if(!empty($latest_data) && !empty($tmp_accounts)){
					foreach ($latest_data as $i => $latest_data_val) {
						//client_idとclient_nameを挿入
						foreach ($tmp_accounts as $key => $account_val) {
							if($latest_data_val["account_id"] == $account_val["account_id"]){
								$latest_data[$i]["client_id"]   = $account_val["client_id"];
								$latest_data[$i]["client_name"] =  $account_val["client_name"];
							}
						}
					}
				}

				$tmp_keyword_data = array_values($latest_data);
				unset($tmp_accounts);
				unset($latest_data);
				unset($secound_data);

				//マイキーワードを参照して表示データを選定
				$keyword_data = array();
				foreach ($mykeyword_data as $key => $mykeyword_val) {
					foreach ($tmp_keyword_data as $tmp_key => $tmp_keyword_val) {
						if($mykeyword_val["client_id"]   == $tmp_keyword_val["client_id"]  &&
							$mykeyword_val["keyword"]    == $tmp_keyword_val["keyword"]    &&
							strcasecmp($mykeyword_val["match_type"], $tmp_keyword_val["match_type"]) == 0)
						{
							$keyword_data[$key]["client_id"]   = $tmp_keyword_val["client_id"];
							$keyword_data[$key]["client_name"] = $tmp_keyword_val["client_name"];
							$keyword_data[$key]["account_id"]  = $tmp_keyword_val["account_id"];
							$keyword_data[$key]["keyword"]     = $tmp_keyword_val["keyword"];
							$keyword_data[$key]["match_type"]  = $tmp_keyword_val["match_type"];
							$keyword_data[$key]["imp"]         = $tmp_keyword_val["imp"];
							$keyword_data[$key]["click"]       = $tmp_keyword_val["click"];
							$keyword_data[$key]["cost"]        = $tmp_keyword_val["cost"];
							$keyword_data[$key]["conv"]        = $tmp_keyword_val["conv"];

							$keyword_data[$key]["diff_imp"]    = (!empty($tmp_keyword_val["diff_imp"]))   ? $tmp_keyword_val["diff_imp"]   : "";
							$keyword_data[$key]["diff_click"]  = (!empty($tmp_keyword_val["diff_click"])) ? $tmp_keyword_val["diff_click"] : "";
							$keyword_data[$key]["diff_cost"]   = (!empty($tmp_keyword_val["diff_cost"]))  ? $tmp_keyword_val["diff_cost"]  : "";
							$keyword_data[$key]["diff_conv"]   = (!empty($tmp_keyword_val["diff_conv"]))  ? $tmp_keyword_val["diff_conv"]  : "";
						}
					}
				}
			$account_ids = \Arr::pluck($keyword_data, 'account_id');
			array_multisort($account_ids ,SORT_ASC,$keyword_data);

			$clients["term"]         = $report_dates;
			$clients["keyword_data"] = $keyword_data;
			$clients["my_keywords"]  = $mykeyword_data;
			unset($mykeyword_data);
			unset($keyword_data);

		}
		return $this->response($clients);
	}


	/**
	* 差分計算
	*
	* @access private
	* @return array
	*/
	private static function _get_diff($keyword_data,$date) {
		$latest = array();
		//diff計算の為、日付をもとに前日と前々日に分ける
		foreach ($keyword_data as $key => $value) {
			($value["report_date"] == $date) ? $latest[$key] = $value : $secound[$key] = $value;
		}

		if(!empty($latest) && !empty($secound)){
			foreach ($latest as $key => $latest_val) {
				foreach ($secound as $secound_key => $secound_val) {
					if($latest_val["account_id"]  == $secound_val["account_id"] &&
						$latest_val["keyword"]    == $secound_val["keyword"]    &&
						$latest_val["match_type"] == $secound_val["match_type"])
					{
						$latest[$key]["diff_imp"]   = $latest_val["imp"]   - $secound_val["imp"];
						$latest[$key]["diff_click"] = $latest_val["click"] - $secound_val["click"];
						$latest[$key]["diff_cost"]  = $latest_val["cost"]  - $secound_val["cost"];
						$latest[$key]["diff_conv"]  = $latest_val["conv"]  - $secound_val["conv"];
					}
				}
			}
		}
		return $latest;
	}


	/**
	* キーワード別データ取得 
	*
	* @access public
	* @return boolean
	*/
	public function action_set_keyword(){
		$values     = array("user_id" => Session::get('user_id_sem'), "status" => json_encode(Input::post()));
		$register   = \Model_Data_Mypage_Keyword::ins($values);
	return true;
	}


	/**
	 * ログインユーザー名取得
	 *
	 * @access public
	 * @return json
	 */
	public function get_username() {
		// ローカル開発用
		//Session::set('user_name', '木ノ下 大輔');
		return $this->response(array(Session::get('user_name')));
	}


    /**
     * お知らせ取得
     *
     * @access public
     * @return json
     */
    public function get_information() {
        $data  = array();
        $type  = "mypage";
        $limit = 4;
        $data["info"] = \Model_Data_Information::get($type,$limit);
        return $this->response($data);
    }
}
