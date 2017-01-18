<tr>
	<?
	$alert_flg_val = '';
	if ($alert_flg) {
		$alert_flg_val = 'alert_';
		?>

		<? if ($account['stop_flg'] == '1') { ?>
			<td class="text-center">
				<li class="label label-info">停止済</li>
			</td>
		<? } else { ?>
			<td class="text-center">
				<li class="label
					<?
					if ($account['remainder_day'] > 3) {
						echo 'label-info';
					} elseif ($account['remainder_day'] > 0) {
						echo 'label-warning';
					} else {
						echo 'label-danger';
					}
					?>">
					<?=$account['remainder_day'] ?>日
				</li>
			</td>
		<? } ?>
	<? } ?>

	<td class="text-center"><?=$account['media_name'] ?></td>
	<td class="text-center"><?=$account['id'] ?></td>
	<td class="table-type-name"><?=$account['account_name'] ?></td>
	<td class="text-center"><?=$account['budget_type_name'] ?></td>

	<td class="media-budget text-right">
		<?if ($account['media_id'] == MEDIA_ID_GOOGLE) {
			echo '---';
		} else { ?>
			¥<?= number_format($account['account_budget']) ?>
		<? } ?>
	</td>
	<td class="media-budget-btn text-center">
		<?if ($account['media_id'] == MEDIA_ID_GOOGLE) {
			echo '---';
		} else { ?>
			<button type="button" class="btn btn-xs btn-default js-mod-budget" data-item-id="<?=$account['id']?>">
				変更
			</button>
		<? } ?>
	</td>

	<td id="limit_budget_<?=$alert_flg_val?><?=$account['id']?>" class="text-right">
		¥<?=number_format($account['limit_budget']) ?>
	</td>

	<td class="text-center">
		<?
		if ($account['total_cost']) { ?>
			¥<?= number_format($account['total_cost']) ?>
		<? } else {
			echo '実績なし';
		}
		?>
	</td>

	<td class="text-center">
		<?
		if ($account['prediction_cost']) {
			if ($account['prediction_cost'] > $account['limit_budget']) {
				echo "<font color='red'>¥".number_format($account['prediction_cost'])."</font>";
			} else {
				echo "¥".number_format($account['prediction_cost']);
			}
		} else {
			echo '実績なし';
		}
		?>
	</td>

	<td class="mod-budget text-center">
		<div class="append input-group">
			<input class="form-control input-sm" id="limit_<?=$alert_flg_val?><?=$account['id']?>" type="text" placeholder="<?=number_format($account['limit_budget']) ?>" />
			<span class="input-group-btn">
				<button type="button" class="btn btn-sm btn-primary" id="mod_btn" onclick="editLimit('<?=$account['id']?>','<?=$alert_flg_val?>')">
					変更
				</button>
			</span>
		</div>
	</td>

	<td id="stop_account_<?=$alert_flg_val?><?=$account['id']?>" class="text-center">
		<? if ($account['stop_flg'] == '1') { ?>
			<button type="button" class="btn btn-xs btn-default" id="search_btn" onclick="startAccount('<?=$account['id']?>')">
				停止解除
			</button>
		<? } else { ?>
			<div class="no_btn"></div>
		<? } ?>
	</td>
</tr>
