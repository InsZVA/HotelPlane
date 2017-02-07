var CSAPI = new Object;
var Customs = {};
var run_Custom;

function Custom(data) {
	var temp = this;
	this._id = data.user_id;
	this.sessionId = data._id;
	this.username = data.username;
	this.lastTimeStamp = data.create_time;
	this._data = Array();
	this.Count = 0;

	
	this.refresh();
	return this;
}

Custom.prototype.choose = function() {
	if(run_Custom == this)
		return;
	$(".chatsheet").empty();
	run_Custom = this;
	this.Count = 0;
	$("span.chosen").removeClass("chosen");
	$("span[uid='"+this._id+"']").addClass("chosen");
	$("#chat").find(".title").html("与 "+run_Custom.user_name+" 的通话");
	this.updateChat();
}


Custom.prototype.refresh = function() {
	var temp = this;
	createPostMethod("getSessionNewestMessages",
	{
			data:{"sessionId":temp.sessionId,"userId":temp._id,"lastTime":temp.lastTimeStamp},
			success:function(data) {

					if(data.length <= 0) {return;}
					if(!(data instanceof Array)) {return;}
					temp._data = temp._data.concat(data.reverse());
					temp.lastTimeStamp = data[data.length-1].create_time;
					if(!(run_Custom instanceof Custom)) {return;}
					if(run_Custom._id == temp._id) { temp.updateChat(); }
			},
			error:function(data) {
			}
		});
	if(temp._data.length - temp.Count > 99) {
		$("span[uid='"+this._id+"']").find("p.count").html(99);
	} else {
		$("span[uid='"+this._id+"']").find("p.count").html(temp._data.length - temp.Count);
	}
	
	this.loop = setTimeout(function() {temp.refresh();},CSAPI.refreshCount);
	
};

Custom.prototype.updateChat = function() {
	var temp = this;
	while(temp.Count + 1 <= temp._data.length)
	{

		$(".chatsheet").append(InfoGenerate(temp._data[temp.Count]));
		temp.Count++;
		
	}
	$("img[rid]").each(function() {
		var temp = $(this);
		$.ajax({
			url:"http://api.xszlv.com/api/ServerPicD.php",
			type:"POST",
			data:{"mediaId":temp.attr("rid")},
			success:function(res) {
				temp.attr("src",res);
				temp.removeAttr("rid");
			}
		});
	});
	$("span.custom[uid='"+this._id+"']").find("p.count").html(0);
	$(".chatsheet").scrollTop($(".chatsheet")[0].scrollHeight);
	
}

Custom.prototype.close = function() {
	var temp = this;
	createPostMethod("closeSession",{data:{sessionId:temp.sessionId},success:function(data) {
		delete Customs[temp._id];
		$("span.custom[uid='"+temp._id+"']").remove();
		if(run_Custom == temp) {
			$(".chatsheet").empty();
			run_Custom = null;
			$("#chat").find(".title").html("聊天窗口");
		}
		return;}});
}

var menuList = [
{name:"客服系统",url:""},
{name:"优惠券",url:""},
{name:"系统设置",url:""},
{name:"...",url:""},
];

$.each(menuList,function() {
	var temp = $("<a>"+this.name+"</a>");
	var url = this.url;
	temp.click(function() {window.location.href = url;});
	$(".menu").append(temp);
});

$(".menu").find("a:first").addClass("chosen");

CSAPI.initialize = function() {
	if (localStorage.userID == undefined) {
		alert("请先登录！");
		location.href= "login.html";
		return;
	}
	this._id = localStorage.userID;
	$.ajax({
		url:"http://api.xszlv.com/STChat/api/api.php",
		data:JSON.stringify({
			"requestMethod":"getWaiterData",
			"userId": "1",
			"token":"test"
		}),
		type:"post",
		async: "false",
		success:function(data) {
			this._data = {
				"_id": data._id,
				"name": data.waiterName,
				"headpic": data.avatar
			}
		}
	});/*
	this._data = {
		"_id":this._id,
		"name":"安妮小郡主",
		"headpic":"sc/1.png"
	};*/
	$("#waiterName").html(this._data.name);
	$("#waiterHeadpic").attr("src",this._data.headpic);
	this.Count = 0;
	this.TCount = 0;
	this.headpic = "sc/1.png";
	this.refreshCount = 1000;
	this.Waitcustoms = {};
	$.ajax({
		url:"http://api.xszlv.com/api/api.php",
		data:JSON.stringify({
			"requestMethod":"getAvatar",
			"userId":CSAPI._id,
			"token":"test"
		}),
		type:"post",
		success:function(avatar) {
			CSAPI.headpic = avatar.avatar;
		}
	});
	this.create();
	this.listen();
}

