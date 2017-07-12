/**
 * 文章校正に関するjs
 *
 * 注 : javascriptでは4byte文字のlengthが2となる為(サロゲートペア)、length,substrなどは標準関数を使わずに独自関数で処理している
 */

(function () {
    $(window).ready(function () {

        /*
         文章校正 変換マスター
         -------------------------------------------------- */
        var proofReplaceMaster = {
            '誤変換'               : {id  :  1, msg : '誤変換の可能性があります。'},
            '誤用'                 : {id  :  2, msg : '誤用の可能性があります。'},
            '使用注意'             : {id  :  3, msg : '使用注意する必要があります。'},
            '不快語'               : {id  :  4, msg : '不快語の可能性があります。'},
            '機種依存または拡張文字' : {id  :  5, msg : '機種依存文字です。特定の環境で文字化けする可能性があります。'},
            '外国地名'             : {id  :  6, msg : '外国地名の可能性があります。'},
            '固有名詞'             : {id  :  7, msg : '固有名詞の可能性があります。'},
            '人名'                 : {id  :  8, msg : '人名の可能性があります。'},
            'ら抜き'               : {id  :  9, msg : 'ら抜き言葉の可能性があります。'},
            '当て字'               : {id  :  10, msg : '当て字の可能性があります。'},
            '表外漢字あり'          : {id  :  11, msg : '表外漢字の可能性があります。'},
            '用字'                 : {id  :  12, msg : 'より平易な表記が望ましい場合があります。'},
            '用語言い換え'          : {id  :  13, msg : '用語言い換えができます。'},
            '二重否定'             : {id  :  14, msg : '二重否定の可能性があります。'},
            '助詞不足の可能性あり'   : {id  :  15, msg : '助詞不足の可能性があります。'},
            '冗長表現'             : {id  :  16, msg : '冗長表現の可能性があります。'},
            '略語'                 : {id  :  17, msg : '略語の可能性があります。'}
        };

        /*
         文章校正API
         -------------------------------------------------- */

        var proofApiService = {
            getProofText : function(q,cnt){
                var defer = $.Deferred();
                $.ajax({
                    type     : 'POST',
                    url      : '/my_c3/api/yahoo_proof_api',
                    data     :  q,
                    imeout  : 10000,
                    error    : defer.reject
                }).then(function(data){
                    defer.resolve(data, cnt);
                });
                return defer.promise();
            },
            getDeadLink : function(q){
                var defer = $.Deferred();
                $.ajax({
                    type     : 'POST',
                    url      : '/my_c3/api/dead_link_api',
                    data     :  q,
                    dataType : 'json',
                    timeout  : 35000,
                    error    : defer.reject
                }).then(function(data){
                    defer.resolve(data);
                });
                return defer.promise();
            },
            getWarningWord : function(q){
                var defer = $.Deferred();
                $.ajax({
                    type     : 'POST',
                    url      : '/my_c3/api/warning_word_api',
                    data     :  q,
                    dataType : 'json',
                    timeout  : 10000,
                    error    : defer.reject
                }).then(function(data){
                    defer.resolve(data);
                });
                return defer.promise();
            },
            getByte4Word : function(q){
                var defer = $.Deferred();
                $.ajax({
                    type     : 'POST',
                    url      : '/my_c3/api/byte4_word_api',
                    data     :  q,
                    dataType : 'json',
                    timeout  : 10000,
                    error    : defer.reject
                }).then(function(data){
                    defer.resolve(data);
                });
                return defer.promise();
            }
        };

        /*
         共通変数
         ------------------------------------------ */
        var loading   = false;
        var addLength = 0; //for文で校正文字をspanでwrapする際に加える対象spanの長さ

        /**
         * yahooのレスポンスに対して除外する項目を指定
         * @link http://developer.yahoo.co.jp/webapi/jlp/kousei/v1/kousei.html
         * レスポンスエラー時はidを変更してリトライ
         */
        var yahooFilterParams = {
            filter_group : '1,2,3',
            no_filter    : '2,6,7,8,11,12,13,15,16,17',
            //no_filter    : '2',//test
            dev_ids      : ['dj0zaiZpPU1VWVYxeHJkNDZPUyZzPWNvbnN1bWVyc2VjcmV0Jng9ZTA-','dj0zaiZpPTNYU2tQZjRrRkNjYSZzPWNvbnN1bWVyc2VjcmV0Jng9MzM-'],
            selected_id  : 'dj0zaiZpPU1VWVYxeHJkNDZPUyZzPWNvbnN1bWVyc2VjcmV0Jng9ZTA-'
        };

        var yahooMaxQuery = 800;
        var get,       //YahooAPI 非同期並列取得イベント
            watch = [];//YahooAPI 非同期並列イベント終了を監視

        //校正させないリスト
        var whiteList = {
            yahoo : ['px','youtube','google'],
            link  : ['https://www.google.com/maps']
        };

        var warning = {
            style: '',
            msg  : '',
            num  : 0
        };

        /*
         オブジェクト操作
         ------------------------------------------ */
        var proofFactory  = {
            /*
             *  初期化
             */
            init : function(){
                $('.js-proofcover').fadeOut(500);
                $('.js-proofreadBtn a').removeClass('disabled');
                $('.js-msg-box').fadeOut('fast',function(){
                    $(this).find('span').removeClass('success warning');
                });
                addLength = 0;
                warning = {
                    style: '',
                    msg  : '',
                    num  : 0
                };
            },

            /*
             *  textのstartからlength分の文字列をafterに差し替える
             *  サロゲートペア対応のため、stringを配列に格納して処理
             */
            wrapText : function(text,after,start,length){
                start           = parseInt(start);
                length          = parseInt(length);
                var start_text  = proofFactory.convertStringToArray(text).splice(0, start + addLength).join('');
                var end_text    = drop( proofFactory.convertStringToArray(text),start + length + addLength ).join('');
                addLength       = parseInt(addLength + proofFactory.strlen(after) - length);
                warning.num++;
                return start_text + after + end_text;

                //arrのn番目以降を返す
                function drop(arr, n){
                    var res = [];
                    for (var i = n, l = arr.length; i < l; i++) {
                        res.push(arr[i]);
                    }
                    return res;
                };
            },

            /*
             * マッチした全てのstrをタグで囲って返す
             * @return json
             */
            wrapAllMatchWord : function(_param,json,str,tpl){
                $.each(json, function (key, arr) {
                    warning.num = warning.num + json[key].length;
                    var words   = _.pluck(json[key], str);

                    _param[proofFactory.convertStr(key,'text','sentence')] = proofFactory.setTmpWord(_param[proofFactory.convertStr(key,'text','sentence')]);

                    $.each(_.uniq(words), function (j, val) {
                        var regExp = proofFactory.getEscapeWord(val);
                        _param[proofFactory.convertStr(key,'text','sentence')] = _param[proofFactory.convertStr(key,'text','sentence')].replace(new RegExp(regExp ,'g'), tpl[0] + val + tpl[1]);
                    });

                    _param[proofFactory.convertStr(key,'text','sentence')] = proofFactory.resetTmpWord(_param[proofFactory.convertStr(key,'text','sentence')]);

                });
                return _param;
            },

            /*
             *  data属性のURLに校正が引っかからないように一時的に文字列を変更
             *  @return string
             *
             */
            setTmpWord : function(val){
                //data属性 回避用関数
                var target      = 'data-value=\"http';
                var target_tmp  = 'data-value=\"http_temporary';
                var res = (typeof val == 'string') ? val.replace(new RegExp(target ,'g'), target_tmp):val;
                return res;
            },

            /*
             *  文字列を元に戻す
             *  @return string
             *
             */
            resetTmpWord : function(val){
                //data属性 回避用関数
                var target      = 'data-value=\"http';
                var target_tmp  = 'data-value=\"http_temporary';
                var res = (typeof val == 'string') ?val.replace(new RegExp(target_tmp ,'g'), target) : val;
                return res;
            },



            /*
             *  replace関数でエスケープが必要な場合は付与して返す
             *  @return string
             *
             */
            getEscapeWord : function(val){
                if(val.indexOf('?') != -1) {
                    var arr = val.split('?');
                    val = arr.join('\\?')
                };
                return val;
            },

            /*
             * 型変換
             * @return string
             */
            convertStringToArray : function(str){
                var res = (_hasSurrogatePair(str)) ? str.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[^\uD800-\uDFFF]/g) : str.split("");

                function _hasSurrogatePair(str) {
                    for ( var i = 0; i < str.length; i++) {
                        var c = str.charCodeAt(i);
                        if ((0xD800 <= c && c <= 0xDBFF) || (0xDC00 <= c && c <= 0xDFFF)) return true;
                    }
                    return false;
                }
                return res;
            },

            /*
             *  サロゲートペアの文字数は１とするlengthを取得
             *
             */
            strlen : function (str) {
                return str.length - (str.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g)||[]).length;
            },

            /*
             * 配列、strの部分一致確認
             * @return boolean
             */
            isInArrPartial : function(arr, str){
                var res = false;
                $.each(arr, function(i,val) {
                    if(str.indexOf(val) != -1) res = true;
                });
                return res;
            },
            /*
             * textをnごとに分割する サロゲートペアの間を分割させないように処理
             * @return object
             */
            sliceSentence : function(obj,n,text){
                //strを一文字づつarrに格納
                var arr   = proofFactory.convertStringToArray(text);
                arr       =  _.values(_.groupBy(arr, function(v, i) { return Math.floor(i / n) }) );
                $.each(arr, function (i, childArr) {
                    obj['sentence_'+i] = childArr.join("");
                });
                return obj;
            },
            /*
             * substrのサロゲートペア対応 指摘ワードの抜き出し
             * @return object
             */
            strSubstr : function(startPos,length,str){
                //strを一文字づつarrに格納
                var arr   = proofFactory.convertStringToArray(str);
                arr = arr.slice(startPos,Number(startPos)+Number(length))
                return arr.join("");
            },
            /*
             * objのkeyにstrが含まれているのがあれば結合
             * @return object
             */
            joinSentence : function(obj,str){
                var res = {sentence : ''};
                $.each(obj, function(key,val) {
                    if(key.indexOf(str) != -1) {
                        res[str] += val;
                    }else{
                        res[key] = val;
                    }
                });
                return res;
            },
            /*
             * key==str1の場合str2をkey!=str1の場合key返却
             * @return str
             */
            convertStr : function(key,str1,str2){
                var res = (key == str1) ? str2: key;
                return res;
            },

            /*
             * DOMからパラメータを取得
             * @return object
             */
            getParams : function(key,str1,str2){
                var res = {
                    title         : $(".scene1 .releasettl textarea").val(),
                    subtitle      : $(".scene1 .subttl textarea").val(),
                    head          : $(".scene1 .lead textarea").val(),
                    sentence      : '',
                    //image_caption : proofFactory.getImageCaption()
                };

                // 本文は画像のaltとtitleを削除
                var text = editor.getData().replace(/ alt=".+?"/ig, " alt=\"\"").replace(/ title=".+?"/ig, " title=\"\"");
                res = proofFactory.sliceSentence(res,yahooMaxQuery,text);
                return res;
            },
            /*
             * lengthでソート
             * @return object
             */
            sortByLength : function(data){
                var res = {};
                $.each(data, function (key, arr) {
                    res[key] = arr.sort(function(a,b){return b.url.length - a.url.length});
                });
                return res;
            }
        };

        /*
         レンダリングに関する関数
         ------------------------------------------ */
        var proofView = {
            /*
             *  ローディング表示非表示 cssにて
             */
            showLoader : function(){
                $('.loading_cover.proof').css('height',$('.registmain').height() ).show();
                $('.loading_cover.proof .loading').addClass('spin');
            },
            hideLoader : function(){
                $('.loading_cover.proof').css('height','auto' ).hide();
                $('.loading_cover.proof .loading').removeClass('spin');
            },
            /*
             * 校正エリアの高さをセット
             *
             */
            setAreaHeight : function(){
                $('.js-proofcover.proofcover_title').css('height',$('.releasettl').height());
                $('.js-proofcover.proofcover_subtitle').css('height',$('.subttl').height());
                $('.js-proofcover.proofcover_head').css({'height':$('.lead').height()});
                $('.js-proofcover.proofcover_sentence').css('height',$('#cke_text').height() - 33);
            },

            /*
             * 外部APIで文章校正されたタグをパラメータとして置き換え 直列処理 (リリース削除対象チェック -> 4バイト文字チェック -> リンクチェック)
             * 回数制限回避のため失敗したらYahooDevIDを変更して再度実行
             * @return mixed
             */
            replaceYahooProofHtml : function(_param){
                warning.num = 0;
                var i       = 0;
                $.each(_param, function(key,val) {
                    if(val == '') return true;
                    //Yahoo  テキスト分割して並列で送信
                    get = proofApiService.getProofText({
                        sentence     : val,
                        filter_group : yahooFilterParams['filter_group'],
                        no_filter    : yahooFilterParams['no_filter'],
                        dev_id       : yahooFilterParams['selected_id']
                    }, i).then(function (xml, cnt) {
                        var deferred = $.Deferred();

                        if(xml.indexOf('<Error>') > -1 && yahooFilterParams['selected_id'] == yahooFilterParams['dev_ids'][0]){
                            yahooFilterParams.selected_id = yahooFilterParams['dev_ids'][1];
                            deferred.reject();
                            $('.js-proofreadBtn a').click();
                            return false;
                        };

                        $(xml).find('Result').each(function () {
                            var shitekiword = $(this).find('shitekiword').text(),
                                shitekiinfo = $(this).find('shitekiinfo').text(),
                                startPos    = $(this).find('startPos').text(),
                                length      = $(this).find('length').text(),
                                surface     = ($(this).find('surface').text() == '' && shitekiword == '') ? proofFactory.strSubstr(startPos,length,val) : $(this).find('surface').text();
                            var template    = '<span data-info="' + shitekiinfo + '" class="proof normal" data-proof_text="' + shitekiword + '">' + surface + '</span>';

                            if ($.inArray(surface, whiteList.yahoo) == -1) {
                                _param[key] = proofFactory.wrapText(_param[key], template, startPos, length);
                            }
                        });
                        i++;
                        addLength = 0;
                        deferred.resolve();
                        return deferred.promise();

                    }, function (data) {
                        proofView.hideLoader();
                        showMsgBox('warning', '文章校正で通信エラーが発生しました。');
                    });
                    watch.push(get);
                });
            },

            /*
             * 内部APIで文章校正されたタグを返却 直列処理 (リリース削除対象チェック -> 4バイト文字チェック -> リンクチェック)
             * @return mixed
             */
            getProofHtml : function(_param){
                var deferred = $.Deferred();
                var map = {
                    delete : {warning : 'リンクチェックで通信エラーが発生しました。',                 tpl : ['<span class="proof delete">','</span>']},
                    byte4  : {warning : '4バイト文字チェックで通信エラーが発生しました。',             tpl : ['<span class="proof byte4">','</span>']},
                    link   : {warning : 'リリース削除対象キーワードチェックで通信エラーが発生しました。',tpl : ['<span data-info="'+'リンク切れの可能性があります。'+'" class="proof link">','</span>']}
                };
                //リリース削除対象チェック
                proofApiService.getWarningWord(_param).then(function (data) {
                    if (data['result']) _param = proofFactory.wrapAllMatchWord(_param,data.result_data,'word',map.delete.tpl);
                    //4バイト文字チェック
                    proofApiService.getByte4Word(_param).then(function (data) {
                        if (data['result']) _param = proofFactory.wrapAllMatchWord(_param,data.result_data,'word',map.byte4.tpl);
                        //リンクチェック
                        proofApiService.getDeadLink(_param).then(function (data) {

                            if (data['result']){
                                data = proofFactory.sortByLength(data.result_data);
                                _param = proofFactory.wrapAllMatchWord(_param,data,'url',map.link.tpl);
                            }
                            deferred.resolve(_param);
                        }, function (data) {
                            proofView.hideLoader();
                            showMsgBox('warning', map.delete.warning);
                        });
                    }, function (data) {
                        proofView.hideLoader();
                        showMsgBox('warning', map.byte4.warning);
                    });
                }, function (data) {
                    proofView.hideLoader();
                    showMsgBox('warning', map.link.warning);
                });
                return deferred.promise();
            },

            /*
             * APIレスポンス後のhtmlを表示
             *
             */
            renderResHtml : function(res){
                $.each(res, function (key, val) {
                    $('.js-proofcover.proofcover_' + key ).html(val).fadeIn(190, 'easeInSine');
                });
            }
        };


        /*
         文章校正 event listener
         ------------------------------------------ */
        //文章校正 初期化
        $('.js-proofcover').on({
            "click" : function () {
                proofFactory.init();
            }
        });

        //YahooAPI取得->内部APIを経由して校正結果を取得表示
        $('.js-proofreadBtn a').on({
            'click': function (evt) {
                evt.preventDefault();
                $('html, body').animate({scrollTop: 0}, 'slow', 'easeOutExpo');
                proofView.showLoader();
                $(this).addClass('disabled');

                var _param = proofFactory.getParams();
                proofView.replaceYahooProofHtml(_param);

                //Yahooからのレスポンスデータを並列で全取得後に実行
                $.when.apply($, watch).done(function() {

                    _param = proofFactory.joinSentence(_param,'sentence');
                    proofView.getProofHtml(_param).then(function(_param) {

                        //結果
                        if (warning.num > 0) {
                            warning.msg   = '校正箇所が' + warning.num + 'か所見つかりました。';
                            warning.style = 'warning';
                            proofView.renderResHtml(_param);
                            proofView.setAreaHeight();
                        } else {
                            proofFactory.init();
                            warning.msg   = '校正箇所は見つかりませんでした。';
                            warning.style = 'success';

                        };
                        proofView.hideLoader();
                        showMsgBox(warning.style, warning.msg);

                    });
                });
            }
        });


        /*
         文章校正 ポップアップ
         ------------------------------------------ */
        var proofPopupTimer = false;
        var proofPopupTpls  = {

            normal : '<div class="proof-tooltip">' +
                         '<p class="t-text">{%msg}</p>' +
                         '<p class="b-text" style="display:{%disp};">推奨例：{%proof_text}</p>' +
                      '</div>',

            link   : '<div class="proof-tooltip">' +
                         '<p class="t-text">リンク切れ、もしくはリダイレクトしている可能性があります。URLをお確かめください。</p>' +
                     '</div>',

            delete : '<div class="proof-tooltip">' +
                         '<p>この文言が含まれるプレスリリースは、削除又は修正をさせていただく可能性があります。詳細については' +
                            '<a href="https://tayori.com/faq/89b604344ebb744dbba41f73d4134560c997a743" target="_brank">こちら</a>をご覧ください。' +
                         '</p>' +
                     '</div>',

            byte4  :  '<div class="proof-tooltip">' +
                          '<p class="t-text">文字化けする可能性がある文字です。</p>' +
                      '</div>'
        };


        $('.js-proofcover').on({
            "mouseenter" : function() {
                var _this = ($(this).parent('.proof').size()  > 0)  ? $(this).parent('.proof') : $(this);


                $('.mainwrapper').css('overflow','visible');

                var template = '';
                if (_this.hasClass('normal')) {
                    var msg        = proofReplaceMaster[_this.data("info")]["msg"];
                    var disp       = (_this.data("proof_text") == '') ? 'none': 'block';
                    var proof_text = _this.data("proof_text");

                    template = proofPopupTpls.normal.replace(/{%msg}/g,msg)
                        .replace(/{%disp}/g,disp)
                        .replace(/{%proof_text}/g,proof_text);

                }else if(_this.hasClass('link')){   template = proofPopupTpls.link;
                }else if(_this.hasClass('delete')){ template = proofPopupTpls.delete;
                }else if(_this.hasClass('byte4')) { template = proofPopupTpls.byte4;}

                $('.proof-tooltip').not(_this.find('.proof-tooltip')).fadeOut(190, "easeInSine");
                clearTimeout(proofPopupTimer);

                if(_this.find('.proof-tooltip').length){
                    _this.find('.proof-tooltip').fadeIn(190, "easeInSine");
                }else{
                    _this.append(template);
                    _this.find('.proof-tooltip').delay(400).fadeIn(190, "easeInSine");
                }
            },
            "mouseleave" : function(){
                var _this = $(this);
                proofPopupTimer = setTimeout(function() {
                    _this.find('.proof-tooltip').fadeOut(190, "easeInSine");
                }, 90);
            }

        }, '.proof');

    });
}());