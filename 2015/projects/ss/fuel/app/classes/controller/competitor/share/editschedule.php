<?php
/**
 * 競合モニタリング
 * ユーザ管理モーダル
 */
class Controller_Competitor_Share_EditSchedule extends Controller_Competitor_Share_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/share_monitor/entry_action_schedule.php";
  public $type = null;

  // 前提共通処理
  public function before() {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      $this->template->set_global('title', '自動実行設定');

      ## ページ固有JS
      $this->js[] = 'competitor/share/entry_action_schedule.js';
    }

    $this->industry_id = Input::get("idst");
    $this->client_id = Input::get("clnt");
    $this->mode = Input::post("action_type");
    if (($this->industry_id || $this->client_id)) {
      if (!$this->mode) $this->mode = 'search';
      if ($this->industry_id) {
        $this->type = "idst";
      } else {
        $this->type = "clnt";
      }
    }
  }

  // 業種管理画面出力
	public function action_index() {
    $table = "";
    if ($this->mode == 'upsert' || $this->mode == 'search') {
      if (!$this->admin_flg) {
        $checker = false;
        if ($this->client_id) {
          $checker = Model_Data_Share_ClientUser::check_client_user(
                                        $this->client_id, Session::get('user_mail_address'));
        } elseif ($this->industry_id) {
          $checker = Model_Data_Share_IndustryUser::check_industry_user(
                                        $this->industry_id, Session::get('user_mail_address'));
        }
        if (!$checker) {
          return new Response(false, 404);
        }
      }

      if ($this->mode == 'upsert') {
        if ($this->type == 'idst') {
          $class_list = Model_Data_Share_IndustryClass::get_industry_class_list($this->industry_id);
        } elseif ($this->type == 'clnt') {
          $class_list = Model_Data_Share_ClientClass::get_client_class_list($this->client_id);
        }
        Util_Competitor_Share_Schedule::schedule_upsert($this->type, $class_list);
        $this->alert_message = "クロールスケジュールを設定しました。";
      }
      $table = Request::forge('competitor/share/editschedule/table', false)->execute();
    }

    $data['type'] = $this->type;
    $data['industry_id'] = $this->industry_id;
    $data['client_id'] = $this->client_id;
//    $data['industry_list'] = Model_Data_Share_IndustryUser::get_select_list_with_admin(
//                                $this->admin_flg, Session::get('user_mail_address'));
    $data['industry_list'] = array();
    $data['client_list'] = Model_Data_Share_ClientUser::get_select_list_with_admin(
                                $this->admin_flg, Session::get('user_mail_address'));
    $form = View::forge('competitor/share/editschedule/form', $data);

    $this->view->set($data);
    $this->view->set_safe('table', $table);
    $this->view->set_safe('form', $form);
    $this->view->set_filename('competitor/share/editschedule/index');
  }

	// 詳細スケジュールテーブルブロック作成
	public function action_table() {
    if(Request::is_hmvc()) {
      if ($this->industry_id) {
        $data['class_list'] = Model_Data_Share_CrawlSchedule::get_schedule_list_with_industry($this->industry_id);
      } elseif ($this->client_id) {
        $data['class_list'] = Model_Data_Share_CrawlSchedule::get_schedule_list_with_client($this->client_id);
      }
      $this->view->set($data);
      $this->view->set_filename('competitor/share/editschedule/table');
      return Response::forge($this->view);
    }
  }
}
