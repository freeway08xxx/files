<div class=" legacy">
	<div class="clearfix select-client">
		<fieldset class="columns">
			<legend>クライアント選択</legend>
			<select name="client_id" id="client_id">
				<option value="">--</option>
				<? foreach($clients as $client) { ?>
					<option value="<?=$client['id'] ?>"<?
						if ($client_id == $client['id']) echo ' selected';
						?>><?=$client['company_name'] ?><? if($client['client_name']) echo "//".$client['client_name']; ?>
					</option>
				<? } ?>
			</select>
		</fieldset>
	</div>

	<? if ($client_id) { ?>
		<div class="filter well well-sm">

			<ul class="list-inline">
				<li>
					<h6>絞り込み検索</h6>
				</li>
				<li>
					<span class="form-inline">媒体</span>
					<select name="client_id" id="media_search" class="form-control input-sm form-inline">
						<option value="">--</option>
						<option value="1">Yahoo</option>
						<option value="2">Google</option>
						<option value="3">YDN</option>
					</select>
				</li>
				<li>
					<input class="form-control input-sm" id="accountid_search" type="text" placeholder="アカウントID" />
				</li>
				<li>
					<input class="form-control input-sm" id="accountname_search" type="text" placeholder="アカウント名" />
				</li>
				<li>
					<div class="checkbox">
						<label class="" for="stop_search" id="for_stop_search">
							<input name="stop_search" id="stop_search" value="1" type="checkbox">
							強制停止
						</label>
					</div>
				</li>
				<li>
					<div class="checkbox">
						<label class="" for="stoplimit_search" id="for_stoplimit_search">
							<input name="stoplimit_search" id="stoplimit_search" value="1" type="checkbox">
							停止3日前
						</label>
					</div>
				</li>
			</ul>
		</div>

		<div class="sub-content alert-list">
			<h5 class="label label-warning">アラート一覧</h5>

			<table class="report-table table table-striped table-condensed table-hover table-bordered" id="alert_table">
				<tr class="table-label">
					<td class="text-center" tooltip-placement="right" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_LIMITDAY ?>">残日数</td>
					<td class="text-center">媒体</td>
					<td class="text-center">アカウントID</td>
					<td class="">アカウント名</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_BUDGETTYPE ?>">予算タイプ</td>
					<td colspan="2" class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_MEDIABUDGET ?>">媒体予算</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_BUDGETLIMIT ?>">予算リミット</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_TOTALCOST ?>">当月実績値</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_PREDICTCOST ?>">当月着予</td>
					<td class="" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_MOD ?>">予算リミット変更</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_REPLAY ?>">再開</td>
				</tr>
			</table>
		</div>

		<div class="sub-content">
			<div class="clearfix">
				<h5 class="label label-default right_10px">アカウント一覧</h5>

				<div class="checkbox budget-search form-inline">
					<label for="budget_search" id="for_budget_search">
						<input name="budget_search" id="budget_search" value="1" type="checkbox">
						実績値ありのみ表示
					</label>
				</div>
			</div>

			<table class="report-table table table-striped table-condensed table-hover table-bordered" id="account_table">
				<tr class="table-label">
					<td class="text-center">媒体</td>
					<td class="text-center">アカウントID</td>
					<td class="">アカウント名</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_BUDGETTYPE ?>">予算タイプ</td>
					<td colspan="2" class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_MEDIABUDGET ?>">媒体予算</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_BUDGETLIMIT ?>">予算リミット</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_TOTALCOST ?>">当月実績値</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_PREDICTCOST ?>">当月着予</td>
					<td class="" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_MOD ?>">予算リミット変更</td>
					<td class="text-center" tooltip-append-to-body="true" tooltip-html-unsafe="<?= BUDGET_EXPLAIN_REPLAY ?>">再開</td>
				</tr>
			</table>
		</div>

		<div id="paging"></div>

	<? } ?>

	<form name="form01" method="post" action="/alert/budget/upd/<?=$client_id?>?page=<?=$current_page?>">
		<input type="hidden" name="account_id" value="">
		<input type="hidden" name="limit" value="">
	</form>

	<!-- Modal Window -->
	<div class="modal fade" id="mediabudget" tabindex="-1" role="dialog" aria-labelledby="mediabudgetlabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">媒体予算変更依頼フォーム</h4>
				</div>
				<div class="modal-body" id="popup_base">
					<p>form body...</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-primary budget-send js-send-budget">
						変更内容を送信する
					</button>

					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
						キャンセル
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

</div>