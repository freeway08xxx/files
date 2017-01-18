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
	<div>
		<section class="tabs">
			<ul class="tab-nav">
				<li class="tab-top active"><a href="#top">　　　　TOP　　　　</a></li>
				<li class="tab-history"><a href="#history">　　　作業履歴　　　</a></li>
			</ul>
			<div class="tab-content top active">
				<div class="set_top"><?=$top?></div>
				<div class="set_table"><?=$table?></div>
			</div>
			<div class="tab-content history">
				<div class="set_history"><?=$history?></div>
			</div>
		</section>
	</div>
<?}?>
<input type="hidden" name="client_id" />