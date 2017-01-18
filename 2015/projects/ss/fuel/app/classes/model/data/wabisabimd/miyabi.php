<?php

class Model_Data_WabisabiMD_Miyabi extends \Model {

	// テーブル名定義
	protected static $_table_name = "t_miyabi";

	/*
	 * 取得 - 履歴用データ
	 */
	public static function get_by_history($miyabi_id, $attr_id) {

		$SQL = DB::select()
			 ->from(self::$_table_name)
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id);

		return $SQL->execute()->as_array();
	}

	/*
	 * 取得 - 入稿対象データ
	 *
	 * 【入稿対象】
	 * ・入札比率を変更した属性
	 * ・入札比率が未設定の属性（初期値0%で入稿）
	 * ・属性が不明かつ、0%のデータ
	 * 　（不明は未設定でも0%でAPIから返却される為、アプリ側で未設定と0%の区別が不可能。よって強制的に0%で入稿）
	 */
	public static function get_by_edit($miyabi_id, $attr_id) {

		$SQL = DB::select("account_id",
						  "adgroup_id",
						  "id",
						  "name",
						  array(DB::expr("case when old_bid_adj is null then 1"
										   . " when id = '" . CRITERIA_ID_AGE_UNKNOWN . "' and old_bid_adj = 1 and new_bid_adj = 1 then 1"
										   . " else new_bid_adj"
									  . " end"), "new_bid_adj"),
						  array(DB::expr("if(bid_adj_operator is null and old_bid_adj is null, 'ADD', bid_adj_operator)"), "bid_adj_operator"))
			 ->from(self::$_table_name)
			 ->where("miyabi_id", $miyabi_id)
			 ->where("bid_obj", MIYABI_BID_OBJ_ON)
			 ->where_open()
			 ->where("old_bid_adj", null)
			 ->or_where_open()
			 ->where("new_bid_adj", "!=", null)
			 ->where("new_bid_adj", "!=", DB::expr("old_bid_adj"))
			 ->where_close()
			 ->or_where_open()
			 ->where("id", CRITERIA_ID_AGE_UNKNOWN)
			 ->where("old_bid_adj", 1)
			 ->where("new_bid_adj", 1)
			 ->where_close()
			 ->where_close();
		if (isset($attr_id)) $SQL->where("attr_id", $attr_id);

		return $SQL->execute()->as_array();
	}

	/*
	 * 登録
	 */
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - 現在の入札額
	 */
	public static function upd_cpc($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("old_bid_cpc" => DB::expr("ifnull(truncate(old_adg_bid_cpc * old_bid_adj + 0.5, 0), old_adg_bid_cpc)")))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id);

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - フラグＡ
	 */
	public static function upd_cpc_flg_a($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_cpc" => DB::expr("truncate(old_bid_cpc * " . MIYABI_FLG_A_CPC_UP . " + 0.5, 0)"),
						 "bid_cpc_flg" => "Flg_A"))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("conv", "between", array(MIYABI_FLG_A_CV_MIN, MIYABI_FLG_A_CV_MAX))
			 ->where(DB::expr("cpa / adg_cpa"), "<", MIYABI_FLG_A_CPA_RATE);

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - フラグＢ
	 */
	public static function upd_cpc_flg_b($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_cpc" => DB::expr("truncate(old_bid_cpc / (cpa / adg_cpa) + 0.5, 0)"),
						 "bid_cpc_flg" => "Flg_B"))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("conv", ">=", MIYABI_FLG_B_CV_MIN)
			 ->where(DB::expr("cpa / adg_cpa"), "<", MIYABI_FLG_B_CPA_RATE);

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - フラグＣ
	 */
	public static function upd_cpc_flg_c($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_cpc" => DB::expr("truncate(old_bid_cpc / (cpa / adg_cpa) + 0.5, 0)"),
						 "bid_cpc_flg" => "Flg_C"))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("conv", ">", 0)
			 ->where(DB::expr("cpa / adg_cpa"), ">", MIYABI_FLG_C_CPA_RATE);

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - フラグＤ
	 */
	public static function upd_cpc_flg_d($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_cpc" => DB::expr("truncate(old_bid_cpc / (cpa / adg_cpa) + 0.5, 0)"),
						 "bid_cpc_flg" => "Flg_D"))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("conv", 0)
			 ->where(DB::expr("cpa / adg_cpa"), ">", MIYABI_FLG_D_CPA_RATE);

		return $SQL->execute();
	}

	/*
	 * 入札比率を更新
	 */
	public static function upd_adj($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_adj" => DB::expr("truncate(new_bid_cpc / old_adg_bid_cpc + 0.005, 2)"),
						 "bid_adj_operator" => DB::expr("if(old_bid_adj is null, 'ADD', 'SET')"),
						 "bid_adj_point" => DB::expr("truncate(new_bid_cpc / old_adg_bid_cpc + 0.005, 2) - ifnull(old_bid_adj, 1)")))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("old_adg_bid_cpc", "!=", null)
			 ->where("new_bid_cpc", "!=", null);

		return $SQL->execute();
	}

	/*
	 * 入札比率を調整 - 変動ポイントによる調整
	 */
	public static function upd_pb_control($miyabi_id, $attr_id, $pb_min, $pb_max) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_adj" => DB::expr("if(bid_adj_point < " . $pb_min . ", new_bid_adj - (bid_adj_point - " . $pb_min . "), new_bid_adj - (bid_adj_point - " . $pb_max . "))")))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("bid_adj_point", "!=", null)
			 ->where("bid_adj_point", "not between", array($pb_min, $pb_max));

		return $SQL->execute();
	}

	/*
	 * 入札比率を調整 - 指定した比率による調整
	 */
	public static function upd_adj_control($miyabi_id, $attr_id, $adj_min, $adj_max) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_adj" => DB::expr("if(new_bid_adj < " . $adj_min . ", " . $adj_min . ", " . $adj_max . ")")))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("new_bid_adj", "!=", null)
			 ->where("new_bid_adj", "not between", array($adj_min, $adj_max));

		return $SQL->execute();
	}

	/*
	 * 入札額を更新 - 入札比率調整を反映
	 */
	public static function upd_new_bid_cpc($miyabi_id, $attr_id) {

		$SQL = DB::update(self::$_table_name)
			 ->set(array("new_bid_cpc" => DB::expr("truncate(old_bid_cpc * new_bid_adj + 0.5, 0)")))
			 ->where("miyabi_id", $miyabi_id)
			 ->where("attr_id", $attr_id)
			 ->where("new_bid_adj", "!=", null);

		return $SQL->execute();
	}

	/*
	 * 設定ごとに削除
	 */
	public static function del_by_miyabi_id($miyabi_id, $attr_id = null) {

		$SQL = DB::delete(self::$_table_name)
			 ->where("miyabi_id", $miyabi_id);
		if (isset($attr_id)) $SQL->where("attr_id", $attr_id);

		return $SQL->execute();
	}

	/*
	 * 全設定を削除
	 */
	public static function del_all() {

		return DBUtil::truncate_table(self::$_table_name, "administrator");
	}

	/*
	 * テーブル再構成
	 */
	public static function alter_table() {

		$SQL = "alter table " . self::$_table_name . " engine InnoDB";

		return DB::query($SQL)->execute("administrator");
	}
}
