
var area = [
    ['集团','实验中学校区', '日月兴城校区', '水木清华校区', '天丽校区','锦州校区','一高中校区','实验高中校区', '越秀佳园校区','大石桥校区','鲅鱼圈校区','江北校区','江南校区'],
    ['数学','物理','化学','英语','生物','语文','政治','历史','地理','理科综合','文科综合','全部'],
    ['时间'],
    ['职务'],
];
function Id() {
    var sel = document.getElementById("prov");
    var opt = "";
    var len = area[sel.value];
    for (var i = 0; i < len.length; i++) {
            opt = opt + '<option value="' + i + '">' + area[sel.value][i] + '</option>';
    }
    document.getElementById("city").innerHTML = opt;
}