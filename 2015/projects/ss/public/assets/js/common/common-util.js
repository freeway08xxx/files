
/**
 * オブジェクトの型判定
 */
function is(type, obj) {
    var clas = Object.prototype.toString.call(obj).slice(8, -1);
    return obj !== undefined && obj !== null && clas === type;
}

window.SsArrUtil = {
	/**
	 * 変数、オブジェクトのプロパティの存在チェック
	 * 第一引数がnullの場合はfalseを返却する。（PHPのisset()に倣う）
	 *
	 * 第二引数にオブジェクトのキーを指定することで、
	 * オブジェクト内部の値の存在チェックが可能。
	 * キーは'.'区切りで深い階層にアクセス可能。
	 *
	 * @param    {mixed}        variable       対象の変数
	 * @param    {string}       key            オブジェクトのキー
	 * @return   {boolean}      定義済みかどうか
	 */
	isDefined : function(variable, key) {
		if (variable === null) {
			return false;
		}

		// keyが指定されていないか、variableが配列かオブジェクトでなければここで判定 ※注1
		if (typeof key === 'undefined' ||
			typeof variable === 'undefined' ||
			(!is('Object', variable) && !is('Array', variable))
		   ) {
			   return typeof variable !== 'undefined';
		}

		// keyがstring か numberでなければエラー ※注2
		if (is('String', key) || is('Number', key)) {
			   // 階層の中を判定していく
			   var obj    = variable,
				   keyArr = (key).toString().split('.'), // ※注3
				   len    = keyArr.length;               // ※注4

			   for (var i = 0; i < len; i++) {
				   obj = obj[keyArr[i]];
				   if(obj == null){
						return false;
				   }
				   if (typeof obj === 'undefined') {
					   return false;
				   }
			   }

			   return true;
		   }

		throw new TypeError('type of "key" is invalid.');

	}
};

window.ssUtil = {
	/**
	 * bool扱いの値を切り替える
	 * @type {{toggle: Function}}
	 */
	toggle: function (val) {
		if (typeof val === 'undefined') return null;
		if (is('Object', val)) return val;
		if (is('Array', val)) return val;

		if (val.toLowerCase() == 'true' || val.toLowerCase()  == 'false') {
			return (val.toLowerCase() != 'true');
		}

		if (is('Number', parseInt(val))) return val === 1;

		return !val;
	}
};