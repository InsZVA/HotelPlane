/**
 * Created by InsZVA on 2016/7/14.
 */
function setToken(token) {
    window.localStorage.setItem("token", token);
}

function getToken() {
    return window.localStorage.getItem("token");
}

function getUserID() {
    return parseInt(window.localStorage.getItem("userID"));
}

function setUserID(userId) {
    window.localStorage.setItem("userID", userId);
}

function CallAPI(data, callback) {
    if (getToken() == undefined || getToken() == "") {
        window.location.href = "landing.html";
        return;
    }
    data.userId = getUserID();
    data.token = getToken();
    var api = "http://api.xszlv.com/api/api.php";
    $.post(api, JSON.stringify(data), callback);
}

function CallAPINotAsync(data) {
    var ret;
    if (getToken() == undefined || getToken() == "") {
        window.location.href = "landing.html";
        return;
    }
    data.userId = getUserID();
    data.token = getToken();
    var api = "http://api.xszlv.com/api/api.php";
    $.ajax({url: api, data: JSON.stringify(data), method: "post", async: false, success: function(data) {ret = data;}});
    return ret;
}

function getJsUrl(){
    var pos,str,para,parastr;
    var array =[]
    str = location.href;
    parastr = str.split("?")[1];
    if (parastr == undefined) return [];
    var arr = parastr.split("&");
    for (var i=0;i<arr.length;i++){
        array[arr[i].split("=")[0]]=arr[i].split("=")[1];
    }
    return array;
}