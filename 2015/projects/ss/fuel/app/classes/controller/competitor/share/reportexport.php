<?php
/**
 * 競合モニタリング
 * レポート作成・出力
 */
class Controller_Competitor_Share_ReportExport extends Controller_Competitor_Share_Report
{
	/**
	 * レポートエクスポート処理
	 */
	public function action_index()
	{
    $this->format = 'csv';
    //ソート用キー
    if ($this->sort_type == "desc") {
      $sort_type_pg = SORT_DESC;
    } else {
      $sort_type_pg = SORT_ASC;
    }

    // レポート集計
    $report_util = new Util_Competitor_Share_MonitorReport('client', $this->client_class_id
                          , $this->device_id, $this->carrier_id, $this->from_day, $this->to_day, $this->media_cost);
    if ($this->sum_type == '1') {
      $report_util->set_rank(1);
    }
    $report_util->set_check_url($this->check_url, $this->check_url_df);
    $report_util->set_replace_url($this->replace_url_before, $this->replace_url_after);
    $report_util->set_convert_sub_domein($this->convert_sub_domein);
    $tmp_result_list = $report_util->get_results($this->target_media, $this->line_type);
    if (!$this->sort_key) {
      if ($this->line_type == 'url') {
        if ($this->yahoo_media) {
          $this->sort_key = 'yahoo_key_count';
        } else {
          $this->sort_key = 'google_key_count';
        }
        $sort_type_pg = SORT_DESC;
      } else {
        ksort($tmp_result_list);
      }
    } 
    if ($this->sort_key) {
      $sort_key_array = array();
      foreach ($tmp_result_list as $item) {
        $sort_key_array[] = $item[$this->sort_key];
      }
      array_multisort($sort_key_array, $sort_type_pg, $tmp_result_list);
    }
    $url_result_list = array();
    foreach ($tmp_result_list as $key => $item) {
      $tmp_result = array();
      if ($this->line_type == 'url') {
        $tmp_result['URL'] = $item['disp_key'];
      } else {
        $tmp_result['キーワード'] = $item['disp_key'];
      }
      if ($this->yahoo_media) {
        if ($this->line_type == 'url') {
          $tmp_result['[Yahoo]KW数'] = number_format($item['yahoo_key_count']);
        }
        $tmp_result['[Yahoo]広告数'] = number_format($item['yahoo_ad_count']);
        $tmp_result['[Yahoo]インサーション'] = number_format($item['yahoo_ins_count']);
        $tmp_result['[Yahoo]imp'] = number_format($item['yahoo_imp']);
        $tmp_result['[Yahoo]click'] = number_format($item['yahoo_click']);
        $tmp_result['[Yahoo]cost'] = number_format($item['yahoo_cost']);
      }
      if ($this->google_media) {
        if ($this->line_type == 'url') {
          $tmp_result['[Google]KW数'] = number_format($item['google_key_count']);
        }
        $tmp_result['[Google]広告数'] = number_format($item['google_ad_count']);
        $tmp_result['[Google]インサーション'] = number_format($item['google_ins_count']);
        $tmp_result['[Google]imp'] = number_format($item['google_imp']);
        $tmp_result['[Google]click'] = number_format($item['google_click']);
        $tmp_result['[Google]cost'] = number_format($item['google_cost']);
      }
      $url_result_list[] = $tmp_result;
    }
    $client_info = Model_Data_Share_ClientClass::get_client_class_list_with_p($this->client_class_id);
    if ($this->from_day == $this->to_day) {
      $day_name = $this->from_day;
    } else {
      $day_name = $this->from_day."_".$this->to_day;
    }
    $day_name = str_replace('/','',$day_name);
    $file_name = $client_info['parent_name'].'_'.$client_info['name'].'_'.$day_name.'.csv';
    $response = $this->response($url_result_list);
    $response->set_header('Content-Disposition', 'attachment; filename="'.$file_name.'"');
    return $response;
	}
  