CSAPI.treatCustom = function(uid) {
	var temp = this;
	createPostMethod("getUserLastSession",
		{
			data:{"userId":uid},
			success:function(data1) {
				createPostMethod("sessionSetWaiter",
					{
							data:{"sessionId":data1._id,"waiterId":temp._id},
							success:function(data) {

							},
							error:function(data) {
								
							}
						});
					},
			error:function(data) {
						warn("连接失败！");
					}
			});
	
}

CSAPI.updateCustomInfo = function(sessionId,uid) {
	var temp = this;
	
	createPostMethod("getSessionData",
	{
			data:{"sessionId":sessionId,"userId":uid},
			success:function(data) {
				
				if(data.state == -1) {
					temp.Count++;
					if($(".WaitC").find("span[uid='"+data.user_id+"']").length <= 0)
					{
						var relatedDom = createUserSpan(data);
						var dom = $("<a></a>");
						dom.html("接待");
						dom.click(function() {temp.treatCustom(data.user_id);});
						relatedDom.append(dom);
						$(".WaitC").append(relatedDom);
					}
					
				} else if (data.state == 0 && data.waiter_id == temp._id) {
					temp.TCount++;
					if($(".WaitC").find("span[uid='"+data.user_id+"']").length > 0)
					{
						$("span[uid='"+data.user_id+"']").remove();
					}
					if($(".TalkC").find("span[uid='"+data.user_id+"']").length <= 0)
					{
						var relatedDom = createUserSpan(data);
						relatedDom.click(function() {Customs[data.user_id].choose();});
						var dom = $("<a></a>");
						dom.html("完成");
						dom.click(function() {Customs[data.user_id].close();});
						relatedDom.append(dom);
						$(".TalkC").append(relatedDom);
					}
					
				} else {
					
				}
			},
			error:function(data) {
				
			}
		});
}

function createUserSpan(data) {
	var relatedDom = $("<span class='custom'></span>");
	if(data.addition.vip) {relatedDom.addClass("vip");}
	
						relatedDom.attr("uid",data.user_id);
						relatedDom.attr("cTime",data.create_time);
						relatedDom.append("<img>");
						relatedDom.append("<p class='uid'>"+data.user_id+"</p>");
						relatedDom.append("<p class='uname'>"+data.user_name+"</p>");
						relatedDom.append("<p class='ctime'>"+data.create_time+"</p>");
						relatedDom.append("<p class='count'>0</p>");

						$.ajax({
							url:"http://api.xszlv.com/api/api.php",
							data:JSON.stringify({
								"requestMethod":"getUserData",
								"userId":data.user_id,
								"token":"test"
							}),
							type:"post",
							success:function(avatar) {
								relatedDom.find("img").attr("src",avatar.avatar);
								relatedDom.find("p.uname").html(avatar.username);
							}
	
						});
						$.ajax({
							url:"http://api.xszlv.com/api/api.php",
							data:JSON.stringify({
								"requestMethod":"isVIP",
								"userId":data.user_id,
								"token":"test"
							}),
							type:"post",
							success:function(res) {
								if(res.VIP) {relatedDom.addClass("vip")};
							}
	
						});
						if(data.addition.checkId) {
								var dom = $("<a></a>");
								dom.html("订单");
								dom.click(function() {window.open(data.addition.checkId);});
								relatedDom.append(dom);
						}
						return relatedDom;
}

CSAPI.refreshCustoms = function() {
	var temp = this;
	this.Count = 0;
	this.TCount = 0;
	$.each(this.Waitcustoms,function(name,value) {
		temp.updateCustomInfo(value,name);
	});
	$.each(Customs,function(name,value) {
		temp.updateCustomInfo(value.sessionId,name);
	});
	$("#Wait").find(".title").html("等待中顾客("+$(".WaitC").children("span").length.toString()+")");
	$("#Talk").find(".title").html("接待中顾客("+$(".TalkC").children("span").length.toString()+")");
}

