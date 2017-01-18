<?php

class Controller_Knowledge_Api_File extends Controller_Knowledge_Api_Base
{
	public function action_detail($file_id)
	{
		$type_section = Input::param('type_section');
		if(empty($type_section)){
			$result = array('message' => 'This type_section is required');
			return $this->response($result,404);
		}


		//ファイル詳細取得
		if(!empty($file_id))
		{
			if(!Util_Knowledge_Common::is_file_allow_authority($this->role_id, $file_id)){
				$result = array('message' => 'This type_section is required');
				return $this->response($result,404);
			}
			$file_detail = Util_Knowledge_Common::get_file_detail($file_id);
		}
		//用途取得
		$purpose_all = Model_Mora_Knowledge_Purpose::find_by_section($type_section);
		//権限名取得
		$role_all = model_mora_role::find_by_all();
		//管理者表示
		$section_authority_level = Util_Knowledge_Common::get_section_authority_level($this->role_id, $type_section, Session::get('bureau_id'),Session::get('work_group_id'),Session::get('user_mail_address'));

		return $this->response( array(
			'file' => !empty($file_detail['file'])? Util_Knowledge_Common::format_file_view($file_detail['file']) : null , 
			'file_entry_user' => !empty($file_detail['file_entry_user'])? $file_detail['file_entry_user'] : null ,
			'file_purpose' => !empty($file_detail['file_purpose'])? $file_detail['file_purpose'] : null ,
			'file_role_ids' =>	!empty($file_detail['file_con_role']) ? Arr::pluck($file_detail['file_con_role'], 'role_id') : null ,
			'file_comment_history' =>!empty($file_detail['file_comment_history'])? $file_detail['file_comment_history'] : null , 
			'file_update_history'=>	!empty($file_detail['file_update_history'])? $file_detail['file_update_history'] : null , 
			'file_download_history' => 	!empty($file_detail['file_download_history'])? $file_detail['file_download_history'] : null ,
			'is_file_exists' => !empty($file_detail['is_file_exists'])? $file_detail['is_file_exists'] : false , 
			'purpose_all' => array_values($purpose_all), 
			'role_all' => 	array_values($role_all), 
			'type_section' =>			$type_section , 
			'section_authority_level' => $section_authority_level
		));
	}
}
