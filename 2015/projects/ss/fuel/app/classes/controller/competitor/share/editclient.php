<?php
/**
 * 競合モニタリング
 * クライアント・クライアント詳細管理画面
 */
class Controller_Competitor_Share_EditClient extends Controller_Competitor_Share_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/share_monitor/edit_monitor_keyword.php";
  public $parent_url;
  public $user_url;
  public $keyword_url;

  // 前提共通処理
  public function before() {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      $this->template->set_global('title', 'クライアント管理');

      ## ページ固有JS
      $this->js[] = 'competitor/share/edit_client.js';
    }

    $this->parent_url = Config::get('base_url')."competitor/share/editclient.php";
    $this->user_url = Config::get('base_url')."competitor/share/editkeyword/set/";
    $this->keyword_url = Config::get('base_url')."competitor/share/editkeyword.php";
    $this->client_id = Input::get("clnt");
  }

  // クライアント管理画面出力
	public function action_index() {
    $table = Request::forge('competitor/share/editclient/table', false)->execute();

    $this->alert_message = Session::get('error_msg');
    $this->view->set_safe('table', $table);
    $this->view->set_filename('competitor/share/editclient/index');
  }

	// クライアントテーブルブロック作成
	public function action_table() {
    if(Request::is_hmvc()) {
      $data['client_id'] = $this->client_id;
      $data['url'] = $this->parent_url . "?clnt=";
      $data['user_url'] = $this->user_url . "?clnt=";
      $data['keyword_url'] = $this->keyword_url . "?clntc=";
      $data['admin_flg'] = $this->admin_flg;

      //クライアント一覧を取得
      $res = Model_Data_Share_Client::get_client_list();
      $client_list = "";
      $data['current_name'] = "";
      // 権限チェック
			$user_mail = Session::get("user_mail_address");
      $admin_client_list = Model_Data_Share_ClientUser::get_client_list($user_mail);

      foreach($res as $item) {
        $data['current_flg'] = false;
        if ($this->client_id == $item['id']) {
          $data['current_flg'] = true;
          $data['current_name'] = $item['name'];
        }
        $data['custom_flg'] = false;
        if ($this->admin_flg || in_array($item['id'], $admin_client_list)) {
          $data['custom_flg'] = true;
        }
        $data['item'] = $item;
        $client_list .= View::forge('competitor/share/editclient/client', $data);
      }
      $client_class_list = "";
      if ($this->client_id) {
        $data['custom_flg'] = false;
        if ($this->admin_flg || in_array($this->client_id, $admin_client_list)) {
          $data['custom_flg'] = true;
        }
        //クライアント詳細一覧を取得
        $res = Model_Data_Share_ClientClass::get_client_class_list($this->client_id);
        //クライアント詳細毎のキーワードカウント取得
        $count_list = Model_Data_Share_ClientClassKeyword::get_clientclass_keyword_count_list($this->client_id);
        foreach($res as $item) {
          $data['item'] = $item;
          $count = 0;
          if (isset($count_list[$item['id']])) {
            $count = $count_list[$item['id']];
          }
          $data['count'] = $count;
          $client_class_list .= View::forge('competitor/share/editclient/clientclass', $data);
        }
      }

      $this->view->set($data);
      $this->view->set_safe('client_list', $client_list);
      $this->view->set_safe('client_class_list', $client_class_list);
      $this->view->set_filename('competitor/share/editclient/table');
      return Response::forge($this->view);
    }
	}

  // Ajax クライアント作成、更新
	public function action_set() {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_ClientUser::check_client_user(Input::get('id'), Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    $data['client_id'] = $this->client_id;
    $data['url'] = $this->parent_url . "?clnt=";
    $data['user_url'] = $this->user_url . "?clnt=";
    $data['item'] = Model_Data_Share_Client::set_client(Input::get('sort'), Input::get('name'), Input::get('id'));
    if (!Input::get('id')) {
      $item = Model_Data_Share_ClientUser::insert_client_user(
                 $data['item']['id'], Session::get('user_mail_address'));
    }
    $data['current_flg'] = false;
    if ($this->client_id == Input::get('id')) {
      $data['current_flg'] = true;
    }

    $data['admin_flg'] = $this->admin_flg;
    $data['custom_flg'] = $checker;
    $view = View::forge('competitor/share/editclient/client', $data);
    return new Response($view, 200);
  }

  // Ajax クライアント削除
	public function action_del() {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_ClientUser::check_client_user(
                    Input::get('del_id'), Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    Model_Data_Share_Client::del_client(Input::get('del_id'));
    return new Response(true, 200);
  }

  // Ajax クライアント詳細作成、更新
	public function action_set_class() {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_ClientUser::check_client_user(
                    $this->client_id, Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    $data['keyword_url'] = $this->keyword_url . "?clntc=";

    $data['item'] = Model_Data_Share_ClientClass::set_client_class(Input::get('sort'), Input::get('name'), Input::get('id'), $this->client_id);

    $data['count'] = 0;
    $data['admin_flg'] = $this->admin_flg;
    $data['custom_flg'] = true;
    $view = View::forge('competitor/share/editclient/clientclass', $data);
    return new Response($view, 200);
  }

  // Ajax クライアント詳細削除
	public function action_del_class() {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Data_Share_ClientUser::check_client_user(
                    $this->client_id, Session::get('user_mail_address'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    Model_Data_Share_ClientClass::del_client_class(Input::get('del_id'));
    return new Response(true, 200);
  }

}
