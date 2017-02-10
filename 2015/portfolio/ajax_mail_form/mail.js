$(function(){

    var $submit = $("#submit");
	
    //conformクリック TEST2
    $("#conform").on('click', function() {
        var is_disabled = $(this).is(':checked') ? false:true;
        $submit.prop('disabled', is_disabled);
    });

    var fomElm = $("#sendmail"); 
    fomElm.submit(function(event){ 
        event.preventDefault(); 

        $submit.prop('disabled', true);
        var data = fomElm.serializeArray(); 

        //ajax 
        json = JSON.stringify(data); 
        $.ajax({
            url:  $(this).attr("action"),
            type: $(this).attr("method"),
            data: {data:  json},
            success: function(data){
                if(data){
                    alert("メールが送信されました。");
                    $(fomElm).find("textarea, :text, select").val("").end().find(":checked").prop("checked", false);
                    location.href = "index.html";

                }else{
                    alert("メール送信できませんでした。しばらくたってからご利用ください。");
                    location.href = "index.html";
                }
            },error: function(data){
                alert("メール送信できませんでした。しばらくたってからご利用ください。");
                location.href = "index.html";
            }
        })
    });   
});
