<?php
class Model_Data_WabiSabiH_Setting extends \Model {

	## テーブル名定義
	protected static $_table_name = 't_wabisabih_setting';

	############################################################################
	## 設定情報取得
	############################################################################
	public static function get($wabisabi_id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('readonly')->current();
	}

	############################################################################
	## 設定情報更新
	############################################################################
	public static function upd($wabisabi_id, $values) {

		$SQL = DB::update(self::$_table_name);
		$SQL->set($values);
		$SQL->where("wabisabi_id", $wabisabi_id);

		return $SQL->execute("administrator");
	}

	############################################################################
	## クライアント毎の設定情報取得
	############################################################################
	public static function get_by_client_id($client_id) {

		$user_table = 'mora.user';

		$SQL = DB::select(
					self::$_table_name.'.wabisabi_id',
					self::$_table_name.'.wabisabi_name',
					self::$_table_name.'.status',
					self::$_table_name.'.created_at',
					array('user_1.user_name', 'created_user'),
					self::$_table_name.'.updated_at',
					array('user_2.user_name', 'updated_user'));
		$SQL->from(self::$_table_name);
		$SQL->join(array($user_table, 'user_1'), 'INNER')
			->on(self::$_table_name.'.created_user', '=', 'user_1.id');
		$SQL->join(array($user_table, 'user_2'), 'LEFT')
			->on(self::$_table_name.'.updated_user', '=', 'user_2.id');
		$SQL->where('client_id', $client_id);

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 処理対象一覧取得
	############################################################################
	public static function get_target_list($target_cv = null, $target_hour = null) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where('status', DB_STATUS_ON);

		## 媒体
		if ($target_cv === TARGET_CV_MEDIA) {
			$all_ext_cv_list = \Model_Data_WabiSabiH_SettingCvName::get_target_list();
			if (!empty($all_ext_cv_list)) {
				$SQL->where('wabisabi_id', 'not in', $all_ext_cv_list);
			}
		## CAMP
		} elseif ($target_cv === TARGET_CV_CAMP) {
			$camp_list = \Model_Data_WabiSabiH_SettingCvName::get_target_list($target_cv);
			if (empty($camp_list)) {
				return array();
			} else {
				$SQL->where('wabisabi_id', 'in', $camp_list);
			}
		## CAMP以外の外部CV
		} elseif ($target_cv === TARGET_CV_EXTCV) {
			$ext_cv_list = \Model_Data_WabiSabiH_SettingCvName::get_target_list($target_cv, $target_hour);
			if (empty($ext_cv_list)) {
				return array();
			} else {
				$SQL->where('wabisabi_id', 'in', $ext_cv_list);
			}
		}

		return $SQL->execute('readonly')->as_array();
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($value) {

		$SQL = DB::insert(self::$_table_name, array_keys($value));
		$SQL->values(array_values($value));

		return $SQL->execute('administrator');
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($wabisabi_id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where('wabisabi_id', $wabisabi_id);

		return $SQL->execute('administrator');
	}

	############################################################################
	## 設定削除
	############################################################################
	public static function del_setting($wabisabi_id) {

		DB::start_transaction("administrator");

		\Model_Data_WabiSabiH_SettingAccount::del($wabisabi_id);
		\Model_Data_WabiSabiH_SettingCvName::del($wabisabi_id);
		\Model_Data_WabiSabiH_SettingFilter::del($wabisabi_id);
		\Model_Data_WabiSabiH_Setting::del($wabisabi_id);

		DB::commit_transaction("administrator");

		return true;
	}
}
