<?php

class Controller_Knowledge_Api_Section extends Controller_Knowledge_Api_Base
{
	/**
	 * セクション情報の取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_all()
	{
		$section_all = Util_Knowledge_Common::get_section_all();
		foreach($section_all as $key => $section ) {
			$section['authority_level'] = Util_Knowledge_Common::get_section_authority_level(Session::get('role_id_sem'), $section['key'], Session::get('bureau_id'),Session::get('work_group_id'),Session::get('user_mail_address'));
			$section_all[$key] = $section;
		}

		return $this->response( array(
			'section_all' => $section_all,
		)); 
	}
}
