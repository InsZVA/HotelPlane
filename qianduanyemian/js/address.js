/**
 * Created by InsZVA on 2016/7/14.
 */
function getCities(chinese, callback) {
    CallAPI({requestMethod: "getCities", chinese:chinese}, callback);
}