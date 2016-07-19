/**
 * Created by InsZVA on 2016/7/17.
 */
function loadUserPayments() {
    CallAPI({requestMethod: "listUserPayments"}, function (data) {
        var state = ['等待确认', '等待付款', '已付款', '已完成'];
        var html ='<div id="bar">我的订单</div>';
        if (data.code == -2) {
            alert("请先登录！");
            window.location.replace("landing.html");
            return;
        }
        if (data.code == -1) {
            alert("连接失败！");
            $("#container").html(html);
            return;
        }
        for (var i = 0;i < data.length;i++) {
            if (data[i].type == 0) {    //Plane
                var plane = getPlaneData(data[i].plane_id);
                html += '<div class="ticket"><div class="bar1"><img src="./img/Capa_1@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jp">机票</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="flight">'+ getCityName(plane.start_city_id) + ' — ' + getCityName(plane.end_city_id) +'</span><span class="htPrice">'+'￥'+ plane.price +'</span>'+
                '</div><div class="bar3"><div class="htDetails">' + plane.start_time + ' － ' + plane.end_time + '</div><div class="htDetails">' + plane.flight_number + '</div></div></div>';
            } else {
                var hotel = getHotelData(data[i].hotel_id);
                html += '<div class="hotel"><div class="bar1"><img src="./img/hotel@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jd">酒店</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="htName">'+ hotel.name +'</span><span class="htPrice"></span></div>'+ //TODO: price
                    '<div class="bar3"><div class="htDetails">'+ new Date(parseInt(data[i].start_date) * 1000).toLocaleString().substr(0,17) +' 至 '+ new Date(parseInt(data[i].end_date) * 1000).toLocaleString().substr(0,17) +'</div><div class="htDetails">1晚</div></div></div>';
            }
        }
        $("#container").html(html);
    })
}
