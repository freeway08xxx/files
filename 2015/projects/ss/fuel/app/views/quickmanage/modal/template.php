<form>
	<fieldset class="columns">
		<legend>テンプレート情報詳細</legend>
		<div class="clearfix">
			<table>
				<tr>
					<td class="table-type-ex1">テンプレート名</td>
					<td class="table-type-ex2"><?= $DB_template_info["template_name"]; ?></td>
				</tr>
				<tr>
					<td class="table-type-ex1">レポート種別</td>
					<td class="table-type-ex2"><?= $GLOBALS["report_type_list"][$DB_template_info["report_type"]]; ?></td>
				</tr>
				<tr>
					<td class="table-type-ex1">サマリ種別</td>
					<td class="table-type-ex2"><?= $GLOBALS["summary_type_list"][$DB_template_info["summary_type"]]; ?></td>
				</tr>
				<tr>
					<td class="table-type-ex1">集計期間</td>
					<td class="table-type-ex2"><? if (isset($GLOBALS["report_term_list"][$DB_template_info["report_type"]][$DB_template_info["report_term"]])) { echo $GLOBALS["report_term_list"][$DB_template_info["report_type"]][$DB_template_info["report_term"]]; } ?></td>
				</tr>
			</table>
		</div>
		<div class="medium primary btn btn-edit"><a href="javascript:editForm('<?= $DB_template_info["id"]; ?>');">テンプレート編集</a></div>
	</fieldset>
</form>
