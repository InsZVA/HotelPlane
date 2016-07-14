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
    var api = "http://121.41.61.101/api/api.php";
    $.post(api, JSON.stringify(data), callback);
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