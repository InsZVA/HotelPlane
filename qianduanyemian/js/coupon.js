/**
 * Created by InsZVA on 2016/8/6.
 */
function loadCoupons() {
    CallAPI({requestMethod: "getUserCoupons"}, function(data) {
        if (data.code == -1) {
            alert('没有可用优惠券！');
        } else {
            for (cp in data) {
                cpData = CallAPINotAsync({requestMethod: "getCouponData", couponId: data[cp].coupon_id});
                $temp = '';
                cpData.end_time = parseInt(cpData.end_time);
                if (new Date().getTime() > cpData.end_time * 1000)
                    $temp = '<div class="mask2"><div class="num">¥ ' + cpData.discount + '</div><div class="validity">有效期至：'+
                        new Date(parseInt(cpData.end_time) * 1000).toDateString() + '</div></div><div class="tag"><div class="overdue">过期</div></div>';
                else if (parseFloat(localStorage.getItem('price')) < parseFloat(cpData.min_price) && getJsUrl()['nouse'] != 1)
                    $temp = '<div class="mask2"><div class="num">¥ ' + cpData.discount + '</div><div class="validity">有效期至：'+
                        new Date(parseInt(cpData.end_time) * 1000).toDateString() + '</div></div><div class="tag"><div class="overdue">不可用</div></div>';
                else
                    $temp = '<div class="mask1" onclick="useCoupon('+ data[cp].uc_id +',' + cpData.discount + ')"><div class="num">¥ ' + cpData.discount + '</div><div class="validity">有效期至：'+
                        new Date(parseInt(cpData.end_time) * 1000).toDateString() + '</div></div><div class="tag"><div class="overdue">可用</div></div>';
                $("#con").html($temp);
            }
        }
    })
}

function useCoupon(ucid, discount) {
    if (getJsUrl()['nouse'] == 1) return;
    localStorage.setItem("coupon", ucid);
    localStorage.setItem("discount", discount);
    location.replace(decodeURIComponent(getJsUrl()['callback']));
}