	/**
	 * レポート全件エクスポート処理
	 */
	public function action_all()
	{
    global $media_name_list;
    $this->format = 'csv';
    //ソート用キー
    if ($this->sort_type == "desc") {
      $sort_type_pg = SORT_DESC;
    } else {
      $sort_type_pg = SORT_ASC;
    }

    $url_result_list = array();
    foreach ($this->target_media as $media_item) {
      $target_media = array($media_item);
      $param = $media_name_list[$media_item];
      // レポート集計
      $report_util = new Util_Competitor_Share_MonitorReport('client', $this->client_class_id
                            , $this->device_id, $this->carrier_id, $this->from_day, $this->to_day, $this->media_cost);
      if ($this->sum_type == '1') {
        $report_util->set_rank(1);
      }
      $report_util->set_check_url($this->check_url, $this->check_url_df);
      $report_util->set_replace_url($this->replace_url_before, $this->replace_url_after);
      $report_util->set_convert_sub_domein($this->convert_sub_domein);
      $tmp_result_list = $report_util->get_results($target_media, $this->line_type."_all");
      foreach ($tmp_result_list as $key => $item) {
        $tmp_result = array();
        $tmp_result['メディア'] = $param;
        $disp_keys = explode('_', $item['disp_key']);
        if ($this->line_type == 'url') {
          $tmp_result['URL'] = $disp_keys[0];
          $tmp_result['キーワード'] = $disp_keys[1];
        } else {
          $tmp_result['キーワード'] = $disp_keys[0];
          $tmp_result['URL'] = $disp_keys[1];
        }
        $tmp_result['変換前URL'] = $item[$param.'_o_url'];
        if ($this->device_type == '2') {
          $rank = round($item[$param."_rank_sum"] / $item[$param."_count"]);
        } else {
          $rank = $item[$param."_rank"];
        }
        $tmp_result['ランク'] = $rank;
        $tmp_result['LPURL'] = $item[$param.'_lp_url'];
        $tmp_result['タイトル'] = $item[$param.'_title'];
        $tmp_result['説明文'] = $item[$param.'_description'];
        $tmp_result['imp'] = number_format($item[$param.'_imp']);
        $tmp_result['click'] = number_format($item[$param.'_click']);
        $tmp_result['cost'] = number_format($item[$param.'_cost']);
        $url_result_list[] = $tmp_result;
      }
    }
    $client_info = Model_Data_Share_ClientClass::get_client_class_list_with_p($this->client_class_id);
    if ($this->from_day == $this->to_day) {
      $day_name = $this->from_day;
    } else {
      $day_name = $this->from_day."_".$this->to_day;
    }
    $day_name = str_replace('/','',$day_name);
    $file_name = $client_info['parent_name'].'_'.$client_info['name'].'_'.$day_name.'_all.csv';
    $response = $this->response($url_result_list);
    $response->set_header('Content-Disposition', 'attachment; filename="'.$file_name.'"');
    return $response;
	}
  