CSAPI.listen = function() {
	var temp = this;
	createPostMethod("getReadySessions",
		{
			data:{},
			success:function(data) {
				$.each(data,function() {
					temp.Waitcustoms[this.user_id] = this._id;
				});
			},
			error:function(data) {
				warn("连接失败");
			}
		});

	createPostMethod("getWaiterOpenSessions",
		{
			data:{waiterId:temp._id},
			success:function(data) {
				$.each(data,function() {
					if(!Customs.hasOwnProperty(this.user_id)) { Customs[this.user_id] = new Custom(this);}
				});
			},
			error:function(data) {
				warn("连接失败");
			}
		});
	this.refreshCustoms();
	setTimeout("CSAPI.listen()",this.refreshCount);
}

CSAPI.create = function() {
	var temp = this;
	createPostMethod("newWaiter",
	{
			data:{"data":JSON.stringify(temp._data)},
			success:function(data) {
				
			},
			error:function(data) {
				warn("连接失败");
				return 0;
			}
		});
}

CSAPI.postMessage = function(check) {
	if(!(run_Custom instanceof Custom))
	{
		warn("没有选中用户");
		return;
	}
	var temp = this;
	var content = $("input[name='message']").val();
	/* 内容合法性检查 */
	check = check || false;
	if(!check)
	{
		content = VerifyPost(content);
	}
	
	createPostMethod("waiterSendMessage",
	{
			data:{"waiterId":temp._id,"sessionId":run_Custom.sessionId,"type":"text","content":content},
			success:function(data) {
				if(data.code == -1) {
					warn("发送失败！");
				} else {
					$("input[name='message']").val("");
				}
			},
			error:function(data) {
				warn("连接失败！");
			}
		});
}

CSAPI.initialize();


$("a.submit").click(function() {
	CSAPI.postMessage();
});


function InfoGenerate(data) {
	var avatartemp = (data.sender == 1 ? $("img#waiterHeadpic").attr("src") : $("span[uid='"+run_Custom._id+"']").find("img").attr("src"));
	return "<div class='chat sender-"+data.sender+"' src-id="+data._id+"><img class='headpic' src='"+avatartemp+"'><span class='chatback'>"+data.content+"</span></div>";
}

function warn(str) {
	$(".chatsheet").append("<div style='    text-align: center;'><p class='warning'>"+str+"</p></div>");
	$(".chatsheet").scrollTop($(".chatsheet")[0].scrollHeight);
}


function VerifyPost(str) {
	
	
	str = str.replace("&","&amp;");
	str = str.replace(" ","&nbsp;");
	str = str.replace('"',"&quot;");
	str = str.replace("<","&lt;");
	str = str.replace(">","&gt;");
	str = str.replace('?',"？");
	str = str.replace("&amp;#","&#");
	
	return str;
}

function createPostMethod(_method,options) {
	options.data.requestMethod = _method;
	options.data.token = "test";
	if(!(options.data.hasOwnProperty('userId'))) { options.data.userId = 0; }
	return $.ajax({
		url:"http://api.xszlv.com/STChat/api/api.php",
		type:"post",
		dataType:"json",
		async:"false",
		data:JSON.stringify(options.data),
		success:options.success,
		error:options.error
	});
}

$("[type='file']").change(
	function() {
		if(!(run_Custom instanceof Custom))
		{
			warn("没有选中用户");
			return;
		}
		var src = this.files[0];
		if(!/image\/\w+/.test(src.type)){ 
			warn("请确保文件为图像类型"); 
			return false; 
		} 
		var reader = new FileReader(); 
		reader.readAsDataURL(src);
		reader.onload = function(e) {
			var base64 = this.result.replace(/data:[^;]+;base64,/,"");
			$.ajax(
				{
					url:"http://121.41.61.101/Images/api.php",
					type:"post",
					data:JSON.stringify({"token":"123","base64":base64,"requestMethod":"upload"}),
					success:function(temp) {
						$("input[name='message']").val("<img src='http://"+temp.image_path+"'>");
						if(temp.code == -1) {
							warn("图片上传失败");
						}
						CSAPI.postMessage(true);
					},error:function() {}
				}
				);
		}
	}
);

