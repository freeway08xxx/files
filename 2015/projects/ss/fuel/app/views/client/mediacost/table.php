<form id="mediacost_list">

	<div class="media-list set-list">
		<? if (empty($target_media_list)) { ?>
			<div class="no-registerd">媒体別の設定はありません。</div>
		<? } else { ?>

			<h5>媒体別設定一覧 <span class="pull-right"> <?= count($target_media_list) ?> 件</span></h5>

			<div class="panel panel-default">
				<table class="table table-striped table-hover table-condensed" id="target_media_list">
					<tr>
						<th class="media-name nowrap">媒体名</th>
						<th class="nowrap">媒体費（%）</th>
						<th class="nowrap">更新者</th>
						<th class="">更新日時</th>
						<th class="delete-check">
							<label>
								<input type="checkbox"
								 ng-click="mediacost.methods.checkAll('media')"
								 ng-model="mediacost.models.is_checked_alldelete.media"
								value="1"> 削除
							</label>
						</th>
					</tr>
					<?foreach ($target_media_list as $key => $value){?>
						<tr class="targetmedia_media_list">
							<td class="media-name nowrap"><?=FalconConst::$falcon_target_media_list[$value['media_id']]?></td>
							<td class="nowrap">
								<div class="input-group input-group-sm media-cost-input-group">
									<input type="number" class="form-control" size="5"
									 min="0" max="100" required
									 ng-model="mediacost.models.target_media_list[<?=$value['id'] ?>]"
									 ng-init="mediacost.models.target_media_list[<?=$value['id'] ?>] = <?=$value['media_cost']?>">
									<span class="input-group-addon">%</span>
								</div>
							</td>
							<td class="nowrap"><?=$value['user_name']?></td>
							<td class="datetime"><?=$value['datetime']?></td>
							<td class="delete-check">
								<label>
									<input type="checkbox" ng-model="mediacost.models.delete_target.media.<?=$value['id'] ?>" value="<?=$value['id'] ?>">
								</label>
							</td>
						</tr>
					<? } ?>
				</table>

				<div class="btn-area">
					<button type="button" class="btn btn-default btn-xs" ng-click="mediacost.methods.update('media')">
						設定内容を更新
					</button>　
					<button type="button" class="btn btn-danger btn-xs" ng-click="mediacost.methods.deleteSet('media')">
						チェックした項目を削除
					</button>
				</div>

			</div>
		<? } ?>
	</div>

	<div class="account-list set-list">
		<? if (empty($target_account_list)) { ?>
			<div class="no-registerd">アカウント別の設定はありません。</div>
		<? } else { ?>

			<h5>アカウント別設定一覧 <span class="pull-right"> <?= count($target_account_list) ?> 件</span></h5>

			<div class="panel panel-default">
				<table class="table table-striped table-hover table-condensed" id="target_account_list">
					<tr class="table-label">
						<th class="nowrap">媒体名</th>
						<th class="">アカウント</th>
						<th class="nowrap">媒体費</th>
						<th class="nowrap">更新者</th>
						<th class="">更新日時</th>
						<th class="delete-check">
							<label>
								<input type="checkbox"
								 ng-click="mediacost.methods.checkAll('account')"
								 ng-model="mediacost.models.is_checked_alldelete.account"
								 value="1"> 削除
							</label>
						</th>
					</tr>

					<? foreach ($target_account_list as $key => $value) { ?>
						<tr class="targetmedia_account_list">
							<td class="nowrap"><?=falconconst::$falcon_target_media_list[$value['media_id']]?></td>
							<td class="">
								<? foreach ($account_info_list as $account => $account_val){ ?>
									<?if($account_val["account_id"]==$value['account_id']){?>
										<?="[".falconconst::$falcon_target_media_list[$account_val["media_id"]].":".$account_val["account_id"]."]".$account_val["account_name"]?>
									<? } ?>
								<? } ?>
							</td>
							<td class="nowrap">
								<div class="input-group input-group-sm media-cost-input-group">
									<input type="number" class="form-control" size="5"
									 min="0" max="100" required
									 ng-model="mediacost.models.target_account_list[<?=$value['id'] ?>]"
									 ng-init="mediacost.models.target_account_list[<?=$value['id'] ?>] = <?=$value['media_cost']?>">
									<span class="input-group-addon">%</span>
								</div>
							</td>

							<td class="nowrap"><?=$value['user_name']?></td>
							<td class="datetime"><?=$value['datetime']?></td>
							<td class="delete-check">
								<label>
									<input type="checkbox" ng-model="mediacost.models.delete_target.account.<?=$value['id'] ?>" value="<?=$value['id'] ?>">
								</label>
							</td>
						</tr>
					<? } ?>
				</table>

				<div class="btn-area">
					<button type="button" class="btn btn-default btn-xs" ng-click="mediacost.methods.update('account')">
						設定内容を更新
					</button>　
					<button type="button" class="btn btn-danger btn-xs" ng-click="mediacost.methods.deleteSet('account')">
						チェックした項目を削除
					</button>
				</div>
			</div>
		<? } ?>
	</div>

</form>