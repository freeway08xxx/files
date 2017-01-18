<div ng-controller="TsktSettingCategoryCtrl as tsktSettingCategory">
	<div ui-view class="transition">
		<!--
		<ul class="breadcrumb">
			<li class="active">カテゴリ一覧</a></li>
		</ul>
		-->
		
		<h4>カテゴリ一覧</h4>
		<div>
			<div class="main-table">
				<div ss-table-rc="tsktSettingCategory.models.categoryTable" external-scopes="tsktSettingCategory"></div>
			</div>
		</div>
	</div>
</div>
