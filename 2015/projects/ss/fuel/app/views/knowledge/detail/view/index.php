<div id="knowledge-main-content" class="row tab-content" style="display: none">
<div class="tab-pane active">

	<div class="detail" ng-controller="fileEditCtr" ng-init="init(<?= Input::param("file_id") ?>)">
		<!-- 以下エラーメッセージの表示 -->
		<?
		if(Session::get_flash("errors")){
			foreach (Session::get_flash("errors") as $error) {
		?>
			<div class="error-msg alert alert-danger"><?= $error ?></div>
		<?
			}
		}
		?>


		<div class="file-title-block row">
			<div class="col-xs-6">
				<h4 class="title"><span class="glyphicon glyphicon-file"></span>{{model.file.file.title}} [ {{model.file.file.filename}} ]</h4>
			</div>
			<div class="col-xs-6 text-right">
			<!--<button type="button" class="btn btn-link">一覧へ戻る</button> --><!-- //Input::referrer(); で取れるがリダイレクトかかると駄目になる -->


				<a ng-if="(model.file.is_file_exists && model.file.section_authority_level >=<?= SECTION_AUTHORITY_LEVEL_VIEW ?>)" href="/sem/new/knowledge/detail/download?file_id={{model.file.file.id}}" download="{{model.file.file.filename}}"> 
					<button type="button" class="btn btn-info"><span class="glyphicon glyphicon-download-alt"></span> ダウンロード</button>
				</a>
				<a ng-if=model.file.section_authority_level==<?= SECTION_AUTHORITY_LEVEL_ADMIN ?> href="/sem/new/knowledge#/detail/edit?file_id={{model.file.file.id}}#type_section={{typeSection}}" >	
					<button type="submit" class="btn btn-warning">編集する</button>
				</a>
				<a ng-if=model.file.section_authority_level==<?= SECTION_AUTHORITY_LEVEL_ADMIN ?> ng-click="actionDelete()">	
					<button type="submit" class="btn btn-danger">削除する</button>
				</a>
			</div>
		</div>


		<div class="description-block">

			<div class="description panel panel-default">
				<div class="panel-heading">詳細</div>
				<div class="panel-body" ng-bind-html="model.file.file.description | noHTML | newlines"></div>
			</div>

			<table class="detail-info-table table table-bordered table-condensed">
				<tr>
					<th>用途</th>
					<td colspan="3" class="category-info">
								<span class="label label-info" ng-repeat="(key,value) in model.file.purpose_all | filter:{selected:true}:true" style="margin:5px;display: inline-block;">
									<purpose-bread-crumb purpose-id=value.id purpose-all=model.file.purpose_all></purpose-bread-crumb>
								</span>
					</td>
				</tr>
				<tr>
					<th>検索用タグ</th>
					<td colspan="3" class="tag">
						<span class="seach-tag label label-default" ng-repeat="(key,value) in model.file.file.freetext_for_search">{{value}}</span>
					</td>
				</tr>
				<tr>
					<th>権限</th>
					<td colspan="3" class="role">
						<span class="seach-tag label label-warning" ng-repeat="role in model.file.role_all | filter:{selected:true}:true">{{role.role_name}}</span>
					</td>
				</tr>
				<tr>
					<th>登録者</th>
					<td>{{model.file.file_entry_user.user_name}}</td>
					<th>公開範囲</th>
					<td>
						{{model.file.file.search_active_flg == 1 ? '公開' : '非公開'}}
					</td>
				</tr>
				<tr>
					<th>ファイル更新日</th>
					<td>{{model.file.file.file_update_date}}</td>
					<th>登録日</th>
					<td>{{model.file.file.insert_datetime}}</td>
				</tr>
			</table>
		</div>


		<div class="comment-block">
			<h5 class="title">コメント</h5>


			<div class="comment-list list-group">

				<div class="comment-item list-group-item clearfix" ng-repeat="(key,value) in model.file.file_comment_history">
					<div>{{key + 1}}</div>
					<div class="icon-col">
						<span class="glyphicon glyphicon-comment"></span>
					</div>
					<div class="comment-col" ng-bind-html="value.comment | noHTML | newlines"></div>
					<div class="info-col">
						<p>{{value.insert_datetime}}</p>
						<p>{{value.user_name}}</p>
					</div>
				</div>

				<div class="comment-edit comment-item list-group-item form-group">
					<h6 class="title">コメントを投稿する</h6>
					<? echo Form::open("knowledge/detail/update/comment"); ?>

					<textarea name="comment" class="form-control"></textarea>

					<? echo Form::hidden('file_id',Input::param("file_id")); ?>
					<input type="hidden" name="type_section" value="{{typeSection}}">
					<button type="submit" class="btn-submit btn btn-success btn-sm">投稿する</button>

					<? echo Form::close(); ?>
				</div>
			</div>
		</div>

		<div class="history-block row">

			<div class="col-xs-6 update-history" ng-if="model.file.file_update_history">
				<h5 class="title">更新履歴</h5>

				<div class="scroll-wrap">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>更新日時</th>
								<th>ユーザー</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="(key,value) in model.file.file_update_history">
								<td>{{value.update_datetime}}</td>
								<td>{{value.user_name}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="col-xs-6 download-history" ng-if="model.file.file_download_history">
				<h5 class="title">ダウンロード履歴</h5>

				<div class="scroll-wrap">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>ダウンロード日時</th>
								<th>ユーザー</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="value in model.file.file_download_history">
								<td>{{value.download_datetime}}</td>
								<td>{{value.user_name}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
