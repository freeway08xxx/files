<div class="form-block list-group">
	<div class="list-group-item">

		<div class="header-area clearfix">
			<h4>
				主要キーワード設定
				<small class="pull-right">
					レポートの主要KW推移シートに出力するキーワードを設定します
				</small>
			</h4>
		</div>

		<div class="kw-table-wrap">
			<table class="table table-condensed table-striped kw-table">
				<tr>
					<th class="number"></th>
					<th>キーワード</th>
					<th class="match-type">マッチタイプ</th>
				</tr>

				<? for ($i = 0; $i < FALCON_KW_MAX_ENTRY; $i++) { ?>
					<tr>
						<td class="text-center number">
							<span class="label label-default"><?= $i + 1 ?></span>
						</td>
						<td>
							<input type="text" class="form-control input-sm"
							 ng-model="kw.models.list[<?= $i ?>].keyword" />
						</td>
						<td>
							<select class="form-control input-sm"
							ng-model="kw.models.list[<?= $i ?>].match_type">
								<? foreach (FalconConst::$matchtype_list as $matchtype_en => $matchtype_ja) { ?>
									<option value="<?= $matchtype_en ?>"><?= $matchtype_ja ?></option>
								<? } ?>
							</select>
						</td>
					</tr>
				<? } ?>
			</table>
		</div>

		<div class="btn-area ">
			<button type="button" class="btn btn-info btn-sm"
			ng-click="kw.save()">
				上記内容で更新する
			</button>
		</div>

		<div class="msg-area" ng-show="kw.models.msg">
			<div class="alert alert-danger">
				{{kw.models.msg}}
			</div>
		</div>
	</div>
</div>
