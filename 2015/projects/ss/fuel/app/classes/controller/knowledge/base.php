<?php

require_once APPPATH."/const/knowledge.php";
require_once APPPATH."/const/error_message.php";

class Controller_Knowledge_Base extends Controller_Base
{
	public $access_url = "/sem/new/knowledge/";

	// ======================================================================
	// 以下共通コントローラーへ移動
	// ======================================================================

	public function before()
	{
		parent::before();
		// 全ログインチェック
		$this->user_id = Session::get('user_id_sem');
		$this->role_id = Session::get('role_id_sem');

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', '資料');
			## AngularJS AppName
			## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
			$this->template->ngapp_name = 'knowledge';

			## ページ固有CSS,JS
			$this->css = array(
				'magicsuggest/magicsuggest-min.css',
				'knowledge/knowledge.css'
			);
			$this->js = array(
				'vendor/jquery.dataTables.min.js',
				'magicsuggest/magicsuggest-min.js',
				'knowledge/app.js',
				'knowledge/services.js',
				'knowledge/controllers.js',
				'knowledge/directives.js',
				'knowledge/filters.js',
			);
		}

		//パラメーターのvalidation
		if (! empty($this->_required_paramater))
		{
			$val = Validation::forge();
			if (!$this->run_valid_params($val))
			{
				foreach($val->error() as $error)
				{
					$errors[] = $error->get_message(VALIDATION_ERROR_ILLEGAL);
				}
				$result = array('message' => $errors);
				return $this->response($result,404);
			}
		}

		$this->setDefine();
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}

	/**
	 * パラメーターの取得
	 *
	 * @param string $label
	 * @param string $default
	 * @access protected
	 * @return mixed
	 */
	protected function get_request_paramater($label, $default = null)
	{
		return Input::param($label,$default);
	}


	/**
	 * validation対象に追加
	 *
	 * @param Array $params
	 * @access protected
	 * @return void
	 */
	protected function set_valid_params(Array $params)
	{
		$this->_required_paramater = $params;
	}

	/**
	 * validationの実行
	 *
	 * @param Validation $val
	 * @access protected
	 * @return boolean
	 */
	protected function run_valid_params(Validation $val)
	{
		foreach ($this->_required_paramater as $paramater_name )
		{
			//TODO ruleの拡張をいずれしたい
			if (is_array(Input::param($paramater_name)))
			{
				foreach (Input::param($paramater_name) as $key => $paramater)
				{
					$val->add($paramater_name.'.'.$key, $paramater_name)->add_rule('required');
				}
			}
			else
			{
				$val->add($paramater_name, $paramater_name)->add_rule('required');
			}
		}

		return $val->run();
	}

	protected function setDefine()
	{
		$bureau_list   = Model_Mora_Bureau::get_list(null);
		foreach ($bureau_list as $key => $value)
		{
			if(	! empty($value['bureau_key']))
			{
				define($value["bureau_key"], $value["bureau_id"]);
			}
		}
	}
}
