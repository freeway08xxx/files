<?php
/**
 * 競合モニタリング
 * ユーザ管理モーダル
 */
class Controller_Competitor_Share_EditUser extends Controller_Competitor_Share_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/share_monitor/edit_monitor_keyword.php";

  // 前提共通処理
  public function before()
  {
    // super
    parent::before();

    if(!Input::is_ajax()) {
      // Ajax以外のアクセスを認めない
      Response::redirect('login/index');
    }
    $this->industry_id = Input::get("idst");
    $this->industry_name = Input::get("idstn");
    $this->client_id = Input::get("clnt");
    $this->client_name = Input::get("clntn");
    $this->mode = Input::get("mode");
  }

  // 業種管理画面出力
	public function action_index()
	{
    //ユーザ一覧を取得
    $user_list = "";
    if ($this->mode == 'industry') {
      $res = Model_Data_Share_IndustryUser::get_user_list($this->industry_id);
      $data['id'] = $this->industry_id;
      $data['name'] = $this->industry_name;
    }
    if ($this->mode == 'client') {
      $res = Model_Data_Share_ClientUser::get_user_list($this->client_id);
      $data['id'] = $this->client_id;
      $data['name'] = $this->client_name;
    }
    $data['mode'] = $this->mode;
    foreach($res as $item) {
      $data['item'] = $item;
      $user_list .= View::forge('competitor/share/edituser/user', $data);
    }
    $this->view->set($data);
    $this->view->set_safe('user_list', $user_list);
    $this->view->set_filename('competitor/share/edituser/index');
    return Response::forge($this->view);
  }

  // Ajax 業種作成、更新
	public function action_set()
	{
    if ($this->mode == 'industry') {
      $is_insert = Model_Data_Share_IndustryUser::insert_industry_user(Input::get('id'), Input::get('address'));
    }
    if ($this->mode == 'client') {
      $is_insert = Model_Data_Share_ClientUser::insert_client_user(Input::get('id'), Input::get('address'));
    }
    $data['id'] = Input::get('id');
    $data['mode'] = $this->mode;
    if ($is_insert) {
      if ($this->mode == 'industry') {
        $this->industry_id = Input::get('id');
      }
      if ($this->mode == 'client') {
        $this->client_id = Input::get('id');
        $this->client_name = Input::get('client_name');
      }

      $view = $this->action_index();

      return new Response($view, 200);
    } else {
      return new Response('Duplicate', 200);
    }
  }

  // Ajax 業種削除
	public function action_del()
	{
    if ($this->mode == 'industry') {
      Model_Data_Share_IndustryUser::delete_industry_user(Input::get('del_id'), Input::get('id'));
    }
    if ($this->mode == 'client') {
      Model_Data_Share_ClientUser::delete_client_user(Input::get('del_id'), Input::get('id'));
    }
    return new Response(true, 200);
  }


}
