<?php
require_once APPPATH."/const/budget.php";
/**
 * 予算アラートコントローラ
 */
class Controller_Alert_Budget extends Controller_Alert_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/account_alert/report_alert.php";

  // 前提共通処理
  public function before() {
    // super
    parent::before();

    $this->page = Input::get("p")? Input::get("p"):1;
    $this->account_id = Input::post("account_id");
    $this->limit_budget = Input::post("limit");
    $this->account_budget = Input::post("budget");
    $this->budget_type_id = Input::post("budget_type_id");

  }

  // 予算アラート確認画面TOP出力
	public function action_index() {
    if ($this->admin_flg) {
      $clients = Model_Mora_Client::get_for_user();
    } else {
      $clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
    }
    $this->view->set('alerttable', '');
    $this->view->set('table', '');
    $this->view->set('clients', $clients);
    $this->view->set('client_id', '');
    $this->view->set('current_page', $this->page);
    $this->view->set_filename('alert/budget/index');
  }

  // 予算アラート確認画面出力
	public function action_list($client_id) {
    if ($this->admin_flg) {
      $clients = Model_Mora_Client::get_for_user();
    } else {
      $clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
    }

    $this->view->set('current_page', $this->page);
    $this->view->set('clients', $clients);
    $this->view->set('client_id', $client_id);
    $this->view->set_filename('alert/budget/index');
  }

	// 予算アラート画面テーブル生成用JSON出力
	public function action_table($client_id="") {
    $this->format = 'json';
    $results = array();
    if ($client_id) {
      $total_count = Model_Mora_Account::count($client_id);

      $accounts = Model_Mora_Account::get_for_client($client_id, null, 1000, 0);
      $accounts = Util_Alert_Budget::synchro_budget($accounts);
      foreach ($accounts as $account) {
        $tmp_result = $account;
        $this->view->set('account', $account);
        $this->view->set('alert_flg', false);
        $tmp_result['view'] = $this->view->render('alert/budget/record');
        $this->view->set('alert_flg', true);
        $tmp_result['alert_view'] = $this->view->render('alert/budget/record');
        $results[] = $tmp_result;
      }
    }
    $response = $this->response($results);
    return $response;
  }

  // 媒体予算変更依頼フォーム出力
	public function action_form($client_id) {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    $account = Model_Data_AccountAlert::get_alert_account($client_id, $this->account_id);
    $this->view->set('account', $account);
    $this->view->set_filename('alert/budget/form');
    return Response::forge($this->view);
  }

  // 媒体予算変更依頼フォーム出力
	public function action_send($client_id) {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
    }
    if (!$checker || !$this->account_id) {
      return new Response(false, 404);
    }

    Util_Alert_Budget::send_budget_mail($client_id,$this->account_id,$this->account_budget,$this->budget_type_id);
    return new Response(true, 200);
  }

  // 予算リミット変更
	public function action_upd($client_id) {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
    }
    if (!$checker || !$this->account_id) {
      return new Response(false, 404);
    }

    Model_Data_AccountAlert::upd($client_id, $this->account_id, $this->limit_budget);
    if(Util_Alert_Budget::account_update($client_id, $this->account_id, $this->limit_budget)) {
      return new Response(true, 200);
    } else {
      return new Response(false, 200);
    }
  }

  // アカウント再開
	public function action_start($client_id) {
    $checker = true;
    if(!Input::is_ajax()) {
      $checker = false;
    } elseif (!$this->admin_flg) {
      $checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
    }
    if (!$checker) {
      return new Response(false, 404);
    }

    if(Util_Alert_Budget::account_restart($client_id, $this->account_id)) {
      return new Response(true, 200);
    } else {
      return new Response(false, 404);
    }
  }
}
