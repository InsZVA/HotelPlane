/**
 * Created by InsZVA on 2016/7/14.
 */
function loadHotHotels() {
    CallAPI({requestMethod: "listHotels", offset: 0, num: 4, orderBy: "price", order: "asc"}, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            return;
        }
        var html = '<div id="Triangle"></div>'+
            '<div id="tjjpbt">特价酒店</div>'+
            '<a href="/hotelList.html"><div id="more">+more</div></a>';
        for (var i = 0;i < data.length;i++) {
            data[i].images = JSON.parse(data[i].images);
            html += '<div id="huodong' + (i+1) +'">'+
                '<img onclick="goHotel('+ data[i].hotel_id +')" src="' + data[i].images[0] + '" style="width: 175px;height: 85px;"'+
                '/>  <div id="hdbt">' + data[i].name +'</div>  </div>';
        }
        $("#tjjd").html(html);
    })
}

function loadNSHotel(cityId, order) {
    cityId = parseInt(cityId);
    CallAPI({requestMethod: "findHotelsByCity", cityId: cityId, orderBy: "price", order: order}, function(data) {
        var html = "";
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            $("#list").html(html);
            alert("未找到相关酒店，已为您推荐相关活动！");
            location.replace('activityList.html');
            return;
        }
        for (var i = 0;i < data.length;i++) {
            data[i].images = JSON.parse(data[i].images);
            html += '<div class="hotelTuwen" onclick="goHotel('+ data[i].hotel_id +')" style="background-image: url('+ data[i].images[0] +')"><div class="caption">' + data[i].name + '</div><div class="region">' + data[i].star + '星级 </div>'+
                '<div class="price">￥'+ data[i].price +'起</div></div>';
        }
        $("#list").html(html);
    });
}

function goHotel(id) {
    window.location.href = "hotelInfo.html?id=" + id;
}

function loadHotelData(id) {
    id = parseInt(id);
    CallAPI({requestMethod: "getHotelData", hotelId: id}, function(data) {
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            alert("连接失败！");
            return;
        }
        data.images = JSON.parse(data.images);
        $("#firstBar1").html(data.name);
        $("#firstBar2").html(data.star + "星级");
        $("#remarks2").html(data.remarks);
        $("#img").attr("src", data.images[0]);
        $("#price").html("￥0");
        CallAPI({requestMethod: "getRooms", hotelId: id}, function(data) {
            var html = "";
            for (var i = 0;i < data.length;i++) {
                html += '<div class="randomH"><div id="leftDiv"></div><div id="midDiv">'+
                    data[i].name + '(' + data[i].description + ')' +
            '</div><div id="rightDiv"><div id="price2">¥ ' + data[i].price + '</div>' +
                '<div id="confirmBtn" class="selectRoom" onclick="selectRoom(this)" rid="'+ data[i].room_id +'" price="'+ data[i].price+'"> 选择此房</div> </div></div>';
            }
            $("#rooms").html(html);
        });
    });
}


function submitPaymentH() {
    var idType = parseInt(localStorage.getItem("idType"));
    var idCode = $("#idCode").val();
    var sex = localStorage.getItem("sex");
    var name = $("#name").val();
    var contact = $("#contact").val();
    var phone = $("#phone").val();
    var regex = /[0-9]+/;
    var startDate = (localStorage.getItem("start_date"));
    var endDate = (localStorage.getItem("end_date"));
    if (name == "" || contact == "" || !regex.test(phone) || !regex.test(idCode) || startDate == "" || endDate == "") {
        alert("填写信息不完整或非法，请更正后再次提交！");
        return;
    }
    startDate.replace(/-/g, "/");
    startDate = new Date(startDate);
    startDate = startDate.getTime() / 1000;
    endDate.replace(/-/g, "/");
    endDate = new Date(endDate);
    endDate = endDate.getTime() / 1000;
    var roomId = parseInt(localStorage.getItem("roomId"));

    var id = parseInt(getJsUrl()['id']);
    var d = {requestMethod: "createPayment", data: {userId: getUserID(), standard:1, type:1, idCode: idCode, sex: parseInt(sex),
        contact: contact, phone: phone, name: name, hotelId: id, idType: idType, roomId: id, startDate: startDate, endDate: endDate}};
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


function getHotelData(id) {
    id = parseInt(id);
    return CallAPINotAsync({requestMethod: "getHotelData", hotelId: id});
}