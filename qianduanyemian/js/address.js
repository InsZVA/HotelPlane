/**
 * Created by InsZVA on 2016/7/14.
 */
function getCities(chinese, callback) {
    CallAPI({requestMethod: "getCities", chinese:chinese}, callback);
}

function getCityName(cityId) {
    var ret = window.localStorage.getItem("cityId" + cityId);
    if (ret == undefined || ret == "") {
        var data = CallAPINotAsync({requestMethod: "getCityData", cityId: cityId});
        window.localStorage.setItem("cityId" + cityId, data.name);
        ret = data.name;
    }
    return ret;
}

function loadCities(chinese) {
    if (chinese == 1) {
        $("#rightDiv").css({backgroundColor: "#009ce5", color: "white"});
        $("#leftDiv").css({backgroundColor: "#fff", color: "black"});
    } else {
        $("#leftDiv").css({backgroundColor: "#009ce5", color: "white"});
        $("#rightDiv").css({backgroundColor: "#fff", color: "black"});
    }
    getCities(chinese, function(data) {
        var letters = [];
        var html = "";
        for (var i = 0;i < data.length;i++) {
            if (letters[data[i].letter] == undefined) letters[data[i].letter] = [{name: data[i].name, id: data[i].city_id}];
            else letters[data[i].letter].push({name: data[i].name, id: data[i].city_id});
        }
        var sorted = Object.keys(letters).sort();
        //letters.sort();
        for (var i in sorted) {
            var l = sorted[i];
            html += '<div class="letter" id="letter_'+ l +'">' + l +'</div>';
            for (var i = 0;i < letters[l].length;i++) {
                html += '<div class="regions" onclick="select(this)" cid="' + letters[l][i].id + '">'+ letters[l][i].name +'</div>'
            }
        }
        $("#list").html(html);
    })
    hotCities(chinese);
}

function getCityId(cityName) {
    var ret = CallAPINotAsync({requestMethod: "getCityId", cityName: cityName});
    return ret.city_id;
}