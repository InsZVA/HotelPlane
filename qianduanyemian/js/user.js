/**
 * Created by InsZVA on 2016/7/14.
 */
function login(username, password) {
    $.post("http://121.41.61.101/api/api.php", JSON.stringify({requestMethod: "login", data: {username: username, password: password}}), function(data) {
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