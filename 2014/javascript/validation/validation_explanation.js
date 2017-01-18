/*

☆バリデーション仕様
次へボタンでバリデーション開始

formの中にidでナンバリングしてあるdivがあり、
表示ブロック内にエラーがなければ次のブロック表示　次のブロックがなければsubmit

<form>
    <div id="q01" class="selected js-enquete_block" style="display: block;">
    <div id="q02" class="selected js-enquete_block" style="display: none;">
    <div id="q03" class="selected js-enquete_block" style="display: none;">
    <div id="q04" class="selected js-enquete_block" style="display: none;">
    <div id="q05" class="selected js-enquete_block" style="display: none;">
    <div id="q06" class="selected js-enquete_block" style="display: none;">
    <div id="q07" class="selected js-enquete_block" style="display: none;">
    <div id="q08" class="selected js-enquete_block" style="display: none;">
    <div id="q09" class="selected js-enquete_block" style="display: none;">
</form>

<div class="t-button">
    <button type="button" class="t-button__default" id="js-next">次へ</button>
</div>



☆エラー文言 judgeArr[] 配列仕様

現在表示されているブロックで不正回答があった場合、
そのname属性が格納される

例　生年月の年、郵便番号が不正回答の時
["year", "postnum"]

ブロックにエラーがないかの判定に使用
配列が空になるとエラー文言のブロックが非表示になり、次のブロックにすすめる



☆エラー番号 allQNumObj{} オブジェクト仕様

現在表示されているブロックの問の数とそれが不正回答かどうかの値を
格納している

key:クラスjs-numberlingの数値
val:Boolean

例　ブロック内　問の数が４つ　２番のみ不正回答
{1: true, 2: false, 3: true, 4: true}

エラー文言のブロックに不正回答の問の番号を表示させる時に使用




☆jsイベントに関わるクラス名と役割
#js-next　　　　　　　  :次へボタンのid　clickイベントでバリデーション
.js-validate           ：バリデーションチェックがeachで発火
.js-required           ：バリデーションチェックがeachで発火
.js-checkboxRequired   ：バリデーションチェックがeachで発火（checkbox）
.js-selectRequired     ：バリデーションチェックがeachで発火（select）
.js-make-next_block    ：次のブロックを表示させる
.js-next-element0,1,2,3 :次のエレメントを0-3の数だけ表示させる（0は表示させない）
.js-hidden_block       ：隠しブロック　選択条件によって表示、非表示を切り分ける
.js-numberling         ：ナンバリングイベントでナンバリングされるクラス
.js-no_count           ：ナンバリングがスキップされる　選択条件によって外れたり付いたりする
.js-make-disabled      ：チェックが入るとthis以外のチェックがはずれdisabledになる
.js-cheack_group       :グループの制御 グループ化され一つでも不正回答があるときはクラスjs-uncompletedが付与される
.js-uncompleted        :グループの制御 グループ内に一つでも不正回答があるときは付与され failedになる
#js-group0,1,2         :グループ内のselectbox重複を削除　良く買い物する店舗で使用
.js_filter_postnum     :郵便番号　固有の出し分けにし使用
.js_filter_year        :生年月日の年　固有の出し分けにし使用
.js-append             :消せる。。
*/
$(function() {

    var currentNumber = 1; //現在のブロックナンバー　次へを押すと一つ増える
    var goToEvent = true; //2度押し防止
    var enqueteBlockLength = $('.js-enquete_block').length;
    var $enqueteBlockId = $('#q0' + currentNumber); //各ブロックごとに番号順のIDがふってある
    var $enqueteNextBlockId = $('#q0' + Number(currentNumber + 1)); //現在から見て次のブロックのID
    var pageSkip = false; //質問内容によってページを一つスキップする場合、trueになる
    var arrErrNum = []; //の配列
    var deleteNum = []; //の配列
    var errWord = [ //エラー文言　条件によって変化
        '未回答',
        '入力内容に誤りがあります',
        '桁数が不正です',
        '半角数字で入力してください',
    ];

    //初期化
    numberling(); //ナンバリングを生成 イベントは515行目
    $('.js-enquete_block').hide(); //ここを非表示にすると全質問が見れます
    $('.js-hidden_element,.js-hidden_block,.js-error').css('display', 'none');
    $enqueteBlockId.show(); //現在の質問ブロックを表示



    //次へボタンのクリックイベント
    $('#js-next').click(function() {

        //goToEvent=true 連続クリックしてないときは
        if (goToEvent) {

            //連続クリック禁止
            goToEvent = false;

            //----------------------初期化----------------------
            var oneTimer = 0; //一回のみの処理のカウント
            var judgeArr = []; //エラーがあった箇所はここに格納。ここが空にならないと次のブロックに切り替わらない
            $enqueteBlockId = $('#q0' + currentNumber); //各ブロックごとに番号順のIDがふってある
            $enqueteNextBlockId = $('#q0' + Number(currentNumber + 1)); //現在から見て次のブロックのID

            //エラー文言とナンバーを消去
            $('.js-err_addtext,.js-error__num').html('');

            //現在のブロックの問の数　表示されていないものは含まない
            var allQNumObj = {};
            var allQNum = $('#q0' + currentNumber + ' .t-enquete__question').not('.js-hidden_block,.js-hidden_element').length;


            /*------------------------------▼フォームバリデーションの内容▼----------------------------------------*/

            /*----------radioボタン入力のチェック----------*/

            //現在のブロックのradioボタンのクラスjs-validateのeachイベントで更にjs-requiredのeachイベント
            $enqueteBlockId.find('.t-enquete__question').find(':radio').filter('.js-validate').each(function() {
                $(this).filter('.js-required').each(function() {

                    //htmlの箇所を限定化
                    var el = $(this);
                    var $thisBlock = el.parents('.t-enquete__question');
                    var errNum = $thisBlock.find('.js-numberling').text().substr(1).slice(0, -1); //問のナンバー抜き出し

                    //表示されているブロックの時は
                    if ($thisBlock.css('display') == 'block') {
                        //チェックが入ってない場合
                        if ($('input[name=' + '"' + el.attr('name') + '"' + ']:checked').size() === 0) {

                            //failedイベントへ　値は引数で
                            failed(el.attr('name'), el, errNum, errWord[0]);

                        } else {
                            //successイベントへ　値は引数で
                            success($(this), errNum);
                        }
                    }
                });
            });

            //radioボタンの選択によって次のブロック表示
            $('.js-make-next_block :checked').each(function() {

                var $el = $(this).parents('.js-enquete_block');
                var $nextEl = $(this).parents('.js-enquete_block').next('.js-enquete_block').find('.js-hidden_block');
                var targetClass = 'js-next-element';
                var thisClass = $(this).attr('class');


                //初期化
                $nextEl.css('display', 'none').find('.t-select').css('display', 'none');
                $nextEl.find('.js-numberling').addClass('js-no_count');

                if (thisClass.search(targetClass) >= 0) {

                    var beginNum = thisClass.search(targetClass) + targetClass.length;
                    var endNum = beginNum + 1;
                    var nextBlockNum = thisClass.substring(beginNum, endNum);

                    if (nextBlockNum != 0) {
                        $nextEl.find('.js-numberling').removeClass('js-no_count');
                        for (var i = 0; i < nextBlockNum; i++) {
                            $nextEl.css('display', 'block').find('.t-select').eq(i).css('display', 'block').find('select').addClass('js-selectRequired');
                            $nextEl.css('display', 'block').find('.t-select').eq(i).find(':radio').addClass('js-validate js-required');
                        }
                    }
                }
                numberling(); //ナンバリングを生成
            });


            //*---------selectboxのチェック---------*/
            $enqueteBlockId.find('.js-selectRequired').each(function() {
                //表示されているブロックの時は
                var $thisBlock = $(this).parents('.t-enquete__question');
                var el = $(this);
                var errNum = el.parents('.t-enquete__question').find('.js-numberling').text().substr(1).slice(0, -1);

                if ($thisBlock.css('display') == 'block') {

                    // "選択してください"の時
                    if (el.prop('selectedIndex') === 0) {

                        //appendするかしないか
                        if (el.hasClass('js-append')) {
                            failed(el.attr('name'), el, errNum, errWord[0], 'js-append');

                        } else {
                            failed(el.attr('name'), el, errNum, errWord[0]);
                        }

                    }

                    //successへ
                    else {
                        success($(this), errNum);
                    }
                }
            });

            /*---------selectbox 次のブロック表示チェック---------*/
            $('.js-select_delete select').each(function() {
                selectDelete($(this));

            });



            //*---------cheackboxのチェック---------*/
            $enqueteBlockId.find('.js-checkboxRequired').each(function() {

                //表示されているブロックの時は
                var $thisBlock = $(this).parents('.t-enquete__question');
                var el = $(this);
                var errNum = $(this).parents('.t-enquete__question').find('.js-numberling').text().substr(1).slice(0, -1);

                if ($thisBlock.css('display') == 'block') {

                    //どれかにチェックがついているか
                    if ($(':checkbox:checked', this).size() === 0) {

                        failed(el.attr('name'), el, errNum, errWord[0]);

                    } else {
                        success($(this), errNum);
                    }
                }
            });
            //*---------cheackbox　次のブロック表示、非表示---------*/
            $enqueteBlockId.find('.js-checkboxRequired input').each(function() {
                var isChecked = $(this).prop('checked');
                var targetClass = 'js-next_number';
                var thisClass = $(this).attr('class');
                var beginNum = thisClass.search(targetClass) + targetClass.length;
                var endNum = beginNum + 1;
                var nextBlockNum = thisClass.substring(beginNum, endNum);
                var $nextEl = $(this).parents('.js-enquete_block').next('.js-enquete_block');
                var nextElLength = $nextEl.children('.js-hidden_element').length;
                var visibleNum = 0;

                //次のブロックを表示
                function nextBlock(el) {
                    $nextEl.find('.js-hidden_element').eq(nextBlockNum).css('display', 'block');
                    $nextEl.find('.js-numberling').eq(nextBlockNum).removeClass('js-no_count');
                    checkPageSkip();
                }

                //次のブロックを隠す
                function nextHide() {
                    $nextEl.find('.js-hidden_element').eq(nextBlockNum).css('display', 'none');
                    $nextEl.find('.js-numberling').eq(nextBlockNum).addClass('js-no_count');
                    checkPageSkip();
                }

                //ページをスキップするかどうか
                function checkPageSkip() {
                    if ($nextEl.hasClass('js-page_skip')) {
                        for (var i = 0; i <= nextElLength; i++) {
                            if ($nextEl.children('.js-hidden_element').eq(i).css('display') == 'block') {
                                visibleNum = visibleNum + 1;
                            }
                        }
                        if (visibleNum == 0) {
                            pageSkip = true;
                        } else {
                            pageSkip = false;
                        }
                    }
                }

                //チェックが入る
                if (isChecked) {
                    nextBlock($(this));

                }
                numberling(); //ナンバリングを生成
            });

            //*---------cheackbox　次のブロックdisabledチェック---------*/
            $enqueteNextBlockId.find('.js-make-disabled').each(function() {
                var $el = $(this).parents('.t-enquete__question');
                if ($(this).is(':checked')) {
                    makeDisabled($el, $(this));
                }
            });

            /*----------textbox入力のチェック----------*/
            $enqueteBlockId.find(':text').filter('.js-required').each(function() {

                var el = $(this);
                var $thisBlock = el.parents('.t-enquete__question');
                var errNum = $thisBlock.find('.js-numberling').text().substr(1).slice(0, -1);
                var textVal = el.val();
                var filterId = $thisBlock.attr('id');
                var now = new Date();
                var nowYear = now.getFullYear();

                //表示されているブロックの時は
                if ($thisBlock.css('display') === 'block') {

                    if (textVal === '') {

                        failed(el.attr('name'), el, errNum, errWord[0]);

                    } else {
                        /*----------------固有設定フィルター---------------*/

                        // "生年月日"
                        if (filterId == 'js_filter_year') {


                            if (textVal.match(/[^0-9]/)) {
                                failed(el.attr('name'), el, errNum, errWord[1]);
                            }
                            //1913以下または現年以上
                            else if (1913 >= textVal || nowYear <= textVal) {

                                failed(el.attr('name'), el, errNum, errWord[1]);
                            } else {
                                success($(this), errNum);
                            }

                            //"郵便番号"　3桁以上7桁以下
                        } else if (filterId == 'js_filter_postnum') {

                            if (textVal.match(/[^0-9]/) || !(textVal.length >= 3 && textVal.length <= 7)) {
                                failed(el.attr('name'), el, errNum, errWord[1]);

                            } else {

                                success($(this), errNum);

                            }
                        }
                    }
                }
            });

            //*---------グループのチェック---------*/
            //グループ内にクラスjs-uncompletedがあるときはfailed
            $enqueteBlockId.find('.js-uncompleted').each(function() {
                var el = $(this);
                var errNum = $(this).parents('.t-enquete__question').find('.js-numberling').text().substr(1).slice(0, -1);
                failed('', '', errNum, errWord[0]);
                $('.js-uncompleted').parents('.t-enquete__question').find('.js-enquete')
                    .addClass('enquete__error');
            });


            //*---------バリデーションひっかかった---------*/
            function failed(failedPlace, el, errNum, errWord, place) {

                    //第五引数がない場合は''
                    if (typeof place === 'undefined') {
                        place = '';
                    }

                    if (failedPlace !== '') {
                        judgeArr.push(failedPlace);
                    }

                    if (el !== '') {
                        //グループの場合はクラスjs-cheack_groupにクラスjs-uncompletedを付与
                        el.parents('.js-cheack_group').addClass('js-uncompleted');

                        //appendするか
                        if (place === 'js-append') {
                            el.parents('.t-enquete__question').find('.js-enquete')
                                .addClass('enquete__error')
                                .find('.js-err_addtext').addClass('err_word').html('<span class="' + place + '">' + errWord + '</span>');
                            //書き換えるか
                        } else {
                            el.parents('.t-enquete__question').find('.js-enquete')
                                .addClass('enquete__error')
                                .find('.js-err_addtext').addClass('err_word').html('<span class="' + place + '">' + errWord + '</span>');
                        }
                    }

                    //オブジェクトにfalseを格納
                    if (errNum !== undefined && errNum !== '') {
                        allQNumObj[errNum] = false;

                    }

                    //一度のみの処理　エラーブロックを表示してスクロールトップへ
                    if (oneTimer === 0 && failedPlace != '') {
                        $('.js-error').css('display', 'block');
                        $('body').animate({
                            scrollTop: 0
                        }), '0';
                        oneTimer++;
                    }
                    goToEvent = true;


                    //エラー箇所確認
                    console.log(judgeArr);
                    console.log(allQNumObj);


                }
                //*---------固有バリデーションぬけた---------*/
                //successイベント

            function success(el, errNum, place) {

                //allQNumObjのerrNum番にtrueをいれる
                allQNumObj[errNum] = true;

                //グループ化のところはクラスjs-uncompletedをはずす
                el.parents('.js-cheack_group').removeClass('js-uncompleted');

                //クラスenquete__errをはずす
                el.parents('.t-enquete__question').find('.js-enquete')
                    .removeClass('enquete__error');

                //エラーNum削除
                for (i = 0; i < arrErrNum.length; i++) {

                    //arrErrNum配列内のerrNum番を削除
                    if (arrErrNum[i] == errNum) {
                        arrErrNum.splice(i, 1);
                    }
                }

                //エラーの配列が空になったらエラー文言表示ブロックを非表示
                if (judgeArr == '') {
                    $('.js-error').css('display', 'none');
                }
            }


            //*---------全体エラーチェック---------*/

            //エラー番号表示
            //android2.1以下は表示なし
            if (!(UA.deviceType == 'android' && UA.osVersion <= 2.1)) {

                //objに問の数が全部揃ったら
                if (Object.keys(allQNumObj).length >= allQNum) {

                    function objectSort(object) {
                        //戻り値用新オブジェクト生成
                        var sorted = {};
                        var array = [];
                        //オブジェクトのキーだけ配列に格納
                        for (key in object) {
                            //指定された名前のプロパティがオブジェクトにあるかどうかチェック
                            if (object.hasOwnProperty(key)) {
                                //if条件がtrueならば，配列の最後にキーを追加する
                                array.push(key);
                            }
                        }
                        //配列のソート　番号順に並び替え
                        array.sort();
                        for (var i = 0; i < array.length; i++) {
                            sorted[array[i]] = object[array[i]];
                        }
                        //戻り値にソート済みのオブジェクトを指定
                        return sorted;
                    }

                    objectSort(allQNumObj);
                    for (var i in allQNumObj) {

                        if (allQNumObj[i] == false) {
                            $('.js-error__num').append('Q' + i + '.');

                        }
                    }
                }
            }

            //エラーブロックが表示されているかどうかで最終判定
            if ($('.js-error').css('display') == 'block') {
                arrErrNum = [];
                allQNumObj = {};
                return false;

                //エラーなし
            } else {
                // 次IDを抽出
                var nextNumber = currentNumber + 1;

                //pageSkip=trueの時はnextNumber,currentNumberに+1
                if (pageSkip) {
                    nextNumber = nextNumber + 1;
                    currentNumber = currentNumber + 1;
                }
                pageSkip = false;

                // 10以下は0を付ける
                if (nextNumber < 10) {
                    nextNumber = '0' + nextNumber.toString();
                }
                // IDを次IDに置換
                var next_id = 'q' + nextNumber.toString();


                // 次ブロックを表示
                setTimeout(function() {

                    currentNumber++;
                    //一度初期化
                    $('.js-enquete_block').hide();

                    //次のブロックに要素があるとき
                    if ($('#' + next_id).is('*')) {

                        //ラストの時はサブミット
                        if (Number(next_id.slice(1)) == enqueteBlockLength) {

                            $('#js-next').get(0).type = 'submit';
                        }


                        $('.js-progress').html('<p class="page0' + currentNumber + '"><em>' + currentNumber + '0%</em></p>');

                        goToEvent = true;
                        $('body').animate({
                            scrollTop: 0
                        }), '0';
                        setTimeout(function() {
                            $('#' + next_id).show("slide", {
                                direction: "right"
                            });
                        }, 400);
                    }
                }, 300);
            }
        }
    });

    /*---------selectbox重複を削除---------*/
    /*
    仕様
    選択されたものはほかのセレクトボックスでは非表示にする（span style="display:none;"で囲って隠す）

    keyとvaluはfirst-enquete.jsで使うためローカルストレージに格納
    例valueが上から3,2,1の場合

    Local Storage(key:value)
    shopping0:3
    shopping1:2
    shopping2:1

    */



    $('.js-select_delete select').change(function() {
        selectDelete($(this));
    });

    function selectDelete(that) {
        var $el = that.parents('.t-enquete__question');
        var groupNum = that.parents('.js-cheack_group').attr('id').slice(8);

        for (var i = 0; i <= 2; i++) {
            deleteNum[i] = $('#js-group' + i + ' option:selected').val();

            //first-enquete.jsで使うためローカルストレージに格納
            localStorage.setItem('shopping' + i, deleteNum[i]);
        }


        //初期化
        $('.js-wrap_hide').find('option').unwrap();

        //チェック
        for (var i = 0; i <= 2; i++) {
            //選択してください以外は
            if (deleteNum[i] > 0) {
                hideOption(i);
            }
        }

        //hide処理
        function hideOption(selfId) {
            $el.find('option[value=' + deleteNum[selfId] + ']')
                .not('#js-group' + selfId + ' option,option[value=0]')
                .wrap('<span style="display: none;" class="js-wrap_hide" />');
        }

    }


    /*---------selectedIndexで出しわけ---------*/
    $('#js-filter-occupation').change(function() {

        var selectedIndex = $(this).prop('selectedIndex');
        if (selectedIndex >= 7 || selectedIndex === 0) {
            $('.js-disabled-category').prop('disabled', true)
                .val(0)
                .removeClass('js-selectRequired');
        } else {
            $('.js-disabled-category').prop('disabled', false)
                .addClass('js-selectRequired');
        }

    }).change();



    //*---------全体モジュール---------*/
    //クラスjs-make-disabledをクリックで
    $('.js-make-disabled').on('click', function() {
        var $el = $(this).parents('.t-enquete__question');
        //チェックの状態
        if ($(this).is(':checked')) {
            makeDisabled($el, $(this))
                //チェックが外れた状態
        } else {
            $el.find(':checkbox').not($(this)).prop({
                'disabled': false
            });
        }
    });

    //自分以外のcheckboxのチェックを外し、disabledにする
    function makeDisabled($el, that) {
        $el.find(':checkbox').not(that).prop({
            'disabled': true,
            'checked': false
        });
    }



    //submit無効　（input type="text"だとsubmitボタンが入力キーにでてしまうので）
    $('input').keypress(function(ev) {
        if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
            return false;
        } else {
            return true;
        }
    });


    //ナンバリング生成　クラスjs-numberlingに番号を付ける（js-no_countがある場合はカウントされない）
    function numberling() {
        var $el = $('.js-numberling').not('.js-numberling.js-no_count');
        var count = $el.length;
        for (var i = 1; i <= count; i++) {
            $el.eq(i - 1).html('Q' + i + '.');
        }
    }


});