<div id="loading">
	<img src="/sem/new/assets/img/loading.gif" >
</div>
<div class="clearfix">
	<form>
		<fieldset class="columns">
			<legend>クライアント選択</legend>
			<select name="client_id" id="client_id" class="client_select float">
			<option value="">--</option>
			<? foreach($clients as $client) { ?>
			<option value="<?=$client['id'] ?>"<?
				if ($client_id == $client['id']) echo ' selected';
			?>><?=$client['company_name'] ?><? if($client['client_name']) echo "//".$client['client_name']; ?></option>
			<? } ?>
			</select>
		</fieldset>
	</form>
</div>
<?if($client_id){?>
	<div class="medium primary btn"><a href="javascript:modGenre();">＋新規カテゴリジャンル登録</a></div>
	<div class="set_table"><?=$table?></div>
<?}?>
<input type="hidden" name="client_id" />