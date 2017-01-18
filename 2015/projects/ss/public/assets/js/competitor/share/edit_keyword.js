var service = 'competitor/share';
var paths = location.pathname.split(service);
var c_path = paths[0];
/**
 * キーワードを登録する
 */
function entry(word){
	//入力チェック
	if (document.form01.keyword.value === "") {
		alert('キーワードを入力してください。');
		return false;
	}
	if (confirm("登録します。よろしいですか?")) {
		document.form01.action="editkeyword.php?" + word;
		document.form01.action_type.value="entry";
		document.form01.submit();
	}
}

/**
 *
 */
function entryCategory(){
	//入力チェック
	document.form01.action="editkeyword/set.php";
	document.form01.target="editkeyword/set";
	document.form01.submit();
	document.form01.target="_self";
}

/**
 *
 */
function changeKeywordType(){
	document.form01.action="editkeyword.php";
	document.form01.submit();
}

/**
 * キーワードを削除する
 */
function deleteKeyword(word){
	var checked = false;
	$('div.checkbox input').each(function(){
		if (checked) {
			return false;
		}
		checked = $(this).prop("checked");
	});

	if (!checked) {
		alert("対象を選択してください。");
		return false;
	}

	if (confirm("削除します。よろしいですか?")) {
		document.form01.action="editkeyword.php?" + word;
		document.form01.action_type.value="delete";
		document.form01.submit();
	}
}

/**
 * ドキュメントロード時に実行する処理は以下にまとめる
 */
$(function(){
	var word = $('input[name="word"]').val();

	/**
	 * DOM Control
	 */

	$(document).on('click', '#check_all', function(){
		$('div.checkbox input').prop('checked', false);
		$('div.checkbox input').prop('checked', true);
	});

	$(document).on('click', '#entry_btn', function(){
		entry(word);
	});

	$(document).on('click', '#delete_btn', function(){
		deleteKeyword(word);
	});


});
