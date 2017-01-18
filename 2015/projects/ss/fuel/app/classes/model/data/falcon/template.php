<?php

class Model_Data_Falcon_Template extends \Model
{
	protected static $_table_name = 't_falcon_template_setting';

	public static function get($client_id){
		$query = DB::select('t_falcon_template_setting.*', 't_category_genre.category_genre_name', 'user.user_name',
							'CASE WHEN t_falcon_template_setting."updated_user" IS NULL THEN t_falcon_template_setting."created_user" ELSE t_falcon_template_setting."updated_user" END as "user_id"',
							'CASE WHEN t_falcon_template_setting."updated_at" IS NULL THEN t_falcon_template_setting."created_at" ELSE t_falcon_template_setting."updated_at" END as "datetime"')->from(self::$_table_name);
		$query->join('t_category_genre', 'LEFT')->on('t_category_genre.id', '=', 't_falcon_template_setting.category_genre_id');
		$query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when t_falcon_template_setting."updated_user" is null then t_falcon_template_setting."created_user" else t_falcon_template_setting."updated_user" end');
		$query->where('t_falcon_template_setting.client_id', $client_id);
		$query->order_by('t_falcon_template_setting.created_at', 'desc');
		return $query->execute()->as_array();
	}

	public static function get_info($template_id){
		$query = DB::select('t_falcon_template_setting.*', 't_category_genre.category_genre_name')->from(self::$_table_name);
		$query->join('t_category_genre', 'LEFT')->on('t_category_genre.id', '=', 't_falcon_template_setting.category_genre_id');
		$query->where('t_falcon_template_setting.id', $template_id);
		return $query->execute()->current();
	}

	public static function get_tmp($template_id){
		$query = DB::select('t_falcon_template_setting.*', 't_category_genre.category_genre_name')->from(self::$_table_name);
		$query->join('t_category_genre', 'LEFT')->on('t_category_genre.id', '=', 't_falcon_template_setting.category_genre_id');
		$query->where('t_falcon_template_setting.id', $template_id);
		return $query->execute()->current();

	}

	public static function upd_customformat($id, $value){
		DB::start_transaction('administrator');
		$query = DB::update(self::$_table_name);
		$query->value('custom_format_file_path',$value);
		$query->where('id', $id);
		$query->execute();
		DB::commit_transaction('administrator');

		return true;
	}

	public static function upd_campaignexclusionmemo($id, $value){
		DB::start_transaction('administrator');
		$query = DB::update(self::$_table_name);
		$query->value('campaign_exclusion_memo',$value);
		$query->where('id', $id);
		$query->execute();
		DB::commit_transaction('administrator');

		return true;
	}

	public static function setting_template($id, $values, $account_list, $sheet_list, $line_list, $aim_list=null){
		DB::start_transaction('administrator');
		if ($id) {
			$query = DB::update(self::$_table_name);
			$query->set($values);
			$query->where('id', $id);
			$query->execute();
		} else {
			$query = DB::insert(self::$_table_name, array_keys($values));
			$query->values(array_values($values));
			$id = $query->execute();
			if (!isset($id[0])) {
				DB::rollback_transaction('administrator');
				return false;
			}
			$id = $id[0];
		}
		\Model_Data_Falcon_AccountSetting::del_ins($id, $account_list);
		\Model_Data_Falcon_SheetSetting::del_ins($id, $sheet_list);
		\Model_Data_Falcon_LineSetting::del_ins($id, $line_list);
		if($aim_list){
			\Model_Data_Falcon_AimSetting::del_ins($id, $aim_list);
		}
		DB::commit_transaction('administrator');

		return $id;
	}

	public static function del_template($id){
		DB::start_transaction('administrator');
		$query = DB::delete(self::$_table_name);
		$query->where('id', $id);
		$query->execute();
		\Model_Data_Falcon_AccountSetting::del($id);
		\Model_Data_Falcon_SheetSetting::del($id);
		\Model_Data_Falcon_LineSetting::del($id);
		\Model_Data_Falcon_AimSetting::del($id);
		DB::commit_transaction('administrator');

		return true;
	}
}
