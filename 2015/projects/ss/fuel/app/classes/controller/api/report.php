<?php

require_once APPPATH."/const/common/report.php";
require_once APPPATH."/const/falcon.php";

class Controller_Api_Report extends Controller_Api_Base
{
	public function before() {
		parent::before();
	}

	public function after($response) {
		return parent::after($response);
	}

	/**
	 * レポート用定数を取得
	 *
	 * @param
	 * @access public
	 */
	public function get_config() {
		global
		$report_type_list, $report_term_list, $report_elem_list, $report_elem_type_list,
		$report_elem_order_list, $report_formula_cell_type_list,
 		$report_category_type_list, $report_category_element_list, $report_device_line_list,
 		$report_export_status_list;

 		// falcon
 		global $report_sheet_list, $report_format, $report_aim_list;

		switch (Input::param('type')) {
			case 'termdate':
				$const_arr = [
					"report_type" => $report_type_list,
					"report_term" => $report_term_list,
					"term_limit"   => REPORT_TERM_COUNT_MAX
				];

				break;
			case 'falcon':
				// Delete Non-Need Const
				unset($report_type_list['summary']);

				// JS用にソート
				$report_type = array();
				foreach ($report_type_list as $key => $name) {
					$report_type[$key]["key"] = $key;
					$report_type[$key]["name"] = $name;
				}

				$report_elem = array();
				foreach ($report_elem_order_list as $i => $keyname) {
					$report_elem[$i] = $report_elem_list[$keyname];
				}

				$report_device_line       = self::_getJsArr($report_device_line_list);
				$report_formula_cell_type = self::_getJsArr($report_formula_cell_type_list);

				$report_sheet = array();
				foreach($report_sheet_list as $summary => $chunk) {
					foreach ($chunk as $type => $list) {
						$report_sheet[$summary][$type] = self::_getJsArr($list);
					}
				}

				$const_arr = [
					"report_type"              => $report_type,
					"report_category_type"     => $report_category_type_list,
					"report_category_element"  => $report_category_element_list,
					"report_elem"              => $report_elem,
					"report_elem_type"         => $report_elem_type_list,
					"report_device_line"       => $report_device_line,
					"report_formula_cell_type" => $report_formula_cell_type,
					"report_sheet"             => $report_sheet,
					"report_format"            => $report_format,
					"report_aim"               => $report_aim_list,
					"report_ad_type"           => FalconConst::$falcon_ad_type_list,
					"campaign_exclude_option"  => FalconConst::$campaign_exclude_option_list,
					"campaign_device_type"     => FalconConst::$falcon_device_list,
					"campaign_status_label"    => FalconConst::$falcon_status_label_list,
					"report_term"              => $report_term_list,
					"report_export_status"     => $report_export_status_list
				];

				break;
			default:
				$const_arr = [];
				break;
		}

		return $this->response($const_arr);
	}

	private static function _getJsArr($list) {
		$arr = array();
		$i = 0;

		foreach ($list as $key => $name) {
			$arr[$i]["key"] = $key;
			if (is_array($name)) {
				foreach ($name as $skey => $value) {
					$arr[$i][$skey] = $value;
				}
			} else {
				$arr[$i]["name"] = $name;
			}
			$i++;
		}

		return $arr;
	}
}
