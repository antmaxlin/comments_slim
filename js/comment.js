// JavaScript Document
//AJAX Class - Set url, divID, and queryString to use. Call with httpRequest.
function AjaxCall() {
	this.req = null;
	this.url = null;
	this.divID = null;
	this.method = 'POST';
	this.async = true;
	this.queryString = null;
	this.visible= false;
	
	this.initReq = function (){
		var self = this;
		this.req.open(this.method,this.url,this.async);
		this.req.onreadystatechange= function() {
			var obj=document.getElementById(self.divID);
			if(self.req.readyState == 4){	
				if(self.req.status == 200){
					obj.innerHTML=self.req.responseText;
					if (self.visible) obj.style.visibility="visible";
					self.onresult();
				} else {
					//alert(self.req.status+"A problem occurred with communicating between the XMLHttpRequest object and the server program.");
				}
			}
		}
		this.req.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
		this.req.send(this.queryString);
	}
	
	this.httpRequest = function (){
		//Mozilla-based browsers
		if(window.XMLHttpRequest){
			this.req = new XMLHttpRequest();
		} else if (window.ActiveXObject){
			this.req=new ActiveXObject("Msxml2.XMLHTTP");
			if (!this.req){
				this.req=new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		//the request could still be null if neither ActiveXObject
		//initializations succeeded
		if(this.req){
			this.initReq();
		}  else {
			//alert("Your browser does not permit the use of all "+"of this application's features!");
		}
	}
	
	this.onresult = function (){
		//Do something after completion here.
	}
}

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
		ajax1.url="/add_comment.php";
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