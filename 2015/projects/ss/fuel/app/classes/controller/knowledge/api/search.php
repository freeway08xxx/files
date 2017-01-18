<?php
class Controller_Knowledge_Api_Search extends Controller_Knowledge_Api_Base
{
	//受付パラメーター名配列 
	private $post_labels = array(
		'type_section', 
		'free_word',
		'entry_user_id',
		'purpose_ids',
	);

	public function before()
	{
		parent::before();
		//メンバ変数に各パラメーターを格納(本当はvalidationかけたい)
		foreach ($this->post_labels as $label)
		{
			$default = '';
			//type_sectionのみ初期化が必要
			if ($label == 'type_section')
			{
				$default = KNOWLEDGE_SECTION_TYPE_SEM;
			}

			$this->$label = Input::param($label,$default);
		}
	}

	public function action_index()
	{
		$search_options['purpose_ids'] 	= $this->purpose_ids != "" ? explode(',',$this->purpose_ids) : null;
		$search_options['free_word'] 	= $this->free_word;
		$search_options['entry_user_id'] = $this->entry_user_id;

		if ($this->role_id != "1" && $this->role_id != "2")
		{
			//管理者でなければロール指定で検索
			$search_options['role_id'] = $this->role_id;
		}
		//ファイル検索
		$knowledge_files = Util_Knowledge_Search::run($this->type_section, $search_options);


		//検索履歴の登録
		if ($this->free_word != "" && $this->user_id)
		{
			//検索履歴登録
			//TODO いらないんじゃないか疑惑
			//$insert_id = Model_Mora_Knowledge_Word_History::insert_searh_word($this->user_id, $this->free_word);
		}

		return $this->response( array(
			'files' => array_values($knowledge_files),
			'totle_file_count' => count($knowledge_files),
		)); 
	}
}
