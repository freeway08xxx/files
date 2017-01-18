// アカウント検索 / キャンペーン検索
function searchComponent(component, id) {

	// アカウント検索
	if (component === "account") {

		var objList = document.getElementById("account_id_list");
		var searchText = document.form01.account_search_text.value;
		var searchType = $("input:radio[name='account_search_type']:checked").val();
		var searchExcept = document.form01.account_search_except.checked;
		var searchBroad = document.form01.account_search_broad.checked;
		var searchIdOnly = document.form01.account_search_id_only.checked;

	// キャンペーン検索
	} else if (component === "campaign") {

		var objList = document.getElementById("campaign_id_list");
		var searchText = document.form01.campaign_search_text.value;
		var searchType = $("input:radio[name='campaign_search_type']:checked").val();
		var searchExcept = document.form01.campaign_search_except.checked;
		var searchBroad = document.form01.campaign_search_broad.checked;
		var searchIdOnly = document.form01.campaign_search_id_only.checked;
	}

	// 検索文字列入力時のみ実行
	if (searchText !== "") {

		var searchTextList = searchText.split("\n");

		for (i = 0; i < objList.length; i++) {

			var objText = objList[i].text;
			var display_none_flg = false;
			$("." + id + i).css("display", "block");

			for (j in searchTextList) {

				if (searchTextList[j] === "") {
					continue;
				}

				// ID検索 ON
				if (searchIdOnly) {
					if (component === "account") {
						var componentId = objText.match(/\[([a-zA-Z0-9-]*)\]/);
					} else if (component === "campaign") {
						var componentId = objText.match(/\[[a-zA-Z0-9-]*\]\[([a-zA-Z0-9-]*)\]/);
					}
					if (componentId) {
						objText = componentId[1];
					}
				}

				// AND
				if (searchType === "and") {

					// 除外 ON / 部分一致 ON
					if (searchExcept && searchBroad) {
						if (objText.match(searchTextList[j])) {
							display_none_flg = true;
						} else {
							display_none_flg = false;
							break;
						}

					// 除外 ON / 部分一致 OFF
					} else if (searchExcept && !searchBroad) {
						if (objText === searchTextList[j]) {
							display_none_flg = true;
						} else {
							display_none_flg = false;
							break;
						}

					// 除外 OFF / 部分一致 ON
					} else if (!searchExcept && searchBroad) {
						if (!objText.match(searchTextList[j])) {
							display_none_flg = true;
							break;
						} else {
							display_none_flg = false;
						}

					// 除外 OFF / 部分一致 OFF
					} else {
						if (objText !== searchTextList[j]) {
							display_none_flg = true;
							break;
						} else {
							display_none_flg = false;
						}
					}

				// OR
				} else if (searchType === "or") {

					// 除外 ON / 部分一致 ON
					if (searchExcept && searchBroad) {
						if (objText.match(searchTextList[j])) {
							display_none_flg = true;
							break
						} else {
							display_none_flg = false;
						}

					// 除外 ON / 部分一致 OFF
					} else if (searchExcept && !searchBroad) {
						if (objText === searchTextList[j]) {
							display_none_flg = true;
							break;
						} else {
							display_none_flg = false;
						}

					// 除外 OFF / 部分一致 ON
					} else if (!searchExcept && searchBroad) {
						if (!objText.match(searchTextList[j])) {
							display_none_flg = true;
						} else {
							display_none_flg = false;
							break;
						}

					// 除外 OFF / 部分一致 OFF
					} else {
						if (objText !== searchTextList[j]) {
							display_none_flg = true;
						} else {
							display_none_flg = false;
							break;
						}
					}
				}
			}

			// コンポーネントを非表示
			if (display_none_flg) {
				$("." + id + i).css("display", "none");
			}
		}
	} else {

		// 全表示
		for (i = 0; i < objList.length; i++) {
			$("." + id + i).css("display", "block");
		}
	}

	$("#" + id).multiSelect("refresh");
}

// 全選択 / 全解除
function allSelectCommon(id) {

	var objList = document.getElementById(id);
	var objArray = new Array();

	// 表示されているコンポーネントのみ対象
	for (i = 0; i < objList.length; i++) {

		if ($("." + id + i).css("display") === "inline"
				|| $("." + id + i).css("display") === "block") {

			objArray.push(objList[i].value);
		}
	}

	return objArray;
}

// 現在のスクロール位置を取得
function getScrollPosition() {

	document.form01.scroll_x.value = document.documentElement.scrollLeft || document.body.scrollLeft;
	document.form01.scroll_y.value = document.documentElement.scrollTop || document.body.scrollTop;
}

// submit時のスクロール位置を設定
function setScrollPosition(scroll_x, scroll_y) {

	window.scroll(scroll_x, scroll_y);
}
