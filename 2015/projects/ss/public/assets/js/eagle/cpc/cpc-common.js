// メディアIDのセル位置
const MEDIA_ID_CELL_NUM = 1;

// アカウントIDのセル位置
const ACCOUNT_ID_CELL_NUM = 2;

// アカウント名のセル位置
const ACCOUNT_NAME_CELL_NUM = 3;

// キャンペーンIDのセル位置
const CAMPAIGN_ID_CELL_NUM = 5;

// キャンペーン名のセル位置
const CAMPAIGN_NAME_CELL_NUM = 6;

// 広告グループIDのセル位置
const AD_GROUP_ID_CELL_NUM = 8;

// 広告グループ名のセル位置
const AD_GROUP_NAME_CELL_NUM = 9;

// 【PC】現在の設定CPCのセル位置
const NOW_ADG_CPC_MAX_PC_CELL_NUM = 12;

// 【PC】設定CPCのセル位置
const ADG_CPC_MAX_PC_CELL_NUM = 13;

// 【SP】現在の設定CPCのセル位置
const NOW_ADG_CPC_MAX_SP_CELL_NUM = 15;

// 【SP】設定CPCのセル位置
const ADG_CPC_MAX_SP_CELL_NUM = 16;

// 現在の設定MBAのセル位置
const NOW_ADG_BID_MODIFIER_CELL_NUM = 18;

// 設定MBAのセル位置
const ADG_BID_MODIFIER_CELL_NUM = 19;

// MBAの最大値（300%）
const MAX_BID_MODIFIER = 4;

// 現在のドキュメント
var currentDoc;

