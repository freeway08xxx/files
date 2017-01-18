<?php
/**
 * AdwordsOAuth認証画面
 */
class Controller_Customer_AdwordsOAuth extends Controller_Customer_Base
{
  // 前提共通処理
  public function before()
  {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      ## ページタイトル
      $this->template->set_global('title', 'AdwordsOAuth認証画面');
      ## AngularJS AppName
      ## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
      $this->template->ngapp_name = 'dummy';

      ## ページ固有CSS,JS
      $this->css = array(
      );
      $this->js = array(
        // dummy Angular App
        'common/ng/dummy.js'
      );
    }

    $this->code = Input::get("code");
    $this->errors = array();
  }

  // アカウント一覧画面
	public function action_index()
	{
    $table = Request::forge('customer/adwordsoauth/table', false)->execute();

    $this->view->set_safe('table', $table);
    $this->view->set_filename('customer/adwordsoauth/index');
  }

  // リダイレクト処理
	public function action_link($id) {
    // アドワーズ承認ターゲット取得
    $account = Model_Data_Account::get_adwords($id)->current();
    $user = new Model_Api_Google_User();
    // OAuth承認用URL取得
    $oauth_url = urlencode($user->get_oauth_url());
    // Googleログイン画面取得
    $url = urlencode('https://accounts.google.com/ServiceLoginAuth?Email=' .$account['login_id']. '&continue='.$oauth_url);
    // Googleログアウト画面セット
    $url = 'https://accounts.google.com/Logout?hl=ja&continue='.$url;
    // 承認アカウントセッション保持
    Session::set("adwords_auth_account", $id);
    Response::redirect($url);
  }

  // 承認完了画面
	public function action_insert() {
    $table = Request::forge('customer/adwordsoauth/view', false)->execute();

    $this->view->set_safe('table', $table);
    $this->view->set_filename('customer/adwordsoauth/index');
  }

	// アドワーズアカウント表示
	public function action_table()
	{
    if(Request::is_hmvc()) {
      // アドワーズアカウント取得
      $accounts = Model_Data_Account::get_adwords();
      $url_param = array();
      foreach($accounts as $account) {
        $url_param['id'] = $account['id'];
        // リダイレクト用URLセット
        $url_param['url'] = '/customer/adwordsoauth/link/'.$account['id'];
        $url_param['name'] = $account['account_name'];
        $data['url_list'][] = $url_param;
      }
      $this->view->set($data);
      $this->view->set_filename('customer/adwordsoauth/table');
      return Response::forge($this->view);
    }
  }

	// OAuth用トークン登録処理
	public function action_view()
	{
    if(Request::is_hmvc()) {
      $user = new Model_Api_Google_User();
      // 承認アカウントセッション取得、削除
      $id = Session::get("adwords_auth_account");
      Session::delete("adwords_auth_account");
      // OAuth用トークン取得
      $refresh_token = $user->get_refresh_token($this->code);
      $data['res'] = false;
      if ($refresh_token) {
        // トークンが正常に取得できれば、ターゲットアカウントへ登録
        $data['res'] = Model_Data_Account::upd($id, $refresh_token);
      }
      $data['id'] = $id;
      $data['token'] = $refresh_token;

      $this->view->set($data);
      $this->view->set_filename('customer/adwordsoauth/view');
      return Response::forge($this->view);
    }
  }

  // 前提共通処理
  public function after($response)
  {
    $this->view->set('errors', $this->errors);
    // super
    return parent::after($response);
  }
}
