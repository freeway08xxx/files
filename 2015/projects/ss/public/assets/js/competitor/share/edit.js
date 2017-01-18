function doActionM(action, id){
  //入力チェック
  document.form01.action="/basic/edit/m" + action;
  if (id > 0) {
    document.form01.id.value=id;
    document.form01.text.value=$('#mtext_'+id).val();
  }
  document.form01.submit();
}
function doActionD(action, id){
  //入力チェック
  document.form02.action="/basic/edit/t" + action;
  if (id > 0) {
    document.form02.id.value=id;
    document.form02.text.value=$('#ttext_'+id).val();
  }
  document.form02.submit();
}