// 入稿 / CPC変更内容DL
function editCommon() {

	// 掲載一覧の設定値をPOST
	var structureListCount = currentDoc.getElementById("structure_list").rows.length;

	// モバイル調整率チェック
	for (i = 2; i < structureListCount; i++) {

		var bidModifier = currentDoc.getElementById("structure_list").rows[i].cells[ADG_BID_MODIFIER_CELL_NUM].innerHTML;
		bidModifier = bidModifier.replace("%", "");
		bidModifier = bidModifier.replace(/(^\s+)|(\s+$)/g, "");

		if (!mbaCheck(bidModifier, "変更MBA", i - 1, false)) {

			return;
		}
	}

	for (i = 2; i < structureListCount; i++) {

		var mediaId = currentDoc.getElementById("structure_list").rows[i].cells[MEDIA_ID_CELL_NUM].innerHTML;
		var accountId = currentDoc.getElementById("structure_list").rows[i].cells[ACCOUNT_ID_CELL_NUM].innerHTML;
		var accountName = currentDoc.getElementById("structure_list").rows[i].cells[ACCOUNT_NAME_CELL_NUM].innerHTML;
		var campaignId = currentDoc.getElementById("structure_list").rows[i].cells[CAMPAIGN_ID_CELL_NUM].innerHTML;
		var campaignName = currentDoc.getElementById("structure_list").rows[i].cells[CAMPAIGN_NAME_CELL_NUM].innerHTML;
		var adGroupId = currentDoc.getElementById("structure_list").rows[i].cells[AD_GROUP_ID_CELL_NUM].innerHTML;
		var adGroupName = currentDoc.getElementById("structure_list").rows[i].cells[AD_GROUP_NAME_CELL_NUM].innerHTML;
		var cpcMaxPc = currentDoc.getElementById("structure_list").rows[i].cells[ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
		var cpcMaxSp = currentDoc.getElementById("structure_list").rows[i].cells[ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
		var bidModifier = currentDoc.getElementById("structure_list").rows[i].cells[ADG_BID_MODIFIER_CELL_NUM].innerHTML;
		bidModifier = bidModifier.replace("%", "");
		bidModifier = bidModifier.replace(/(^\s+)|(\s+$)/g, "");

		// メディアIDを設定
		var inputMediaId = currentDoc.createElement("input");
		inputMediaId.type = "hidden";
		inputMediaId.name = "structure_list[" + i + "][media_id]";
		inputMediaId.value = mediaId;
		currentDoc.form01.appendChild(inputMediaId);

		// アカウントIDを設定
		var inputAccountId = currentDoc.createElement("input");
		inputAccountId.type = "hidden";
		inputAccountId.name = "structure_list[" + i + "][account_id]";
		inputAccountId.value = accountId;
		currentDoc.form01.appendChild(inputAccountId);

		// アカウント名を設定
		var inputAccountName = currentDoc.createElement("input");
		inputAccountName.type = "hidden";
		inputAccountName.name = "structure_list[" + i + "][account_name]";
		inputAccountName.value = accountName;
		currentDoc.form01.appendChild(inputAccountName);

		// キャンペーンIDを設定
		var inputCampaignId = currentDoc.createElement("input");
		inputCampaignId.type = "hidden";
		inputCampaignId.name = "structure_list[" + i + "][campaign_id]";
		inputCampaignId.value = campaignId;
		currentDoc.form01.appendChild(inputCampaignId);

		// キャンペーン名を設定
		var inputCampaignName = currentDoc.createElement("input");
		inputCampaignName.type = "hidden";
		inputCampaignName.name = "structure_list[" + i + "][campaign_name]";
		inputCampaignName.value = campaignName;
		currentDoc.form01.appendChild(inputCampaignName);

		// 広告グループIDを設定
		var inputAdGroupId = currentDoc.createElement("input");
		inputAdGroupId.type = "hidden";
		inputAdGroupId.name = "structure_list[" + i + "][adgroup_id]";
		inputAdGroupId.value = adGroupId;
		currentDoc.form01.appendChild(inputAdGroupId);

		// 広告グループ名を設定
		var inputAdGroupName = currentDoc.createElement("input");
		inputAdGroupName.type = "hidden";
		inputAdGroupName.name = "structure_list[" + i + "][adgroup_name]";
		inputAdGroupName.value = adGroupName;
		currentDoc.form01.appendChild(inputAdGroupName);

		// 【PC】設定CPCを設定
		var inputCpcMaxPc = currentDoc.createElement("input");
		inputCpcMaxPc.type = "hidden";
		inputCpcMaxPc.name = "structure_list[" + i + "][adgroup_cpc_max_pc]";
		inputCpcMaxPc.value = cpcMaxPc;
		currentDoc.form01.appendChild(inputCpcMaxPc);

		// 【SP】設定CPCを設定
		var inputCpcMaxSp = currentDoc.createElement("input");
		inputCpcMaxSp.type = "hidden";
		inputCpcMaxSp.name = "structure_list[" + i + "][adgroup_cpc_max_sp]";
		inputCpcMaxSp.value = cpcMaxSp;
		currentDoc.form01.appendChild(inputCpcMaxSp);

		// 設定MBAを設定
		var inputBidModifier = currentDoc.createElement("input");
		inputBidModifier.type = "hidden";
		inputBidModifier.name = "structure_list[" + i + "][adgroup_bid_modifier]";
		inputBidModifier.value = bidModifier;
		currentDoc.form01.appendChild(inputBidModifier);
	}

	currentDoc.form01.action="/sem/new/eagle/cpc/update/edit";
	currentDoc.form01.target="";
	currentDoc.form01.submit();
}

// 【PC】変更CPC
function changeCpcMaxPc(cpcMaxPc, rowIndex, alertFlg) {

	// 正数チェック
	if (!positiveNumberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 半角数字チェック
	if (!numberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 【PC】変更CPC入力時のみ実行
	if (cpcMaxPc !== "") {

		var mediaId = currentDoc.getElementById("structure_list").rows[rowIndex].cells[MEDIA_ID_CELL_NUM].innerHTML;
		var cpcMaxPcCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_PC_CELL_NUM];
		var cpcMaxSpCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_SP_CELL_NUM];
		var bidModifierCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_BID_MODIFIER_CELL_NUM];
		var befCpcMaxPc = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
		var befCpcMaxSp = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
		var befBidModifier = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_BID_MODIFIER_CELL_NUM].innerHTML;

		var bidModifier = Math.round((cpcMaxSpCell.innerHTML / cpcMaxPc) * 100) / 100;

		// 設定MBAが媒体の最大値より大きい場合、最大値を設定
		if (bidModifier > MAX_BID_MODIFIER) {

			bidModifier = MAX_BID_MODIFIER;
			cpcMaxSpCell.innerHTML = cpcMaxPc * bidModifier;
		}

		bidModifierDisp = Math.round((bidModifier - 1) * 100);

		// モバイル調整率チェック
		if (mediaId !== 3 && !mbaCheck(String(bidModifierDisp), "変更MBA", rowIndex - 1, alertFlg)) {

			return;
		}

		cpcMaxPcCell.innerHTML = cpcMaxPc;
		bidModifierCell.innerHTML = bidModifierDisp + "%";

		// 【PC】設定CPCを変更した場合、文字色を赤色で表示
		if (parseInt(cpcMaxPc) !== parseInt(befCpcMaxPc)) {
			cpcMaxPcCell.style.color = "red";
		} else {
			cpcMaxPcCell.style.color = "dimgray";
		}

		// 【SP】設定CPCが変更された場合、文字色を赤色で表示
		if (parseInt(cpcMaxSpCell.innerHTML) !== parseInt(befCpcMaxSp)) {
			cpcMaxSpCell.style.color = "red";
		} else {
			cpcMaxSpCell.style.color = "dimgray";
		}

		// 設定MBAが変更された場合、文字色を赤色で表示
		if (parseFloat(bidModifier) !== parseFloat(befBidModifier)) {
			bidModifierCell.style.color = "red";
		} else {
			bidModifierCell.style.color = "dimgray";
		}
	}
}

