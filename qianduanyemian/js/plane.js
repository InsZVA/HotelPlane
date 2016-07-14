/**
 * Created by InsZVA on 2016/7/14.
 */
function loadNSPlanes(offset, num, orderBy, order, standard) {
    CallAPI({requestMethod: "listPlanes", offset: offset, num: num, orderBy: orderBy, order: order, standard: standard},
    function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        alert(JSON.stringify(data));
    });
}