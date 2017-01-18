<?php
/**
 * 編集お手本コントローラ
 */
class Controller_Customer_Cleaner extends Controller_Customer_Base
{
  // loginユーザ権限チェック用URL
  public $access_url = "/sem/share_monitor/entry_action_schedule.php";

  // 前提共通処理
  public function before()
  {
    // super
    parent::before();

    if ( ! $this->is_restful()) {
      ## ページタイトル
      $this->template->set_global('title', '削除条件指定');
      ## AngularJS AppName
      ## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
      $this->template->ngapp_name = 'dummy';

      ## ページ固有CSS,JS
      $this->css = array(
        'alert/budget.css'
      );
      $this->js = array(
        // dummy Angular App
        'common/ng/dummy.js'
      );
    }

    $this->id = Input::post("id");
    $this->table = Input::post("table");
    $this->column = Input::post("column");
    $this->type = Input::post("type");
    $this->num = Input::post("num");
    $this->errors = array();
  }

  // 画面出力
	public function action_index()
	{
    $table = Request::forge('customer/cleaner/table', false)->execute();

    $this->view->set_safe('table', $table);
    $this->view->set_filename('customer/cleaner/index');
  }

	// テーブルブロック作成
	public function action_table()
	{
    if(Request::is_hmvc()) {
      $data['master_list'] = Model_Master_Cleaner::get();
      $this->view->set($data);
      $this->view->set_filename('customer/cleaner/table');
      return Response::forge($this->view);
    }
  }

  // Master 挿入
	public function action_insert() {
    $val = Validation_Customer_Checker::cleaner_check();
    if ($val->run()) {
      $value = array($this->table, $this->column, $this->type, $this->num);
      Model_Master_Cleaner::ins($value);
    } else {
      $this->errors = $val->error();
    }
  }

  // Master 更新
	public function action_update() {
    $val = Validation_Customer_Checker::cleaner_check();
    if ($val->run()) {
      $value = array('table_name'     => $this->table
                    , 'target_column' => $this->column
                    , 'destroy_type'  => $this->type
                    , 'destroy_num'   => $this->num);
      Model_Master_Cleaner::upd($this->id, $value);
    } else {
      $this->errors = $val->error();
    }
  }

  // Master 削除
	public function action_delete() {
    Model_Master_Cleaner::del($this->id);
  }

  // 前提共通処理
  public function after($response)
  {
    $action = Request::active()->action;
    if ($action == 'insert' || $action == 'update' || $action == 'delete') {
      if ($this->errors) {
        $table = Request::forge('customer/cleaner/table', false)->execute();
        $this->view->set_safe('table', $table);
        $this->view->set_filename('customer/cleaner/index');
      } else {
        Response::redirect('/customer/cleaner/');
      }
    }
    $this->view->set('errors', $this->errors);

    // super
    return parent::after($response);
  }
}
