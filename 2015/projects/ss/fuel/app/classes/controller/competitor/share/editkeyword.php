<?php
/**
 * 競合モニタリング
 * キーワードセット編集
 */
class Controller_Competitor_Share_EditKeyword extends Controller_Competitor_Share_Base
{
  public $access_url = "/sem/share_monitor/edit_monitor_keyword.php";
  //最大キーワード数
  public $max_keyword_count = 1000;
  // 管理者以外に開放
  public $editable = true;
  // 登録モード・id
  public $id;
  public $mode;

  public function before()
  {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      $this->template->set_global('title', 'キーワード紐付設定');

      ## ページ固有JS
      $this->js[] = 'competitor/share/edit_keyword.js';
    }

    $this->keyword = Input::post("keyword");
    $this->check_keywords = Input::post("check_keyword");
    $this->action_type = Input::post("action_type");
    $this->id = Input::get("idstc");
    $this->mode = "industry";
    if (!$this->id) {
      $this->id = Input::get("clntc");
      $this->mode = "client";
    }
  }

	public function action_index()
	{
    if ($this->admin_flg) $this->editable = true;

    $message = "";
    if ($this->action_type == "entry") {
      // 登録処理
      $message = Util_Competitor_Share_Keyword::insert_keyword(
                  $this->max_keyword_count, $this->keyword, $this->id, $this->mode);
    } elseif ($this->action_type == "delete") {
      // 削除処理
      $message = Util_Competitor_Share_Keyword::delete_keyword($this->id, $this->mode, $this->check_keywords);
    }

    $search_form = Request::forge('competitor/share/editkeyword/form', false)->execute();
    $table = "";
    if($this->id) {
      $table = Request::forge('competitor/share/editkeyword/table', false)->execute();
    }
    $this->alert_message = $message;
    if ($this->mode == 'industry') {
      $data['word'] = 'idstc='.$this->id;
    }
    if ($this->mode == 'client') {
      $data['word'] = 'clntc='.$this->id;
    }

    $this->view->set($data);
    $this->view->set_safe('search_form', $search_form);
    $this->view->set_safe('table', $table);
    $this->view->set_filename('competitor/share/editkeyword/index');
  }

	/**
	 * 検索フォームブロック作成
	 */
	public function action_form()
	{
    if(Request::is_hmvc()) {
      if ($this->mode == 'industry') {
        $data['target'] = Model_Data_Share_IndustryClass::get_industry_class_list_with_p($this->id);
      }
      if ($this->mode == 'client') {
        $data['target'] = Model_Data_Share_ClientClass::get_client_class_list_with_p($this->id);
      }

      $data['mode'] = $this->mode;
      $this->view->set($data);
      $this->view->set_filename('competitor/share/editkeyword/form');
      return Response::forge($this->view);
    }
  }

	/**
	 * 検索結果テーブルブロック作成
	 */
	public function action_table()
	{
    if(Request::is_hmvc()) {
      //キーワード一覧を取得
      if ($this->mode == 'industry') {
        $data['keyword_list'] = Model_Data_Share_Keyword::get_keyword_list_by_industry($this->id);
      }
      if ($this->mode == 'client') {
        $data['keyword_list'] = Model_Data_Share_Keyword::get_keyword_list_by_client($this->id);
      }

      $data['mode'] = $this->mode;
      $this->view->set($data);
      $this->view->set_filename('competitor/share/editkeyword/table');
      return Response::forge($this->view);
    }
	}
}