	/**
	 * レポートエクスポート処理
	 */
	public function action_detail() {
    $this->format = 'csv';
    global $media_name_list;
    if ($media_name_list[$this->target_media[0]] == 'yahoo') {
      $name = 'Yahoo';
      $param = 'yahoo';
    } else {
      $name = 'Google';
      $param = 'google';
    }

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
    if ($this->line_type == 'url') {
      $line_type = 'keyword';
    } elseif ($this->line_type == 'keyword') {
      $line_type = 'url';
    }
    $tmp_result_list = $report_util->get_results($this->target_media, $line_type);

    $url_result_list = array();
    foreach ($tmp_result_list as $key => $item) {
      $tmp_result = array();
      $device_name = "PC";
      if ($this->device_id == 3) $device_name = "SP:";
      if (isset($item[$param."_carrier_1"])) {
        $device_name .= "iPhone";
      }
      if (isset($item[$param."_carrier_2"])) {
        if (isset($item[$param."_carrier_1"])) {
          $device_name .= "　";
        }
        $device_name .= "Android";
      }
      $tmp_result['デバイス'] = $device_name;
      if ($this->line_type == 'url') {
        if (!$this->ad_flg) {
          $tmp_result['キーワード'] = $item['disp_key'];
        }
      } else {
        $tmp_result['URL'] = $item['disp_key'];
      }
      $tmp_result['['.$name.']変換前URL'] = $item[$param.'_o_url'];
      if ($this->device_type == '2') {
        $rank = round($item[$param."_rank_sum"] / $item[$param."_count"]);
      } else {
        $rank = $item[$param."_rank"];
      }
      $tmp_result['['.$name.']ランク'] = $rank;
      $tmp_result['['.$name.']LPURL'] = $item[$param.'_lp_url'];
      $tmp_result['['.$name.']タイトル'] = $item[$param.'_title'];
      $tmp_result['['.$name.']説明文'] = $item[$param.'_description'];
      $tmp_result['['.$name.']imp'] = number_format($item[$param.'_imp']);
      $tmp_result['['.$name.']click'] = number_format($item[$param.'_click']);
      $tmp_result['['.$name.']cost'] = number_format($item[$param.'_cost']);
      $url_result_list[] = $tmp_result;
    }
    $client_info = Model_Data_Share_ClientClass::get_client_class_list_with_p($this->client_class_id);
    if ($this->from_day == $this->to_day) {
      $day_name = $this->from_day;
    } else {
      $day_name = $this->from_day."_".$this->to_day;
    }
    $day_name = str_replace('/','',$day_name);
    $file_name = $client_info['parent_name'].'_'.$client_info['name'].'_'.$day_name.'_detail.csv';
    $response = $this->response($url_result_list);
    $response->set_header('Content-Disposition', 'attachment; filename="'.$file_name.'"');
    return $response;
	}

