var chatAPI = new Object;

chatAPI.initialize = function() {
	this.debug = true; /* 当DEBUG开着的时候若浏览器没有userID,userId也会分派。 */

	if(!window.localStorage) {
		warn("浏览器不支持localStorage，可能无法正常使用！");
	}
	
	if(window.localStorage['userID']) {
		this.userId = window.localStorage['userID'];
	} else {
		this.userId = this.debug ? 4 : -1; // 4测试用
	}
	this.urlData = parseURL();
	this.historySessions = {};
	this.version = "0.0.1";
	this.newestM = 0;
	this.status = -1; /* 默认ready */
	this.refreshCount = 1000;
	this.loop = false;
	this.lastTimeStamp = 0;
	this.headpic = "sc/2.png";
	this.create();
	var temp = this;
	$.ajax({
		url:"http://api.xszlv.com/api/api.php",
		data:JSON.stringify({
			"requestMethod":"getAvatar",
			"userId":chatAPI.userId,
			"token":"test"
		}),
		type:"post",
		success:function(avatar) {
			temp.headpic = avatar.avatar;
		}
	});
	$(".headinfo").html("等待客服接起...");

}

chatAPI.listen = function() {
	this.getStatus();
	if (this.status == 0) {
		this.getNewestMessage();
	}
	var temp = this;
	this.loop = setTimeout(function() {temp.listen();},this.refreshCount);
}

chatAPI.updateChat = function() {
	var temp = this;
	if(temp.status != 0)
	{
		return;
	}
	while(temp.Count + 1 <= temp._data.length)
	{
		$(".chatsheet").append(InfoGenerate(temp._data[temp.Count]));
		temp.Count++;
	}
	$("img[rid]").each(
		function() {
			var temp = $(this);
			wx.downloadImage({
		    serverId: temp.attr("rid"), // 需要下载的图片的服务器端ID，由uploadImage接口获得
		    isShowProgressTips: 1, // 默认为1，显示进度提示
		    success: function (res) {
		        var localId = res.localId; // 返回图片下载后的本地ID
		        temp.attr("src",localId);
		        temp.removeAttr("rid");
		    }
		});
			
		}
		);
	$(".chatsheet").scrollTop($(".chatsheet")[0].scrollHeight);
}

chatAPI.getStatus = function() {
	var temp = this;
	createPostMethod("getSessionData",
		{
			data:{"sessionId":temp.sessionId},
			success:function(data) {
				if(temp.status != data.state)
				{
					if(temp.status == 0 && data.state == 1)
					{
						temp.status = 2;
					} else {
						temp.status = data.state;
					}
					
					if(data.state == 0) {
						temp.waiterId = data.waiter_id;
						warn("成功连接客服。");
						createPostMethod("getWaiterData",
							{data:{waiterId:temp.waiterId},success:function(data) {
								if(!data.headpic) { data.headpic = "sc/1.png"; }
								$(".headinfo").html(data.name);
								$.ajax({
									url:"http://api.xszlv.com/api/api.php",
									data:JSON.stringify({
										"requestMethod":"getAvatar",
										"userId":temp.waiterId,
										"token":"test"
									}),
									type:"post",
									success:function(avatar) {
										$(".headinfo").attr("headpic",data.headpic);
									}
								});
							},error:function(data) {}});
						
					} else if (data.state == 1) {
						warn("您的会话已结束。 ");
						
					}
					if(temp.status == 1) {
						temp.create();
					}
				}
			},
			error:function(data) {
			}
		});
	
}

chatAPI.lastSession = function(callback) {
	var temp = this;
	return createPostMethod("getUserLastSession",
		{
			data:{"userId":temp.userId},
			success:callback,
			error:function(data) {
						warn("连接失败！");
					}
			});
}

chatAPI.create = function() {
	if(this.userId < 0) {
		warn("获取用户ID失败！");
		return;
	}
	var temp = this;
	this.waiterId = -1;
	this._data = new Array();
	this.Count = 0;
	this.sessionId = 0;
	this.status = -1;

	this.lastSession(
		function(data1) {
			createPostMethod("getSessionData",
			{
				data:{"sessionId":data1._id},
				success:function(data2) {
					if(data2.state == 0 || data2.state == -1) 
					{
						temp.sessionId = data1._id;
						window.localStorage['token'] = temp.sessionId;
						if(temp.loop === false) {temp.listen();}

					} else {
						createPostMethod("newSession",
						{
							data:{"userId":temp.userId,"addition":temp.urlData},
							success:function(data) {

								temp.lastSession(function(data) {temp.sessionId = data._id});
								
								if(temp.loop === false) {temp.listen();}
							},
							error:function(data) {
								warn("连接失败！");
							}
						});
					}
				},
				error:function(data) {
					warn("连接失败！");
				}
			});
		}
		);

	
	

}

