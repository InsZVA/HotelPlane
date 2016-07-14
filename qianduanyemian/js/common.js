/**
 * Created by InsZVA on 2016/7/14.
 */
function setToken(token) {
    window.localStorage.setItem("token", token);
}

function getToken() {
    return window.localStorage.getItem("token");
}

function CallAPI(address, data) {
    if (getToken() == undefined || getToken() == "") 
}