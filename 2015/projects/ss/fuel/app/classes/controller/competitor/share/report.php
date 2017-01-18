<?php
require APPPATH.'vendor/reviser.php';
/**
 * 競合モニタリング
 * レポート作成・出力
 */
class Controller_Competitor_Share_Report extends Controller_Competitor_Share_Base
{
  public $access_url = "/sem/share_monitor/check_monitor_result.php";
  public $device_id = 1;
  public function before(){
    global $media_name_list, $device_type_list;
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      $this->template->set_global('title', 'レポート作成');

      ## ページ固有JS
      $this->js[] = 'vendor/highcharts/highcharts.js';
      $this->js[] = 'competitor/share/entry_action_schedule.js';
      $this->js[] = 'competitor/share/report.js';
    }

		$this->client_id = Input::post("client_id", 0);
		$this->client_class_id = Input::post("client_class_id", 0);
		$this->industry_id = Input::post("industry_id", 0);
		$this->industry_class_id = Input::post("industry_class_id", 0);
		$this->line_type = Input::post("line_type");
		$this->sum_type = Input::post("sum_type");
		$this->device_type = Input::post("device_type");
    if ($this->device_type == $device_type_list['PC']) {
		  $this->device_id = DEVICE_ID_PC;
		  $this->carrier_id = 0;
    }
    if ($this->device_type == $device_type_list['SP']) {
		  $this->device_id = DEVICE_ID_SMARTPHONE;
		  $this->carrier_id = 0;
    }
    if ($this->device_type == $device_type_list['iPhone']) {
		  $this->device_id = DEVICE_ID_SMARTPHONE;
		  $this->carrier_id = 1;
    }
    if ($this->device_type == $device_type_list['Android']) {
		  $this->device_id = DEVICE_ID_SMARTPHONE;
		  $this->carrier_id = 2;
    }
		$this->convert_sub_domein = Input::post("convert_sub_domein");
		$this->media_cost = Input::post("media_cost");
		$this->check_url = Input::post("check_url");
		$this->check_url_df = Input::post("check_url_df");
		$this->day_type = Input::post("day_type");
    $this->from_day = Input::post("from_day");
    $this->to_day = Input::post("to_day");
    if ($this->day_type != 'fromto') {
      $this->to_day = Input::post("from_day");
    }
		$this->replace_url_before = Input::post("replace_url_before");
		$this->replace_url_after = Input::post("replace_url_after");
		$this->yahoo_media = Input::post("yahoo_media");
		$this->google_media = Input::post("google_media");
		$target["yahoo"] = Input::post("yahoo_media");
		$target["google"] = Input::post("google_media");
    $target_media = array();
    foreach($media_name_list as $key => $item) {
      if (!empty($target[$item])) {
        $target_media[] = $key;
      }
    }
    $this->target_media = $target_media;
		$this->sort_key = Input::post("sort_key");
		$this->sort_type = Input::post("sort_type");
		$this->action_type = Input::post("action_type");

