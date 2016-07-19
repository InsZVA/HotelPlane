/**
 * Created by InsZVA on 2016/7/17.
 */
function listActivity() {
    CallAPI({requestMethod: "listAvailableActivity"}, function(data) {
        var html = '<div id="list">活动列表</div>';
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            $("#container").html(html);
            return;
        }
        for (var i = 0;i < data.length;i++) {
            html += '<div class="activity" onclick="goActivity('+ data[i].activity_id +')"><img src="'+ data[i].image + '" style="width: 375px;height: 150px;"/><div class="caption">'
            + data[i].name + '</div><div class="price">￥' + data[i].price + '</div></div>';
        }
        $("#container").html(html);
    })
}

function goActivity(id) {
    window.location.href = "activityDetails.html?id=" + id;
}

function loadActivity(id) {
    id = parseInt(id);
    CallAPI({requestMethod: "getActivityData", activityId: id}, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            alert("连接失败！");
            return;
        }
        data = data[0];
        $("#bar").html(data.name);
        $("#hdjs").html(data.name);
        $("#price").html("￥" + data.price);
        $("#pic1").attr("src", data.image);
        $("#content").html(data.description);
    });
}