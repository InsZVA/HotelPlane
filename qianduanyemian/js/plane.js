/**
 * Created by InsZVA on 2016/7/14.
 */
function loadNSPlanes(offset, num, orderBy, order, standard) {
    CallAPI({requestMethod: "listPlanes", offset: offset, num: num, orderBy: orderBy, order: order, standard: standard},
    function(data) {
        var html = '<div id="Triangle"></div>'+
            '<div id="tjjpbt">特价机票</div>'+
            '<a href="/flightList.html?ns=1"><div id="more">+more</div></a>';
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            $("#tjjp").html(html);
            return;
        }
        for (var i = 0;i < data.length;i++) {
            html += '<div id="huodong'+ (i+1) +'"><img src="https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1467684389&di=c67792c4cd69e320f2daec61be78315b&src=http://image41.360doc.com/DownloadImg/2011/11/0706/19073054_28.jpg" style="width: 175px;height: 85px;"'+
                '/> <div id="hdbt">' + getCityName(data[i].start_city_id) + ' —— ' +  getCityName(data[i].end_city_id) +'</div> </div>';
        }
        $("#tjjp").html(html);
    });
}


function listNSPlanes() {
    CallAPI({requestMethod: "listPlanes", offset: 0, num: 30, orderBy: "price", order: "asc", standard: 1},
        function(data) {
            var html = '';
            if (data.code == -2) {
                alert("请先登录！");
                window.location.replace("landing.html");
                return;
            }
            if (data.code == -1) {
                $("#container").html(html);
                alert("未找到特价机票，已为您推荐特价活动。");
                window.location.replace("activityList.html");
                return;
            }
            var html = "";
            var regex = /([0-9]+:[0-9]+):[0-9]+/;
            window.localStorage.setItem("datas", JSON.stringify(data));
            for (var i = 0;i < data.length;i++) {
                data[i].start_time = regex.exec(data[i].start_time)[1];
                data[i].end_time = regex.exec(data[i].end_time)[1];
                data[i].price = "￥" + data[i].price;
                html += '<div class="part" onclick="nsplane('+ i +')"><div class="leftPart"><div class="part1"><div class="start">'+ data[i].start_time +'</div>'+
                    '<div class="line"></div><div class="end">'+ data[i].end_time +'</div></div><div class="part2"><div class="airport1">'+
                    data[i].start_airport +'</div><div class="airport2">'+ data[i].end_airport +'</div></div><div class="part3"><div class="flightInfo">'+
                    data[i].flight_number +'</div></div></div><div class="rightPart">' + data[i].price + '</div></div>';
            }
            $("#container").html(html);
        });
}

function searchPlanes(start_city_id, end_city_id, start_date, offset, num) {
    CallAPI({requestMethod: "findPlanes", data: {offset: offset, num: num, start_city_id: start_city_id, end_city_id: end_city_id, start_date: start_date}}, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            $("#container").html(html);
            alert("未找到相关机票，已为您推荐附近酒店。");
            window.location.replace("hotelList.html?cityId=" + end_city_id);
            return;
        }
        var html = "";
        var regex = /([0-9]+:[0-9]+):[0-9]+/;
        window.localStorage.setItem("datas", JSON.stringify(data));
        for (var i = 0;i < data.length;i++) {
            data[i].start_time = regex.exec(data[i].start_time)[1];
            data[i].end_time = regex.exec(data[i].end_time)[1];
            data[i].price = "￥" + data[i].price;
            html += '<div class="part" onclick="onplane('+ i +')"><div class="leftPart"><div class="part1"><div class="start">'+ data[i].start_time +'</div>'+
            '<div class="line"></div><div class="end">'+ data[i].end_time +'</div></div><div class="part2"><div class="airport1">'+
                data[i].start_airport +'</div><div class="airport2">'+ data[i].end_airport +'</div></div><div class="part3"><div class="flightInfo">'+
                data[i].flight_number +'</div></div></div><div class="rightPart">' + data[i].price + '</div></div>';
        }
        $("#container").html(html);
    });
}

function readyPayment(id) {
    id = parseInt(id);
    var datas = localStorage.getItem("datas");
    if (datas == undefined || datas == "") {
        alert("数据错误！请重新预定！");
        window.location.replace("index.html");
        return;
    }
    datas = JSON.parse(datas);
    $("#part1").html(datas[id].start_time + " " + getCityName(datas[id].start_city_id) + "--" + getCityName(datas[id].end_city_id));
    var regex = /([0-9]+:[0-9]+):[0-9]+/;
    $("#time1").html(regex.exec(datas[id].start_time)[1]);
    $("#time3").html(regex.exec(datas[id].end_time)[1]);
    var st = datas[id].start_time;
    var et = datas[id].end_time;
    st = st.replace(/-/g,"/");
    var sd = new Date(st );
    et = et.replace(/-/g,"/");
    var ed = new Date(et );
    $("#timeT").html(((ed - sd) / 3600000) + "小时" + (((ed - sd) % 3600000) / 60000) + "分钟");
    $("#start").html(datas[id].start_airport);
    $("#end").html(datas[id].end_airport);
    $("#part4").html("航班号：" + datas[id].flight_number);
    $("#remarks").html(datas[id].remarks);
    $("#price").html("￥" + datas[id].price);
}

function submitPayment() {
    var idType = parseInt(localStorage.getItem("idType"));
    var idCode = $("#idCode").val();
    var sex = localStorage.getItem("sex");
    var name = $("#name").val();
    var contact = $("#contact").val();
    var phone = $("#phone").val();
    var regex = /[0-9]+/;
    if (name == "" || contact == "" || !regex.test(phone) || !regex.test(idCode)) {
        alert("填写信息不完整或非法，请更正后再次提交！");
        return;
    }
    var datas = JSON.parse(localStorage.getItem("datas"));
    var id = parseInt(getJsUrl()['id']);
    var d = {requestMethod: "createPayment", data: {userId: getUserID(), standard:0, type:0, idCode: idCode, sex: parseInt(sex),
        contact: contact, phone: phone, name: name, planeId: datas[id].plane_id, idType: idType}};
    CallAPI(d, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            alert("订单生成失败！");
            //window.location.replace("index.html");
            return;
        }
        alert("您的订单已经发送至后台进行处理，我们会在20分钟内对该订单进行确认，之后会以微信推送的形式告知您，请您注意查收！");
        //TODO: 跳转详情页
        window.location.replace("index.html");
        return;
    });
}

function getPlaneData(id) {
    id = parseInt(id);
    return CallAPINotAsync({requestMethod: "getPlaneData", planeId: id})[0];
}

function nsplane(id) {
    window.location.replace("chat.html");
}