    // detail用パラメーター
		$t["yahoo"] = Input::get("y_media");
		$t["google"] = Input::get("g_media");
		$this->target_keyword_id = Input::get("t_key");
		$this->target_url = Input::get("t_url");
    if ($t["yahoo"] || $t["google"]) {
      $target_media = array();
      foreach($media_name_list as $key => $item) {
        if ($t[$item]) {
          $target_media[] = $key;
          break;
        }
      }
      $this->target_media = $target_media;
    }
		$this->ad_flg = Input::get("ad");
		$this->ins_flg = Input::get("ins");
  }

	public function action_index()
	{
    if($this->action_type == 'export') {
      Request::forge('competitor/share/report/export', false)->execute();
      exit;
    }
    $search = Request::forge('competitor/share/report/form', false)->execute();
    $search_form = $search->response->body['form'];
    $search_hidden = $search->response->body['hidden'];
    $table = "";
    $info_message = "";
    if($this->action_type == 'search') {
      $table = Request::forge('competitor/share/report/table', false)->execute();
    }
    if($this->action_type == 'detail') {
      $table = Request::forge('competitor/share/report/tabledetail', false)->execute();
    }
    $this->alert_message = Session::get('error_msg');
    Session::delete('error_msg');
    if (!$this->alert_message && $info_message) {
      $this->alert_message = $info_message;
    }

    $this->view->set_safe('search_form', $search_form);
    $this->view->set_safe('search_hidden', $search_hidden);
    $this->view->set_safe('table', $table);
    $this->view->set_filename('competitor/share/report/index');
	}

	/**
	 * 検索フォームブロック作成
	 */
	public function action_form()
	{
    if(Request::is_hmvc()) {

      $this->alert_message = Session::get('error_msg');
      if (($this->action_type == 'search' || $this->action_type == 'detail') && !$this->alert_message ){
        $display = "none";
        $word = "検索条件を表示する";
      } else {
        $display = "visible";
        $word = "結果を全画面表示";
      }
      if(!$this->action_type) {
        $this->yahoo_media = "1";
        $this->google_media = "1";
      }

      $client_list = Model_Data_Share_Client::get_client_list();
      // クライアント一覧取得
      $my_client_list = null;
      if (!$this->admin_flg) {
        $my_client_list = Model_Data_Share_ClientUser::get_client_list(Session::get("user_mail_address"));
        $tmp_client_list = array();
        foreach($client_list as $item) {
          if (in_array($item['id'], $my_client_list)) {
            $tmp_client_list[] = $item;
          }
        }
        $client_list = $tmp_client_list;
      }
      $client_class_list = Model_Data_Share_ClientClass::get_client_class_list_all($my_client_list);

      $data['device_type'] = $this->device_type;
      $data['sum_type_list'] = array(1 => "1位広告", 2 => "全広告");
      $data['client_list'] = $client_list;
      $data['client_id'] = $this->client_id;
      $data['client_class_list'] = $client_class_list;
      $data['client_class_id'] = $this->client_class_id;
      $data['day_type'] = $this->day_type;
      $data['from_day'] = $this->from_day;
      $data['to_day'] = $this->to_day;
      $data['yahoo_media'] = $this->yahoo_media;
      $data['google_media'] = $this->google_media;
      $data['display'] = $display;
      $data['word'] = $word;
      $data['line_type'] = $this->line_type;
      $data['sum_type'] = $this->sum_type;
      $data['convert_sub_domein'] = $this->convert_sub_domein;
      $data['check_url'] = $this->check_url;
      $data['check_url_df'] = $this->check_url_df;
      $data['replace_url_before'] = $this->replace_url_before;
      $data['replace_url_after'] = $this->replace_url_after;
      $data['device_id'] = $this->device_id;
		  $data['action_type'] = $this->action_type;
		  $data['media_cost'] = $this->media_cost;

      $this->view->set($data);
      $response['form'] = View::forge('competitor/share/report/form',$data);
      $response['hidden'] = View::forge('competitor/share/report/hidden',$data);
      return Response::forge($response);
    } else {
      header("Location:" . LOGIN_URL);
      exit;
    }
	}

	/**
	 * レポートブロック作成
	 */
	public function action_table() {
    if(Request::is_hmvc()) {

      //ソート用キー
      if ($this->sort_type == "desc") {
        $sort_type_pg = SORT_DESC;
      } else {
        $sort_type_pg = SORT_ASC;
      }


      $c_id = $this->carrier_id? $this->carrier_id:null;
      // レポート集計
      $report_util = new Util_Competitor_Share_MonitorReport('client', $this->client_class_id
                            , $this->device_id, $this->carrier_id, $this->from_day, $this->to_day, $this->media_cost);
      if ($this->sum_type == '1') {
        $report_util->set_rank(1);
      }
      $report_util->set_check_url($this->check_url, $this->check_url_df);
      $report_util->set_replace_url($this->replace_url_before, $this->replace_url_after);
      $report_util->set_convert_sub_domein($this->convert_sub_domein);
      $url_result_list = $report_util->get_results($this->target_media, $this->line_type);
      if (!$this->sort_key) {
        if ($this->line_type == 'url') {
          if ($this->yahoo_media) {
            $this->sort_key = 'yahoo_key_count';
          } else {
            $this->sort_key = 'google_key_count';
          }
          $sort_type_pg = SORT_DESC;
        } else {
          ksort($url_result_list);
        }
      }
      if ($this->sort_key) {
        $sort_key_array = array();
        foreach ($url_result_list as $item) {
          $sort_key_array[] = $item[$this->sort_key];
        }
        array_multisort($sort_key_array, $sort_type_pg, $url_result_list);
      }
      $data['url_result_list'] = $url_result_list;

      //最大件数で切る。
      $max_url_count = 1000;

      // データセット
      $data['device_type'] = $this->device_type;
      $data['device_id'] = $this->device_id;
      $data['yahoo_media'] = $this->yahoo_media;
      $data['google_media'] = $this->google_media;
      $data['line_type'] = $this->line_type;
      $data['check_url_list'] = "";
      $data['media_cost'] = $this->media_cost;
      $data['sort_key'] = $this->sort_key;
      $data['sort_type'] = $this->sort_type;
      $data['carrier_id'] = $this->carrier_id;
      $data['max_url_count'] = $max_url_count;
      $viewmodel = ViewModel::forge('competitor/share/report/table');
      $viewmodel->set($data, '');
      return Response::forge($viewmodel);
    } else {
      header("Location:" . LOGIN_URL);
      exit;
    }
	}

	/**
	 * レポートブロック作成
	 */
	public function action_tabledetail() {
    if(Request::is_hmvc()) {
      global $media_name_list;
      $c_id = $this->carrier_id? $this->carrier_id:null;
      // レポート集計
      $report_util = new Util_Competitor_Share_MonitorReport('client', $this->client_class_id
                            , $this->device_id, $this->carrier_id, $this->from_day, $this->to_day, $this->media_cost);
      if ($this->sum_type == '1') {
        $report_util->set_rank(1);
      }
      $report_util->set_keyword_id($this->target_keyword_id);
      $report_util->set_url($this->target_url);
      $report_util->set_check_url($this->check_url, $this->check_url_df);
      $report_util->set_replace_url($this->replace_url_before, $this->replace_url_after);
      $report_util->set_convert_sub_domein($this->convert_sub_domein);
      $report_util->set_ad_flg($this->ad_flg);
      $report_util->set_ins_flg($this->ins_flg);
      if ($media_name_list[$this->target_media[0]] == 'yahoo') {
        $link_key = '?y_media=1';
      } else {
        $link_key = '?g_media=1';
      }
		  if ($this->line_type == 'url') {
        $line_type = 'keyword';
        $link_key .= '&t_url='.$this->target_url;
      } elseif ($this->line_type == 'keyword') {
        $line_type = 'url';
        $link_key .= '&t_key='.$this->target_keyword_id;
      }
      if ($this->ad_flg) $link_key .= '&ad=1';
      if ($this->ins_flg) $link_key .= '&ins=1';
      $data['url_result_list'] = $report_util->get_results($this->target_media, $line_type);
      $data['link_key'] = $link_key;

      //最大件数で切る。
      $max_url_count = 1000;

      // データセット
      $data['media_name'] = $media_name_list[$this->target_media[0]];
      $data['device_type'] = $this->device_type;
      $data['device_id'] = $this->device_id;
      $data['yahoo_media'] = $this->yahoo_media;
      $data['google_media'] = $this->google_media;
      $data['line_type'] = $this->line_type;
      $data['carrier_id'] = $this->carrier_id;
      $data['ad_flg'] = $this->ad_flg;
      $data['ins_flg'] = $this->ins_flg;
      $data['max_url_count'] = $max_url_count;
      $this->view->set($data);
      $this->view->set_filename('competitor/share/report/detailtable');
      return Response::forge($this->view);
    } else {
      header("Location:" . LOGIN_URL);
      exit;
    }
	}
}
