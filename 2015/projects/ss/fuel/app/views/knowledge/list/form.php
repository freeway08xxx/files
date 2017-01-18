<div class="tab-pane form-container clearfix active" id="form">
	<div class="row">
		<div class="col-xs-5" >
			<div ng-show="model.selectSection.authority_level == 2" ng-cloak>
				<a href="/sem/new/knowledge#/detail/edit#type_section={{typeSection}}">
					<button type="submit" class="btn btn-primary">新規登録</button>
				</a>
			</div>
		</div>

		<div class="col-xs-7">
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-search"></span></div>
				<input type="text" class="form-control" ng-model="query.free_word" placeholder="フリーワード検索" type="search" />
			</div>
		</div>

	</div>

	<div class="panel panel-default category" ng-controller="purposeHierarchyCtrl">
		<div class="panel-body">
			<h5 class="title">カテゴリ</h5>

			<div class="row">
				<! -- 大項目 -->
				<div class="col-sm-4">
					<div class="category-col">
						<div class="list-group">
							<div class="list-group-item"
								ng-class="{active: model.purposes.hierarchy.high == 0}"
								ng-click="action.clickHierarchy('', '')"
							>すべて
							</div>
							<div class="list-group-item"
								ng-class="{active: model.purposes.hierarchy.high == purpose.id}"
								ng-repeat="purpose in model.purposes.all|filter:{parent_purpose_master_id:null}|orderBy:'sort_order'" 
								ng-click="action.clickHierarchy(model.purposes.hierarchyType.high, purpose.id)"
								ng-cloak
							>
								{{purpose.name}}
							</div>
						</div>
					</div>
					<span class="arrow glyphicon glyphicon-chevron-right"></span>
				</div>

				<! -- 中項目 -->
				<div class="col-sm-4">

					<div style="top: -25px; left:-12px; position: relative;" class="pull-right" ng-show="(model.purposes.hierarchy.high != 0 && model.selectSection.authority_level == 2)" ng-cloak>
						<a class="hover-pointer" ng-click="service.addPurposeView('middle')">
							<span class="glyphicon glyphicon-plus-sign"></span>
						</a>
					</div>
					<div id="purpose-hierarchy-middle" class="category-col">
						<div class="list-group">
							<div class="list-group-item"
								ng-class="{active: model.purposes.hierarchy.middle == purpose.id}"
								ng-repeat="purpose in model.purposes.all|filter:{parent_purpose_master_id:model.purposes.hierarchy.high}:true|orderBy:'sort_order'" 
								ng-click="action.clickHierarchy(model.purposes.hierarchyType.middle, purpose.id)"
								ng-cloak
							>
								<div class="list-group-item-content" ng-show="(!purpose.editing)">
									{{purpose.name}}
									<div edit-purpose-button="purpose" ng-if="(model.purposes.hierarchy.high != 0 && model.selectSection.authority_level == 2)"></div>
								</div>
							</div>
						</div>
					</div>
					<span class="arrow glyphicon glyphicon-chevron-right"></span>
				</div>

				<! -- 小項目 -->
				<div class="col-sm-4">
					<div style="top: -25px; left:-12px; position: relative;" class="pull-right" ng-show="(model.purposes.hierarchy.middle != 0 && model.selectSection.authority_level == 2)" ng-cloak>
						<a class="hover-pointer" ng-click="service.addPurposeView('row')">
							<span class="glyphicon glyphicon-plus-sign"></span>
						</a>
					</div>
					<div id="purpose-hierarchy-row" class="category-col">
						<div class="list-group">
							<div class="list-group-item"
								ng-class="{active: model.purposes.hierarchy.low == purpose.id}"
								ng-repeat="purpose in model.purposes.all|filter:{parent_purpose_master_id:model.purposes.hierarchy.middle}:true|orderBy:'sort_order'"
								ng-click="action.clickHierarchy(model.purposes.hierarchyType.low, purpose.id)"
								ng-cloak
							>
								<div class="list-group-item-content" ng-show="( !purpose.editing )">
									{{purpose.name}}
									<div edit-purpose-button="purpose" ng-if="( model.purposes.hierarchy.high != 0 && model.selectSection.authority_level == 2 )"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
