$(function() {


    /*
    inner link
    ----------------------------------------------------------------------*/
    $('.nav--elm a[href^=#]').click(function() {
          var head_size = $('.g_navi').height();
          var speed = 400;
          var href= $(this).attr("href");
          var target = $(href == "#" || href == "" ? 'html' : href);
          var position = target.offset().top - head_size;
          $('body,html').animate({scrollTop:position}, speed, 'swing');
          return false;
    });

    /*
    sns　insert self link
    ----------------------------------------------------------------------*/
    $('.js-insert-selflink').each(function() {
        var self = window.location.href;
        var base = $(this).attr('href');
        $(this).attr('href',base + self);
    });

    /*
    window サイズクラス
    ----------------------------------------------------------------------*/
    function setSizeClass(){
        var small  = 780
        var w = $(window).width();

        if(w <= small){
            $('body').addClass('small').removeClass('large'); 

        }else{
            $('body').addClass('large').removeClass('small'); 
        }
    }
    setSizeClass();
    var resizeTimer  = false;
    $(window).resize(function() {
        if (resizeTimer !== false) {
            clearTimeout(resizeTimer);
        }
        resizeTimer = setTimeout(function() {
            setSizeClass();
        },200);
    });




    /*
    モーダルイベントを解除してアンカーリンク
    ----------------------------------------------------------------------*/

    $(document).on('mouseenter','.js-link',function(){
        alert(!$('body').hasClss('small'))
        if( !$('body').hasClss('small')) {
            if($(this).attr('href')  != '') $(this).css('text-decoration','underline')
        }
    });
    $(document).on('mouseleave','.js-link',function(){
        if( !$('body').hasClss('small')) {
            $(this).css('text-decoration','none')
        }
    });

    $(document).on("click", ".js-link",function(evt){
        if( !$('body').hasClss('small')) {
            window.open().location.href = $(this).attr('href');
            $("#lean_overlay").click();
            evt.preventDefault();
        }
        }
    );


    /*
    bg-size:cover imgTag
    ----------------------------------------------------------------------*/
    function fixImageSize(elm){
        $(elm).ready(function() {
            $(elm).each(function() {
                //set size
                var th = $(this).height(),//box height
                    tw = $(this).width(),//box width
                    im = $(this).children('img'),//image
                    ih = im.height(),//inital image height
                    iw = im.width();//initial image width
                if (ih>iw) {//if portrait
                    im.addClass('ww').removeClass('wh');//set width 100%
                } else if(ih == iw && ih <= 200){
                    im.addClass('square')//set width 100%
                } else {//if landscape
                    im.addClass('wh').removeClass('ww');//set height 100%
                }
                //set offset
                var nh = im.height(),//new image height
                    nw = im.width(),//new image width
                    hd = (nh-th)/2,//half dif img/box height
                    wd = (nw-tw)/2;//half dif img/box width
                if (nh<nw) {//if portrait
                    im.css({marginLeft: '-'+wd+'px', marginTop: 0});//offset left
                } else {//if landscape
                    im.css({marginTop: '-'+hd+'px', marginLeft: 0});//offset top
                }
            });
        });
    }



    /*
    データを取得
    ----------------------------------------------------------------------*/
    var model = {
        works : {
            dataUrl  : 'assets/data/works/data.json',
            template :  '<div class="detail selection page-{selection}">'          +
                            '<div class="view view-first">'                        +
                                '<img src="assets/images/works/{img}">'            +
                                '<div class="mask js-modal" href="#modal-works-body_{i}">' +
                                    '<h2 class="view--title"><a class="js-link" href="{url}">{name}</a></h2>'          +
                                    //'<p>'                                          +
                                        //'<span class="sub">URL</span>'             +
                                        //'<span class="elm url"><a href="{url}">{url}</a></sapn>' +
                                    //'</p>'                                         +
                                    '<p>'                                          +
                                        '<span class="sub">Client</span>'          +
                                        '<span class="elm">{Client}</sapn>'        +
                                    '</p>'                                         +
                                    '<p>'                                          +
                                        '<span class="sub">Agency</span>'          +
                                        '<span class="elm">{Agency}<span>'         +
                                    '</p>'                                         +
                                    '<p>'                                          +
                                        '<span class="sub">JOB</span>'             +
                                        '<span class="elm">{JOB}</span>'           +
                                    '</p>'                                         +
                                    '<p>'                                          +
                                        '<span class="sub">Date</span>'            +
                                        '<span class="elm">{Date}</span>'          +
                                    '</p>'                                         +
                                '</div>'                                             +
                            '</div>'                                               +
                        '</div>' + 
                        '<div id="modal-works-body_{i}" class="modal-works-body"><img src="assets/images/works/{img}"></div>'

        },
        member : {
            dataUrl  : 'assets/data/member/data.json',
            template : '<div class="detail selection page-{selection}">'       +
                            '<div class="view view-first">'                    +
                                '<img src="assets/images/member/{img}">'       +
                                '<div class="mask js-modal" href="#modal-member-body_{i}">'                           +
                                    '<h2>{name}</h2>'                          +
                                    '<p>'                                      +
                                        '<span class="job">{job}</span>'       +
                                        '<span class="from">{from}</span><br>' +
                                        '<span class="text">{text}</span>'     +
                                    '</p>'                                     +
                                '</div>'                                       +
                            '</div>'                                           +
                        '</div>' + 
                        '<div id="modal-member-body_{i}" class="modal-member-body"><img src="assets/images/member/{img}"></div>'


        }
    }


    var view = {
        // サーバからarrayで返ってくる
        render: function(data,type){
             var items       = 0;
             var selection   = 0;
             var itemsOnPage = 6;
             var views = "";
             var imgPreloader = new Image();
             _.each(data, function(row,i) {
                selection　= Math.floor( i / itemsOnPage) + 1;
                
                if(type == 'works' ){

                    var content = model[type].template
                                  .replace(/{name}/g      , row.name)
                                  .replace(/{url}/g       , row.url)
                                  .replace(/{img}/g       , row.img)
                                  .replace(/{Client}/g    , row.Client)
                                  .replace(/{Agency}/g    , row.Agency)
                                  .replace(/{JOB}/g       , row.JOB)
                                  .replace(/{Date}/g      , row.Date)
                                  .replace(/{selection}/g , selection)
                                  .replace(/{i}/g         , i)
                }else if(type == 'member' ){
                    var content = model[type].template
                                  .replace(/{name}/g      , row.name)
                                  .replace(/{img}/g       , row.img)
                                  .replace(/{job}/g       , row.job)
                                  .replace(/{from}/g      , row.from)
                                  .replace(/{text}/g      , row.text)
                                  .replace(/{selection}/g , selection)
                                  .replace(/{i}/g         , i)
                }

                items = i;
                views += content;
                imgPreloader.src = 'assets/images/' + type + '/' + row.img;
            });

            $('#' + type + ' .js-details').append(views);
            imgPreloader.onload = function() {
                fixImageSize('.'+type+' .view');
                
                $("#paging-" + type).pagination({
                    items          : items + 1,
                    itemsOnPage    : itemsOnPage,
                    prevText       : '<',
                    nextText       : '>',
                    displayedPages : 6,//pagerの数
                    cssStyle       : type,
                    onPageClick    : function(currentPageNumber,e){
                        showPage(currentPageNumber,this.cssStyle);
                    }
                });

                function showPage(pageNumber,type){
                    $('#' + type + ' .selection').hide();
                    $('#' + type + ' .page-' + pageNumber).fadeIn(500);
                    fixImageSize('#' + type + ' .view');
                };

                $('.js-modal').leanModal({
                    top         : 50,             // モーダルウィンドウの縦位置を指定
                    overlay     : 0.7,            // 背面の透明度 
                    closeButton : ".modal_close"  // 閉じるボタンのCSS classを指定
                });
            }
        }
    }


    var service = {
        getData: function(url){
            var defer   = $.Deferred();
            var promise = $.ajax({
                url         : url,
                dataType    : 'json',
                contentType : "application/json; charset=utf-8",
                success     : defer.resolve,
                error       : defer.reject
            });
            return promise;
        }
    }


    //works
    service.getData(model.works.dataUrl).then(function(data){
        console.log(data)
        view.render(data.works,'works');
    });


    //member
    service.getData(model.member.dataUrl).then(function(data){
        view.render(data.member,'member')
    });


    /*
    メール送信
    ----------------------------------------------------------------------*/
    var $submit = $("#submit");
    var $fomElm = $("#sendmail"); 
    $fomElm.submit(function(event){ 

        event.preventDefault(); 
        $submit.prop('disabled', true);
        data = $fomElm.serializeArray(); 

        //ajax 
        json = JSON.stringify(data); 
        $.ajax({
            url:  'sendmail.php',
            type: 'POST',
            data: {data:  json},
            success: function(data){
                if(data){
                    alert("メールが送信されました。");
                    $fomElm.find("textarea, :text, select").val("").end().find(":checked").prop("checked", false);
                            location.href = "index.php";

                    }else{
                        alert("メール送信できませんでした。しばらくたってからご利用ください。");
                        location.href = "index.php";
                    }
                },
            error: function(data){
                    alert("メール送信できませんでした。しばらくたってからご利用ください。");
                    location.href = "index.php";
                }
        })
    }); 




});

