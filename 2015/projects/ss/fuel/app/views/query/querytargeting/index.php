<div class="content row legacy">
	<div class="col-md-8">
		<form name="form01" method="post" action="#">

			<h6>業界名</h6>
			<input type="text" class="form-control category-name" id="category_name" name="category_name" value="<?=$category_value?>" placeholder="業界名を入力してください" tabindex="1">

			<h6>登録するキーワード</h6>
			<textarea class="form-control keyword-input" id="keywords_name" name="keywords_name" tabindex="2"
				placeholder="複数キーワード登録は、改行で単語区切ってください"><?=$keywords_value?></textarea>

			<div class="btn-area">
				<button type="button" class="btn btn-sm btn-primary" id="button" onclick="doActionMI()" tabindex="3">
					新規登録
				</button>
			</div>

		</form>
	</div>
</div>