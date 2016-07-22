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
    getCities(chinese, function(data) {
        var letters = [];
        var html = "";
        for (var i = 0;i < data.length;i++) {
            if (letters[data[i].letter] == undefined) letters[data[i].letter] = [{name: data[i].name, id: data[i].city_id}];
            else letters[data[i].letter].push({name: data[i].name, id: data[i].city_id});
        }
        letters.sort();
        for (var l in letters) {
            html += '<div class="letter">' + l +'</div>';
            for (var i = 0;i < letters[l].length;i++) {
                html += '<div class="regions" onclick="select(this)" cid="' + letters[l][i].id + '">'+ letters[l][i].name +'</div>'
            }
        }
        $(".initial").html(html);
    })
}