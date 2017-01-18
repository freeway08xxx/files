// 変更内容のプレースホルダ CPC PC
const CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC = "アカウントID キャンペーンID 広告グループID 変更後CPC(PC)";

// 変更内容のプレースホルダ CPC SP
const CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_SP = "アカウントID キャンペーンID 広告グループID 変更後CPC(SP)";

// 変更内容のプレースホルダ CPC PC / SP
const CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC_SP = "アカウントID キャンペーンID 広告グループID 変更後CPC(PC) 変更後CPC(SP)";

// 変更内容のプレースホルダ MBA
const CPC_BULK_CHANGE_TEXT_PLACEHOLDER_MBA = "アカウントID キャンペーンID 広告グループID 変更後MBA(%不要)";

// CPC一括変更画面
$(document).ready(function() {

	currentDoc = window.opener.document;

	// 変更内容のプレースホルダの初期値
	document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC;

	// 変更方法
	$("input:radio[name='cpc_bulk_change_method']").on("change", function() {

		var method = $("input:radio[name='cpc_bulk_change_method']:checked").val();
		var device = $("input:radio[name='cpc_bulk_change_device']:checked").val();

		// CPC
		if (method === "cpc") {

			$(".cpc_bulk_change_device").css("display", "block");

			// PC
			if (device === "pc") {

				document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC;

			// SP
			} else if (device === "sp") {

				document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_SP;

			// PC / SP
			} else if (device === "pc_sp") {

				document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC_SP;
			}

		// MBA
		} else if (method === "mba") {

			$(".cpc_bulk_change_device").css("display", "none");
			document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_MBA;

		// Default CPC
		} else if (method === "default_cpc") {

			$(".cpc_bulk_change_device").css("display", "none");
			document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC;
		}
	});

	// 変更デバイス
	$("input:radio[name='cpc_bulk_change_device']").on("change", function() {

		var device = $("input:radio[name='cpc_bulk_change_device']:checked").val();

		// PC
		if (device === "pc") {

			document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC;

		// SP
		} else if (device === "sp") {

			document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_SP;

		// PC / SP
		} else if (device === "pc_sp") {

			document.form01.cpc_bulk_change_text.placeholder = CPC_BULK_CHANGE_TEXT_PLACEHOLDER_CPC_PC_SP;
		}
	});

	// 入稿
	$("#bulk_edit").on("click", function() {

		window.opener.document.form01.action_type.value="bulk_edit";

		if (bulkEditCommon()) {

			// 入稿ボタンを非表示
			document.getElementById("bulk_edit").style.display="none";
		}
	});

	// CPC変更内容DL
	$("#bulk_cpc_change_dl").on("click", function() {

		window.opener.document.form01.action_type.value="bulk_cpc_change_dl";

		if (bulkEditCommon()) {

			// 入稿ボタンを表示
			document.getElementById("bulk_edit").style.display="inline-block";
		}
	});

	// 設定
	$("#cpc_bulk_change_set").on("click", function() {

		var textValue = document.form01.cpc_bulk_change_text.value;
		var method = $("input:radio[name='cpc_bulk_change_method']:checked").val();
		var device = $("input:radio[name='cpc_bulk_change_device']:checked").val();

		// 変更内容チェック
		if (!cpcBulkChangeTextCheck(textValue, method, device)) {

			document.form01.cpc_bulk_change_text.value = "";
			return;
		}

		// 変更内容入力時のみ実行
		if (textValue !== "") {

			// 変更内容をPOST
			var textList = textValue.split("\n");

			for (i = 0; i < textList.length; i++) {

				var editList = textList[i].split("\t");

				// 掲載一覧を取得
				var structureListCount = window.opener.document.getElementById("structure_list").rows.length;

				for (j = 2; j < structureListCount; j++) {

					var listAccountId = window.opener.document.getElementById("structure_list").rows[j].cells[ACCOUNT_ID_CELL_NUM].innerHTML;
					var listCampaignId = window.opener.document.getElementById("structure_list").rows[j].cells[CAMPAIGN_ID_CELL_NUM].innerHTML;
					var listAdGroupId = window.opener.document.getElementById("structure_list").rows[j].cells[AD_GROUP_ID_CELL_NUM].innerHTML;

					// 掲載一覧のレコードとCPC一括変更の変更内容のレコードが一致する場合、一括変更内容を掲載一覧に反映
					if (listAccountId === editList[0]
							&& listCampaignId === editList[1]
							&& listAdGroupId === editList[2]) {

						// CPC
						if (method === "cpc") {

							// PC
							if (device === "pc") {

								changeCpcMaxPc(editList[3], j, false);

							// SP
							} else if (device === "sp") {

								changeCpcMaxSp(editList[3], j, false);

							// PC / SP
							} else if (device === "pc_sp") {

								changeCpcMaxPcSp(editList[3], editList[4], j, false);
							}

						// MBA
						} else if (method === "mba") {

							changeBidModifier(editList[3], j, false);

						// Default CPC
						} else if (method === "default_cpc") {

							changeDefaultCpc(editList[3], j);
						}

						break;
					}
				}
			}

			alert("CPC一括変更の反映が完了しました。");
		}
	});

	// 閉じる
	$("#cpc_bulk_change_close").on("click", function() {

		window.close();
	});
});

