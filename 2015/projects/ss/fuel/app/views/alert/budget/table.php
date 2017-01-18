<? if ($accounts || !$alert_flg) {?>
	<div class="sub-content">
		<? if($alert_flg) { ?>
			<div class="danger label budget-title">アラート一覧</div>
		<? } else { ?>
			<div class="info label budget-title">アカウント一覧</div>
		<? } ?>
		<? if ($total_count) { ?>
			<div style="display: inline-block;">アカウント総件数：<?=$total_count?>件</div>
		<? } ?>

		<table class="report-table table striped">
			<tr class="table-label">
				<td>媒体</td>
				<td>アカウントID</td>
				<td>アカウント名</td>
				<td>予算タイプ</td>
				<td>アカウント予算</td>
				<td>予算リミット</td>
				<td>当月実績値</td>
				<td>当月着予</td>
				<td>予算リミット変更</td>
				<td>再開</td>
			</tr>
		</table>

		<? if ($total_count) {echo Pagination::create_links();} ?>
	</div>
<? } ?>
