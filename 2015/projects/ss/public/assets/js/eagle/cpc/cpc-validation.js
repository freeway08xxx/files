// 半角数字チェック
function numberCheck(value, item, row) {

	if (value !== "") {

		if (value.match(/[^0-9]+/)) {

			alert("半角数字を入力してください。\n[No:" + row + "] " + item);
			return false;
		}
	}

	return true;
}

// 正数チェック
function positiveNumberCheck(value, item, row) {

	if (value !== "") {

		// 正数チェック
		if (value < 0) {

			alert("１以上の数値を設定してください。\n[No:" + row + "] " + item);
			return false;
		}
	}

	return true;
}

// モバイル調整率チェック
function mbaCheck(value, item, row, alertFlg) {

	if (value !== "") {

		// 半角数字チェック
		var chkValue = value;
		var valueTop = value.substr(0, 1);

		if (valueTop === "-") {
			chkValue = value.substr(1);
		}

		if (chkValue.match(/[^0-9]+/)) {

			alert("半角数字を入力してください。\n[No:" + row + "] " + item);
			return false;
		}

		// モバイル調整率チェック
		if (alertFlg && (value < -100 || value > 300 || (value < -90 && value > -100))) {

			alert("-100% ～ -90% もしくは 0% ～ 300% の間で設定してください。\n[No:" + row + "] " + item);
			return false;
		}
	}

	return true;
}

// CPC一括変更の変更内容チェック
function cpcBulkChangeTextCheck(value, method, device) {

	if (value !== "") {

		// 変更内容チェック
		var valueList = value.split("\n");

		for (i = 0; i < valueList.length; i++) {

			var objValue = valueList[i].split("\t");

			// 項目数チェック
			var checkNum = 0;

			// CPC & PC / SP
			if (method === "cpc" && device === "pc_sp") {

				checkNum = 5;

			} else {

				checkNum = 4;
			}

			if (objValue[0] !== "" && objValue.length !== checkNum) {

				alert("変更内容のフォーマットが不正です。\n[" + (i + 1) + "行目]");
				return false;
			}
		}
	}

	return true;
}