// 入稿 / CPC変更内容DL
function bulkEditCommon() {

	var textValue = document.form01.cpc_bulk_change_text.value;
	var method = $("input:radio[name='cpc_bulk_change_method']:checked").val();
	var device = $("input:radio[name='cpc_bulk_change_device']:checked").val();

	// 変更内容チェック
	if (!cpcBulkChangeTextCheck(textValue, method, device)) {

		document.form01.cpc_bulk_change_text.value = "";
		return;
	}

	// 変更内容入力時のみ実行
	if (textValue !== "") {

		// 変更内容をPOST
		var textList = textValue.split("\n");

		var exeFlg = false;

		for (i = 0; i < textList.length; i++) {

			if (textList[i] !== "") {

				exeFlg = true;

				var editList = textList[i].split("\t");

				// 変更方法を設定
				var inputMethod = window.opener.document.createElement("input");
				inputMethod.type = "hidden";
				inputMethod.name = "structure_list[" + i + "][method]";
				inputMethod.value = method;
				window.opener.document.form01.appendChild(inputMethod);

				// アカウントIDを設定
				var inputAccountId = window.opener.document.createElement("input");
				inputAccountId.type = "hidden";
				inputAccountId.name = "structure_list[" + i + "][account_id]";
				inputAccountId.value = editList[0];
				window.opener.document.form01.appendChild(inputAccountId);

				// キャンペーンIDを設定
				var inputCampaignId = window.opener.document.createElement("input");
				inputCampaignId.type = "hidden";
				inputCampaignId.name = "structure_list[" + i + "][campaign_id]";
				inputCampaignId.value = editList[1];
				window.opener.document.form01.appendChild(inputCampaignId);

				// 広告グループIDを設定
				var inputAdGroupId = window.opener.document.createElement("input");
				inputAdGroupId.type = "hidden";
				inputAdGroupId.name = "structure_list[" + i + "][adgroup_id]";
				inputAdGroupId.value = editList[2];
				window.opener.document.form01.appendChild(inputAdGroupId);

				// 【PC】設定CPCを設定
				var inputCpcMaxPc = window.opener.document.createElement("input");
				inputCpcMaxPc.type = "hidden";
				inputCpcMaxPc.name = "structure_list[" + i + "][adgroup_cpc_max_pc]";

				// 【SP】設定CPCを設定
				var inputCpcMaxSp = window.opener.document.createElement("input");
				inputCpcMaxSp.type = "hidden";
				inputCpcMaxSp.name = "structure_list[" + i + "][adgroup_cpc_max_sp]";

				// 設定MBAを設定
				var inputBidModifier = window.opener.document.createElement("input");
				inputBidModifier.type = "hidden";
				inputBidModifier.name = "structure_list[" + i + "][adgroup_bid_modifier]";

				var cpc_max_pc = null;
				var cpc_max_sp = null;
				var bid_modifier = null;

				// CPC
				if (method === "cpc") {

					// PC
					if (device === "pc") {

						// 正数チェック / 半角数字チェック
						if (!positiveNumberCheck(editList[3], "【PC】変更CPC", i + 1)
								|| !numberCheck(editList[3], "【PC】変更CPC", i + 1)) {

							return;
						}
						cpc_max_pc = editList[3];

					// SP
					} else if (device === "sp") {

						// 正数チェック / 半角数字チェック
						if (!positiveNumberCheck(editList[3], "【SP】変更CPC", i + 1)
								|| !numberCheck(editList[3], "【SP】変更CPC", i + 1)) {

							return;
						}
						cpc_max_sp = editList[3];

					// PC / SP
					} else if (device === "pc_sp") {

						// 正数チェック / 半角数字チェック
						if (!positiveNumberCheck(editList[3], "【PC】変更CPC", i + 1)
								|| !numberCheck(editList[3], "【PC】変更CPC", i + 1)
								|| !positiveNumberCheck(editList[4], "【SP】変更CPC", i + 1)
								|| !numberCheck(editList[4], "【SP】変更CPC", i + 1)) {

							return;
						}
						cpc_max_pc = editList[3];
						cpc_max_sp = editList[4];
					}

				// MBA
				} else if (method === "mba") {

					// モバイル調整率チェック
					if (!mbaCheck(editList[3], "変更MBA", i + 1, false)) {

						return;
					}
					bid_modifier = editList[3];

				// Default CPC
				} else if (method === "default_cpc") {

					// 正数チェック / 半角数字チェック
					if (!positiveNumberCheck(editList[3], "【PC】変更CPC", i + 1)
							|| !numberCheck(editList[3], "【PC】変更CPC", i + 1)) {

						return;
					}
					cpc_max_pc = editList[3];
				}

				// 【PC】設定CPCを設定
				inputCpcMaxPc.value = cpc_max_pc;
				window.opener.document.form01.appendChild(inputCpcMaxPc);

				// 【SP】設定CPCを設定
				inputCpcMaxSp.value = cpc_max_sp;
				window.opener.document.form01.appendChild(inputCpcMaxSp);

				// 設定MBAを設定
				inputBidModifier.value = bid_modifier;
				window.opener.document.form01.appendChild(inputBidModifier);
			}
		}

		if (exeFlg) {

			window.opener.document.form01.action="/sem/new/eagle/cpc/update/edit";
			window.opener.document.form01.target="";
			window.opener.document.form01.submit();

			return true;
		}
	}
}
