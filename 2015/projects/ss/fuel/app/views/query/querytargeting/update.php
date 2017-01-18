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

			<h6>登録するキーワード</h6>
			<textarea class="form-control keyword-input" id="keywords_name" name="keywords_name" tabindex="2"
				placeholder="複数キーワード登録は、改行で単語区切ってください"><?=$keywords_value?></textarea>

			<div class="btn-area keyword-btn-area">
				<button type="button" class="btn btn-sm btn-primary" id="button" onclick="doActionMI()" tabindex="3">
					キーワード追加登録
				</button>

				<button type="button" class="btn btn-sm btn-danger" id="button" onclick="doActionMD()" tabindex="4">
					業界カテゴリー削除
				</button>
			</div>

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
