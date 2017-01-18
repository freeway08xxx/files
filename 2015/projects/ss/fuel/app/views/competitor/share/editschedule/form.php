<? if (count($client_list) > 0 || count($industry_list) > 0) { ?>
	<div class="list-group">
		<div class="list-group-item">
			<legend>設定対象</legend>

			<ul class="list-inline">
				<li class="">
					<div class="picker">
						<select id="parent" class="form-control client_select" name="type">
							<? if (count($client_list) > 0) { ?>
								<option value="1"<?
								if ($type == 'clnt') echo ' selected';
								?> class="client">クライアント</option>
							<? } ?>
							<? if (count($industry_list) > 0) { ?>
								<option value="2"<?
								if ($type == 'idst') echo ' selected';
								?> class="dist">業種</option>
							<? } ?>
						</select>
					</div>
				</li>
				<li>
					<div class="picker child client" id="child_client">
						<select class="form-control" name="target">
							<? foreach($client_list as $item){ ?>
								<option class="c_1" value="<?=$item['id']?>"<?
								if ($client_id == $item['id']) echo ' selected';
								?>><?=$item['name']?></option>
							<? } ?>
						</select>
					</div>
				</li>
				<li>
					<div class="picker child dist" id="child_dist">
						<select class="form-control" name="target">
							<? foreach($industry_list as $item){ ?>
								<option class="c_2" value="<?=$item['id']?>"<?
								if ($industry_id == $item['id']) echo ' selected';
								?>><?=$item['name']?></option>
							<? } ?>
						</select>
					</div>
				</li>
			</ul>

			<div class="btn-area">
				<a href="#" class="btn btn-sm btn-info" id="show_btn">設定内容を表示</a>
			</div>

		</div>
	</div>

<? } else { ?>
	<div>設定可能なクライアントが存在しません。</div>
<? } ?>
