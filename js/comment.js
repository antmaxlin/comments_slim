// JavaScript Document

function reply(e, id){
	if (!e) var e = window.event;
	if (e.keyCode==13) {
		$('#form_'+id).hide();
		var value=encodeURIComponent(document.getElementById("replytext_"+id).value);
		var display=document.getElementById("display_"+id).value;
		if (document.getElementById("privacy0_"+id).checked){
			var level=0;
		} else if (document.getElementById("privacy1_"+id).checked){
			var level=1;
		} else {
			var level=2;
		}
		var ajax1 = new AjaxCall();
		ajax1.divID="replies_"+id;
		ajax1.queryString="id="+id+"&value="+value+"&level="+level+"&display="+display;
		ajax1.url="/comment.php";
		ajax1.httpRequest();
	}
}

function reply_delete(com_id, rev_id){
	var display=document.getElementById("display_"+rev_id).value;
	var ajax1 = new AjaxCall();
	ajax1.divID="replies_"+rev_id;
	ajax1.queryString="com_id="+com_id+"&rev_id="+rev_id+"&display="+display;
	ajax1.url="/delete_comment.php";
	ajax1.httpRequest();
}

function reply_all(rev_id, start){
	var ajax1 = new AjaxCall();
	ajax1.divID="replies_"+rev_id;
	ajax1.queryString="rev_id="+rev_id+"&start="+start;
	ajax1.url="/comment_all.php";
	ajax1.httpRequest();
}