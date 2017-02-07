/**
 * Created by InsZVA on 2016/7/14.
 */
function login(username, password) {
    $.post("http://api.xszlv.com/api/api.php", JSON.stringify({requestMethod: "login", data: {username: username, password: password}}), function(data) {
        var obj = data;
        if (obj.user_id && obj.token) {
            setToken(obj.token);
            setUserID(obj.user_id);
            alert("登录成功！");
            var callback = getJsUrl()['callback'];
            if (callback == undefined || callback == "") callback = "index.html";
            window.location.replace(callback);
            return;
        } else {
            alert("登录失败！");
        }
    });
}

function loadUserData() {
    CallAPI({requestMethod: "getUserData"}, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            alert("连接失败！");
            return;
        }
        $("#account").html("￥" + data.account);
        $("#award_account").html("￥" + data.award_account);
        $("#name").html(data.username);
        $("#yue").html("余额： ￥" + data.account);
        if (data.avatar != "")
            $("#avatar").attr("src", data.avatar);
        if (data.vip == 1) {
            $("#bar").html("至尊会员");
            //TODO 会员
        }
    });
}