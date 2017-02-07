/**
 * Created by InsZVA on 2016/7/17.
 */
function loadUserPayments() {
    CallAPI({requestMethod: "listUserPayments"}, function (data) {
        var state = ['等待确认', '等待付款', '已付款', '已完成'];
        state[-1] = '已取消';
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
            if (data[i].type == 0 && data[i].standard == 0) {    //Plane
                var plane = getPlaneData(data[i].plane_id);
                html += '<div class="ticket" onclick="goPayment('+ data[i].payment_id + ',' + data[i].state +')"><div class="bar1"><img src="./img/Capa_1@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jp">机票</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="flight">'+ getCityName(plane.start_city_id) + ' — ' + getCityName(plane.end_city_id) +'</span><span class="htPrice">'+'￥'+ plane.price +'</span>'+
                '</div><div class="bar3"><div class="htDetails">' + plane.start_airport + '(' + plane.start_time + ')' + ' － ' + plane.end_airport + '(' + plane.end_time + ')</div><div class="htDetails">' + plane.flight_number + '</div></div></div>';
            } else if (data[i].type == 0 && data[i].standard == 1) {    //NSPlane
                html += '<div class="ticket" onclick="goPayment('+ data[i].payment_id + ',' + data[i].state +')"><div class="bar1"><img src="./img/Capa_1@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jp">机票</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="flight">'+ data[i].start_city + ' — ' + data[i].end_city +'</span><span class="htPrice">'+'￥'+ data[i].price +'</span>'+
                    '</div><div class="bar3"><div class="htDetails">' + data[i].start_airport + '(' + data[i].start_time + ' － '+ data[i].end_airport + '(' + data[i].end_time + '</div><div class="htDetails">' + data[i].flight_number + '</div></div></div>';

            }
            else if(data[i].type == 1) { //Hotel
                var hotel = getHotelData(data[i].hotel_id);
                html += '<div class="hotel" onclick="goPayment('+ data[i].payment_id + ',' + data[i].state +')"><div class="bar1"><img src="./img/hotel@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jd">酒店</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="htName">'+ hotel.name +'</span><span class="htPrice">￥'+ getPaymentPrice(data[i].payment_id) +'</span></div>' +
                    '<div class="bar3"><div class="htDetails">'+ new Date(parseInt(data[i].start_date) * 1000).toLocaleString().substr(0,17) +' 至 '+ new Date(parseInt(data[i].end_date) * 1000).toLocaleString().substr(0,17) +'</div><div class="htDetails">1晚</div></div></div>';
            } else {
                var activity = getActivityData(data[i].activity_id);
                html += '<div class="hotel" onclick="goPayment('+ data[i].payment_id + ',' + data[i].state +')"><div class="bar1"><img src="./img/hotel@1x.png" style="margin-left: 11px;margin-top:10px;vertical-align: baseline;float: left;" />'+
                    '<span class="jd">活动</span><span class="arrearage">'+ state[parseInt(data[i].state)] +'</span></div><div class="bar2"><span class="htName">'+ activity.name +'</span><span class="htPrice">￥'+ getPaymentPrice(data[i].payment_id) +'</span></div>'+
                    '<div class="bar3"><div class="htDetails">'+ "下单时间:" + new Date(parseInt(data[i].create_time) * 1000).toLocaleString().substr(0,17) +'</div><div class="htDetails"></div></div></div>';
            }
        }
        $("#container").html(html);
    })
}

function goPayment(paymentId, state) {
    switch (state) {
        case 0:
            alert("您的订单还在确认中，请耐心等待，正在为您跳到客服~");
            location.href='chat.html?addition={"paymentId":' + paymentId + '}';
            break;
        case 1:
            location.href='paymentConfirmed.html?id=' + paymentId;
            break;
        case 2:
        case 3:
            if (confirm("对此订单还有什么问题要向客服了解吗？"))
                location.href='chat.html?addition={"paymentId":' + paymentId + '}';
            break;
        case -1:
            alert("抱歉，我们无法处理您的订单！");
            break;
    }
}

function getPaymentPrice(paymentId) {
    var data = CallAPINotAsync({requestMethod: "getPaymentPrice", paymentId: paymentId});
    return data.price;
}