	/**
	 * レポートエクスポート処理
	 */
	public function action_daysdetail() {
    $this->format = 'csv';
    global $media_name_list;
    if ($media_name_list[$this->target_media[0]] == 'yahoo') {
      $name = 'Yahoo';
      $param = 'yahoo';
    } else {
      $name = 'Google';
      $param = 'google';
    }

    $c_id = $this->carrier_id? $this->carrier_id:null; 
    
    $todate = date("Y-m-d", strtotime($this->to_day));
    for ( $i = 0; $i < 90; $i++ ) {
      $fromdate = date("Y-m-d", strtotime($this->from_day.' +'.$i.' day'));
      if ($todate < $fromdate) {
        break;
      }

      // レポート集計
      $report_util = new Util_Competitor_Share_MonitorReport('client', $this->client_class_id
                            , $this->device_id, $this->carrier_id, $fromdate, $fromdate, $this->media_cost);
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
      if ($this->line_type == 'url') {
        $line_type = 'keyword';
      } elseif ($this->line_type == 'keyword') {
        $line_type = 'url';
      }
      $tmp_result_lists[$fromdate] = $report_util->get_results($this->target_media, $line_type);
    }

    $url_result_list = array();
    foreach ($tmp_result_lists as $do_date => $tmp_result_list) {
      foreach ($tmp_result_list as $key => $item) {
        $tmp_result = array();
        $device_name = "PC";
        if ($this->device_id == 3) $device_name = "SP:";
        if (isset($item[$param."_carrier_1"])) {
          $device_name .= "iPhone";
        }
        if (isset($item[$param."_carrier_2"])) {
          if (isset($item[$param."_carrier_1"])) {
            $device_name .= "　";
          }
          $device_name .= "Android";
        }
        $tmp_result['デバイス'] = $device_name;
        if ($this->line_type == 'url') {
          if (!$this->ad_flg) {
            $tmp_result['キーワード'] = $item['disp_key'];
          }
        } else {
          $tmp_result['URL'] = $item['disp_key'];
        }
        $tmp_result['['.$name.']変換前URL'] = $item[$param.'_o_url'];
        $url_result_list[$key] = $tmp_result;
      }
    }
    // クーロン実行時間取得
    $search_param['industry_class_id'] = $this->industry_class_id;
    $search_param['client_class_id'] = $this->client_class_id;
    $search_param['media_id'] = $this->target_media[0];
    $search_param['device_id'] = $this->device_id;
    $search_param['from_day'] = $this->from_day;
    $search_param['to_day'] = $this->to_day;
    $crawl_log = Model_Data_Share_CrawlLog::get_crawl_log($search_param);
    $action_status = array();
    foreach($crawl_log as $item) {
      $action_status[$item['action_day']] = $item;
    }
    $return_result_list = array();
    for ( $i = 0; $i < 90; $i++ ) {
      $fromdate = date("Y-m-d", strtotime($this->from_day.' +'.$i.' day'));
      if ($todate < $fromdate) {
        break;
      }
      foreach ($url_result_list as $key => $days_data) {
        if ($tmp_result_lists[$fromdate]) {
        $tmp_result = array();
          if (isset($tmp_result_lists[$fromdate][$key])) {
            $item = $tmp_result_lists[$fromdate][$key];
            if ($this->device_type == '2') {
              $rank = round($item[$param."_rank_sum"] / $item[$param."_count"]);
            } else {
              $rank = $item[$param."_rank"];
            }
            $status = '';
            if (!isset($action_status[$fromdate])) {
              $status = 'MISSING';
            } else {
              if ($action_status[$fromdate]['status'] == 'RUNNING' && $fromdate < date('Y-m-d')) {
                $status = 'MISSING';
              }
              if ($action_status[$fromdate]['status'] == 'RUNNING' && $fromdate == date('Y-m-d')) {
                $status = 'RUNNING';
              }
              if ($action_status[$fromdate]['status'] == 'FINISHED') {
                $status = 'FINISHED('.$action_status[$fromdate]['end_time'].')';
              }
            }
            $tmp_result['['.$name.'-'.$fromdate.']実行日']   = $fromdate;
            $tmp_result['['.$name.'-'.$fromdate.']実行ステータス']   = $status;
            $tmp_result['['.$name.'-'.$fromdate.']ランク']   = $rank;
            $tmp_result['['.$name.'-'.$fromdate.']LPURL']    = $item[$param.'_lp_url'];
            $tmp_result['['.$name.'-'.$fromdate.']タイトル'] = $item[$param.'_title'];
            $tmp_result['['.$name.'-'.$fromdate.']説明文']   = $item[$param.'_description'];
            $tmp_result['['.$name.'-'.$fromdate.']imp']      = number_format($item[$param.'_imp']);
            $tmp_result['['.$name.'-'.$fromdate.']click']    = number_format($item[$param.'_click']);
            $tmp_result['['.$name.'-'.$fromdate.']cost']     = number_format($item[$param.'_cost']);
          } else {
            $tmp_result['['.$name.'-'.$fromdate.']実行日']   = $fromdate;
            $tmp_result['['.$name.'-'.$fromdate.']実行ステータス']   = "";
            $tmp_result['['.$name.'-'.$fromdate.']ランク']   = "";
            $tmp_result['['.$name.'-'.$fromdate.']LPURL']    = "";
            $tmp_result['['.$name.'-'.$fromdate.']タイトル'] = "";
            $tmp_result['['.$name.'-'.$fromdate.']説明文']   = "";
            $tmp_result['['.$name.'-'.$fromdate.']imp']      = "";
            $tmp_result['['.$name.'-'.$fromdate.']click']    = "";
            $tmp_result['['.$name.'-'.$fromdate.']cost']     = "";
          }
          if (isset($return_result_list[$key])) {
            $return_result_list[$key] = array_merge($return_result_list[$key], $tmp_result);
          } else {
            $return_result_list[$key] = array_merge($days_data, $tmp_result);
          }
        }
      }
    }
    $client_info = Model_Data_Share_ClientClass::get_client_class_list_with_p($this->client_class_id);
    if ($this->from_day == $this->to_day) {
      $day_name = $this->from_day;
    } else {
      $day_name = $this->from_day."_".$this->to_day;
    }
    $day_name = str_replace('/','',$day_name);
    $file_name = $client_info['parent_name'].'_'.$client_info['name'].'_'.$day_name.'_days_detail.csv';
    $response = $this->response($return_result_list);
    $response->set_header('Content-Disposition', 'attachment; filename="'.$file_name.'"');
    return $response;
	}
}