// 【SP】変更CPC
function changeCpcMaxSp(cpcMaxSp, rowIndex, alertFlg) {

	// 正数チェック
	if (!positiveNumberCheck(cpcMaxSp, "【SP】変更CPC", rowIndex - 1)) {

		return;
	}

	// 半角数字チェック
	if (!numberCheck(cpcMaxSp, "【SP】変更CPC", rowIndex - 1)) {

		return;
	}

	var cpcMaxPcCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_PC_CELL_NUM];
	var cpcMaxSpCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_SP_CELL_NUM];
	var bidModifierCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_BID_MODIFIER_CELL_NUM];
	var befCpcMaxSp = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
	var befBidModifier = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_BID_MODIFIER_CELL_NUM].innerHTML;

	var bidModifier = Math.round((cpcMaxSp / cpcMaxPcCell.innerHTML) * 100) / 100;

	// 設定MBAが媒体の最大値より大きい場合、最大値を設定
	var setCpcMaxSp;

	if (bidModifier > MAX_BID_MODIFIER) {

		bidModifier = MAX_BID_MODIFIER;
		setCpcMaxSp = cpcMaxPcCell.innerHTML * bidModifier;

	} else {

		setCpcMaxSp = cpcMaxSp;
	}

	bidModifierDisp = Math.round((bidModifier - 1) * 100);

	// モバイル調整率チェック
	if (!mbaCheck(String(bidModifierDisp), "変更MBA", rowIndex - 1, alertFlg)) {

		return;
	}

	// 【SP】変更CPC入力時のみ実行
	if (cpcMaxSp !== "") {

		cpcMaxSpCell.innerHTML = setCpcMaxSp;
		bidModifierCell.innerHTML = bidModifierDisp + "%";

		// 【SP】設定CPCが変更された場合、文字色を赤色で表示
		if (parseInt(cpcMaxSp) !== parseInt(befCpcMaxSp)) {
			cpcMaxSpCell.style.color = "red";
		} else {
			cpcMaxSpCell.style.color = "dimgray";
		}

		// 設定MBAが変更された場合、文字色を赤色で表示
		if (parseFloat(bidModifier) !== parseFloat(befBidModifier)) {
			bidModifierCell.style.color = "red";
		} else {
			bidModifierCell.style.color = "dimgray";
		}
	}
}

// 【PC】変更CPC / 【SP】変更CPC
function changeCpcMaxPcSp(cpcMaxPc, cpcMaxSp, rowIndex, alertFlg) {

	// 正数チェック 【PC】変更CPC
	if (!positiveNumberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 正数チェック 【SP】変更CPC
	if (!positiveNumberCheck(cpcMaxSp, "【SP】変更CPC", rowIndex - 1)) {

		return;
	}

	// 半角数字チェック 【PC】変更CPC
	if (!numberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 半角数字チェック 【SP】変更CPC
	if (!numberCheck(cpcMaxSp, "【SP】変更CPC", rowIndex - 1)) {

		return;
	}

	var mediaId = currentDoc.getElementById("structure_list").rows[rowIndex].cells[MEDIA_ID_CELL_NUM].innerHTML;
	var cpcMaxPcCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_PC_CELL_NUM];
	var cpcMaxSpCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_SP_CELL_NUM];
	var bidModifierCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_BID_MODIFIER_CELL_NUM];
	var befCpcMaxPc = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
	var befCpcMaxSp = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
	var befBidModifier = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_BID_MODIFIER_CELL_NUM].innerHTML;

	var setCpcMaxSp = cpcMaxSp;
	var bidModifier = Math.round((cpcMaxSp / cpcMaxPc) * 100) / 100;

	// 設定MBAが媒体の最大値より大きい場合、最大値を設定
	if (bidModifier > MAX_BID_MODIFIER) {

		bidModifier = MAX_BID_MODIFIER;
		setCpcMaxSp = cpcMaxPc * bidModifier;
	}

	bidModifierDisp = Math.round((bidModifier - 1) * 100);

	// モバイル調整率チェック
	if (mediaId !== 3 && !mbaCheck(String(bidModifierDisp), "変更MBA", rowIndex - 1, alertFlg)) {

		return;
	}

	cpcMaxPcCell.innerHTML = cpcMaxPc;
	cpcMaxSpCell.innerHTML = setCpcMaxSp;
	bidModifierCell.innerHTML = bidModifierDisp + "%";

	// 【PC】設定CPCを変更した場合、文字色を赤色で表示
	if (parseInt(cpcMaxPcCell.innerHTML) !== parseInt(befCpcMaxPc)) {
		cpcMaxPcCell.style.color = "red";
	} else {
		cpcMaxPcCell.style.color = "dimgray";
	}

	// 【SP】設定CPCが変更された場合、文字色を赤色で表示
	if (parseInt(cpcMaxSpCell.innerHTML) !== parseInt(befCpcMaxSp)) {
		cpcMaxSpCell.style.color = "red";
	} else {
		cpcMaxSpCell.style.color = "dimgray";
	}

	// 設定MBAが変更された場合、文字色を赤色で表示
	if (parseFloat(bidModifier) !== parseFloat(befBidModifier)) {
		bidModifierCell.style.color = "red";
	} else {
		bidModifierCell.style.color = "dimgray";
	}
}

