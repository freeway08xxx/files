<div class="content row legacy">
	<div class="col-md-8">
		<form name="form01" method="post" action="#">

			<h6>業界名</h6>
			<select name="category_name" class="form-control input-sm category-name" id="category_name" tabindex="1">
				<?
					if ($category_list) {
						foreach ($category_list as $key => $item) {
							if ($category_value && $category_value === $item["category_name"]) {
								echo '<option value="'.$item["category_name"].'" selected>'.$item["category_name"].'</option>';
							} else {
								echo $category_value.' && '.$category_value.' === '.$item["category_name"];
								echo '<option value="'.$item["category_name"].'">'.$item["category_name"].'</option>';
							}
						}
					}
				?>
			</select>

			<button type="button" class="btn btn-sm btn-primary" id="button" onclick="doAction()" tabindex="3">
				実行
			</button>

		</form>
	</div>
</div>

<? if ($keyword_list) { ?>
	<div class="keyword-list" id="keyword_map">
		<span class="label label-info"><?=$keyword?></span> キーワード一覧

		<table class="keyword-table table table-bordered table-hover table-striped table-condensed">
			<? foreach ($keyword_list as $key => $item) { ?>
				<? if (($key+1) % 5 === 1) { ?>
					<tr>
				<? } ?>
				<td class="">
				<? if ($item['delete_flg'] !== '0') { ?>
					<span class="label label-danger">停止中</span>
				<? } ?>
					<?=$item['keyword']?>
				</td>
				<? if (($key+1) % 5 === 0) { ?>
					</tr>
				<? } ?>
			<? } ?>
			</tr>
		</table>
	</div>
<? } ?>