chatAPI.getNewestMessage = function() {
	var temp = this;
	createPostMethod("getSessionNewestMessages",
		{
			data:{"sessionId":temp.sessionId,"lastTime":temp.lastTimeStamp},
			success:function(data) {
				if(data.length <= 0) { return; }
				if(!(data instanceof Array)) { return; }
				temp._data = temp._data.concat(data.reverse());
				temp.lastTimeStamp = data[data.length-1].create_time;
				temp.updateChat();
			},
			error:function(data) {
			}
		});
}

chatAPI.postMessage = function(check) {
	if(this.status != 0)
	{
		warn("还没有找到客服为您服务！");
		return;
	}
	var temp = this;
	var content = $("input[name='message']").val();
	check = check || false;
	/* 内容合法性检查 */
	if(!check) content = VerifyPost(content);
	createPostMethod("userSendMessage",
	{
			data:{"userId":temp.userId,"sessionId":temp.sessionId,"type":"text","content":content},
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





function createPostMethod(_method,options) {
	options.data.requestMethod = _method;
	options.data.token = "test";
	options.data.userId = chatAPI.userId;
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

function warn(str) {
	$(".chatsheet").append("<div style='    text-align: center;'><p class='warning'>"+str+"</p></div>");
	$(".chatsheet").scrollTop($(".chatsheet")[0].scrollHeight);
}

function InfoGenerate(data) {
	var avatartemp = data.sender == 0 ? chatAPI.headpic : $("span.headinfo").attr("headpic");
	return "<div class='chat sender-"+data.sender+"' src-id="+data._id+"><img class='headpic' src='"+avatartemp+"'><span class='chatback'>"+data.content+"</span></div>";
}

function VerifyPost(str) {
	
	
	str = str.replace("&","&amp;");
	str = str.replace(" ","&nbsp;");
	str = str.replace('"',"&quot;");
	str = str.replace("<","&lt;");
	str = str.replace(">","&gt;");
	str = str.replace("&amp;#","&#");
	str = str.replace('?',"？");
	return str;
}

$("a.submit").click(function() {
	chatAPI.postMessage();
});

function parseURL() {
	var url = location.search.substr(1);
	var regex = /([^=&]+)=([^=&]+)/g;
	var temp;
	var urlpro = new Object;
	while((temp = regex.exec(url)) != null)
	{
		temp[2] = temp[2].replace(/%22/g,"\"");
		urlpro[temp[1]] = temp[2];
		
	}
	if(urlpro.addition)
		return JSON.parse(urlpro.addition);
	return new Object;
}

var url = location.href.split('#')[0];

$.ajax({
	url:"http://api.xszlv.com/api/wxtoken.php",
	data:{"url":url},
	type:"POST",
	success:function(res) {
		$("span#config").html(res);
		wx.config({
		    debug: false, 
		    appId: "wx81c1603b41b5f4f6", 
		    timestamp: $("p#timestamp").html(), 
		    nonceStr: $("p#noncestr").html(), 
		    signature: $("p#sign").html(),
		    jsApiList: ['chooseImage', 'uploadImage', 'downloadImage'] // 功能列表，我们要使用JS-SDK的什么功能
		});
	}
});


wx.ready(function() {
	warn("占位符");
	chatAPI.initialize();
	$("#camera").click(
	function() {
		if(chatAPI.status != 0)
			{
				warn("还没有找到客服为您服务！");
				return;
			}
		wx.chooseImage({
		    count: 1, // 默认9
		    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
		    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
		    success: function (res) {
		        var localIds = res.localIds[0]; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
		        uploadPic(localIds);
		    }
		});
	}
);
});

function uploadPic(_localId) {
	wx.uploadImage({
		localId: _localId, // 需要上传的图片的本地ID，由chooseImage接口获得
		isShowProgressTips: 0, // 默认为1，显示进度提示
		success: function (res) {
			var serverId = res.serverId; // 返回图片的服务器端ID
			$("input[name='message']").val("<img rid='"+serverId+"'>");
			chatAPI.postMessage(true);
		}
	});
}