/**
 * Created by InsZVA on 2016/7/14.
 */
function setToken(token) {
    window.localStorage.setItem("token", token);
}

function getToken() {
    return window.localStorage.getItem("token");
}

function CallAPI(data, callback) {
    if (getToken() == undefined || getToken() == "") {
        window.location.href = "";
        return;
    }
    api = "http://121.41.61.101/api/api.php";
    $.post(api, JSON.stringify(data), callback(data));
}