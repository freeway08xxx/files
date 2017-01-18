<table class="media-budget-table table table-condensed table-bordered table-striped">
	<tr>
		<th class="">アカウント名</th>
		<td class=""><?= $account['account_name'] ?></td>
	</tr>
	<tr>
		<th class="">アカウントID</th>
		<td class=""><?= $account['account_id'] ?></td>
	</tr>
	<tr>
		<th class="">予算タイプ</th>
		<td class="">
		<div class="row">
			<div class="col-xs-6">
				<select class="form-control input-sm" id="after_budget_type">
					<option value="1"<? if ($account['budget_type_id'] == '1') echo ' selected'; ?>>月額</option>
					<option value="2"<? if ($account['budget_type_id'] == '2') echo ' selected'; ?>>総額</option>
				</select>
				<input type="hidden" id="before_budget_type" value="<?=$account['budget_type_id']?>">
			</div>
		</div>
		</td>
	</tr>
	<tr>
		<th class="">媒体予算</th>
		<td class="">
			<div class="row">
				<div class="col-xs-7">
					<input type="text" class="form-control input-sm" id="after_budget" value="<?=$account['account_budget']?>">
					<input type="hidden" id="before_budget" value="<?=$account['account_budget']?>">
				</div>
			</div>
		</td>
	</tr>
</table>


<input type="hidden" name="item_id" id="item_id" value="<?=$account['account_id']?>" placeholder="">
