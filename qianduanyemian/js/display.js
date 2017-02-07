/**
 * Created by InsZVA on 2016/8/22.
 */
function loadDisplays() {
    var data = CallAPINotAsync({requestMethod: "getDisplay"});
        var html = "";
        if (data.code != undefined) {
            html = '<ul style="padding:0">'+
                '<li><a href="#" target="_blank"><img src="./img/BestofMAS2015-banner.png" alt=""></a></li>'+
                '<li><a href="#" target="_blank"><img src="./img/BestofMAS2015-banner.png" alt=""></a></li>'+
                '<li><a href="#" target="_blank"><img src="./img/BestofMAS2015-banner.png" alt=""></a></li>'+
                '<li><a href="#" target="_blank"><img src="./img/BestofMAS2015-banner.png" alt=""></a></li>'+
                '<li><a href="#" target="_blank"><img src="./img/BestofMAS2015-banner.png" alt=""></a></li>'+
                '</ul>';
        } else {
            html = "<ul style='padding:0'>";
            for (var i = 0;i < data.length;i++) {
                if (data[i].state == 0) continue;
                html += '<li><a href="'+ data[i].href +'" target="_blank"><img src="'+ data[i].path +'"></a></li>';
            }
            html += '</ul>';
        }
        $("#slide2").html(html);
}
loadDisplays();