// 変更MBA
function changeBidModifier(bidModifierDisp, rowIndex, alertFlg) {

	// モバイル調整率チェック
	if (!mbaCheck(bidModifierDisp, "変更MBA", rowIndex - 1, alertFlg)) {

		return;
	}

	// 変更MBA入力時のみ実行
	if (bidModifierDisp !== "") {

		var cpcMaxPcCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_PC_CELL_NUM];
		var cpcMaxSpCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_SP_CELL_NUM];
		var bidModifierCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_BID_MODIFIER_CELL_NUM];
		var befCpcMaxSp = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
		var befBidModifier = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_BID_MODIFIER_CELL_NUM].innerHTML;

		bidModifier = Math.round(((bidModifierDisp / 100) + 1) * 100) / 100;
		cpcMaxSpCell.innerHTML = Math.round(cpcMaxPcCell.innerHTML * bidModifier);
		bidModifierCell.innerHTML = bidModifierDisp + "%";

		// 【SP】設定CPCが変更された場合、文字色を赤色で表示
		if (parseInt(cpcMaxSpCell.innerHTML) !== parseInt(befCpcMaxSp)) {
			cpcMaxSpCell.style.color = "red";
		} else {
			cpcMaxSpCell.style.color = "dimgray";
		}

		// 設定MBAが変更された場合、文字色を赤色で表示
		if (parseFloat(bidModifier) !== parseFloat(befBidModifier)) {
			bidModifierCell.style.color = "red";
		} else {
			bidModifierCell.style.color = "dimgray";
		}
	}
}

// Default CPC
function changeDefaultCpc(cpcMaxPc, rowIndex) {

	// 正数チェック
	if (!positiveNumberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 半角数字チェック
	if (!numberCheck(cpcMaxPc, "【PC】変更CPC", rowIndex - 1)) {

		return;
	}

	// 【PC】変更CPC入力時のみ実行
	if (cpcMaxPc !== "") {

		var cpcMaxPcCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_PC_CELL_NUM];
		var cpcMaxSpCell = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_CPC_MAX_SP_CELL_NUM];
		var bidModifier = currentDoc.getElementById("structure_list").rows[rowIndex].cells[ADG_BID_MODIFIER_CELL_NUM].innerHTML;
		var befCpcMaxPc = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
		var befCpcMaxSp = currentDoc.getElementById("structure_list").rows[rowIndex].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;

		bidModifier = (bidModifier.slice(0, -1) / 100) + 1;

		cpcMaxPcCell.innerHTML = cpcMaxPc;
		cpcMaxSpCell.innerHTML = Math.round(cpcMaxPc * bidModifier);

		// 【PC】設定CPCを変更した場合、文字色を赤色で表示
		if (parseInt(cpcMaxPc) !== parseInt(befCpcMaxPc)) {
			cpcMaxPcCell.style.color = "red";
		} else {
			cpcMaxPcCell.style.color = "dimgray";
		}

		// 【SP】設定CPCが変更された場合、文字色を赤色で表示
		if (parseInt(cpcMaxSpCell.innerHTML) !== parseInt(befCpcMaxSp)) {
			cpcMaxSpCell.style.color = "red";
		} else {
			cpcMaxSpCell.style.color = "dimgray";
		}
	}
}
