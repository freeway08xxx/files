<?php
/**
 * 競合モニタリング
 * 業種・業種詳細管理画面
 */
class Controller_Competitor_Share_EditIndustry extends Controller_Competitor_Share_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/share_monitor/edit_monitor_keyword.php";
  public $parent_url = "/sem/keyword_monitor/edit_industry.php";
  public $user_url = "/sem/keyword_monitor/edit_monitor_keyword_set.php";
  public $keyword_url = "/sem/keyword_monitor/edit_monitor_keyword.php";
  // 管理者以外に開放
  public $editable = true;

  // 前提共通処理
  public function before()
  {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      $this->template->set_global('title', '業種管理');

      ## ページ固有JS
      $this->js[] = 'competitor/share/edit_industry.js';
    }

    $this->industry_id = Input::get("idst");
  }

  // 業種管理画面出力
	public function action_index()
	{
    if ($this->admin_flg) $this->editable = true;

    $table = Request::forge('competitor/share/editindustry/table', false)->execute();

    $this->alert_message = Session::get('error_msg');
    $this->view->set_safe('table', $table);
    $this->view->set_filename('competitor/share/editindustry/index');
  }

	// 業種テーブルブロック作成
	public function action_table()
	{
    if(Request::is_hmvc()) {
      if ($this->admin_flg) $this->editable = true;
      $data['editable'] = $this->editable;
      $data['industry_id'] = $this->industry_id;
      $data['url'] = $this->parent_url . "?idst=";
      $data['user_url'] = $this->user_url . "?idst=";
      $data['keyword_url'] = $this->keyword_url . "?idstc=";
      $data['admin_flg'] = $this->admin_flg;

      //業種一覧を取得
      $res = Model_Data_Share_Industry::get_industry_list();
      $industry_list = "";
      $data['current_name'] = "";
			$user_mail = Session::get("user_mail_address");
      $admin_industry_list = Model_Data_Share_IndustryUser::get_industry_list($user_mail);

      foreach($res as $item) {
        $data['current_flg'] = false;
        if ($this->industry_id == $item['id']) {
          $data['current_flg'] = true;
          $data['current_name'] = $item['name'];
        }
        $data['custom_flg'] = false;
        if ($this->admin_flg || in_array($item['id'], $admin_industry_list)) {
          $data['custom_flg'] = true;
        }
        $data['item'] = $item;
        $industry_list .= View::forge('competitor/share/editindustry/industry', $data);
      }
      $industry_class_list = "";
      if ($this->industry_id) {
        $data['custom_flg'] = false;
        if ($this->admin_flg || in_array($this->industry_id, $admin_industry_list)) {
          $data['custom_flg'] = true;
        }
        //業種詳細一覧を取得
        $res = Model_Data_Share_IndustryClass::get_industry_class_list($this->industry_id);
        //業種詳細毎のキーワードカウント取得
        $count_list = Model_Data_Share_IndustryClassKeyword::get_industryclass_keyword_count_list($this->industry_id);
        foreach($res as $item) {
          $data['item'] = $item;
          $count = 0;
          if (isset($count_list[$item['id']])) {
            $count = $count_list[$item['id']];
          }
          $data['count'] = $count;
          $industry_class_list .= View::forge('competitor/share/editindustry/industryclass', $data);
        }
      }

      $this->view->set($data);
      $this->view->set_safe('industry_list', $industry_list);
      $this->view->set_safe('industry_class_list', $industry_class_list);
      $this->view->set_filename('competitor/share/editindustry/table');
      return Response::forge($this->view);
    }
	}

  // Ajax 業種作成、更新
	public function action_set()
	{
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_IndustryUser::check_industry_user(Input::get('id'), Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    $data['industry_id'] = $this->industry_id;
    $data['url'] = $this->parent_url . "?idst=";
    $data['user_url'] = $this->user_url . "?idst=";

    $data['item'] = Model_Data_Share_Industry::set_industry(Input::get('sort'), Input::get('name'), Input::get('id'));
    $data['current_flg'] = false;
    if ($this->industry_id == Input::get('id')) {
      $data['current_flg'] = true;
    }

    $data['admin_flg'] = $this->admin_flg;
    $data['custom_flg'] = true;
    $view = View::forge('competitor/share/editindustry/industry', $data);
    return new Response($view, 200);
  }

  // Ajax 業種削除
	public function action_del()
	{
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_IndustryUser::check_industry_user(
                    Input::get('del_id'), Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    Model_Data_Share_Industry::del_industry(Input::get('del_id'));
    return new Response(true, 200);
  }

  // Ajax 業種詳細作成、更新
	public function action_set_class()
	{
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_IndustryUser::check_industry_user(
                    $this->industry_id, Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    $data['keyword_url'] = $this->keyword_url . "?idstc=";
    $data['item'] = Model_Data_Share_IndustryClass::set_industry_class(Input::get('sort')
                      , Input::get('name'), Input::get('id'), $this->industry_id);

    $data['count'] = 0;
    $data['admin_flg'] = $this->admin_flg;
    $data['custom_flg'] = true;
    $view = View::forge('competitor/share/editindustry/industryclass', $data);
    return new Response($view, 200);
  }

  // Ajax 業種詳細削除
	public function action_del_class()
	{
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_IndustryUser::check_industry_user(
                    $this->industry_id, Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    Model_Data_Share_IndustryClass::del_industry_class(Input::get('del_id'));
    return new Response(true, 200);
  }

}
