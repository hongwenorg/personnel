/**
 * Created by Administrator on 2016/3/30.
 */



//******************************     全局变量  start    ***************************
var arr_staff = [];
var arr_class = new Array();
var CurrentPage = 0, CountPage, PageSize = 25;
var find_btt = document.getElementById("find_btt");
var pageall = document.getElementById("pageall");
var pagemin = document.getElementById("page_min");
var pagemax = document.getElementById("page_max");
var copy_excel = document.getElementById("copy_excel");
var copy_dayin = document.getElementById("copy_dayin");
var tb = document.getElementById("tb");
var city = document.getElementById("city");
var tabb1 = document.getElementById("tabb1");
var tabb2 = document.getElementById("tabb2");
var tabb3 = document.getElementById("tabb3");
var tabb4 = document.getElementById("tabb4");
var tabb5 = document.getElementById("tabb5");
var tabb6 = document.getElementById("tabb6");
var tabb7 = document.getElementById("tabb7");
var tb_content = tb.innerHTML;
//******************************     全局变量  end    ***************************

//自适应宽度
var tab_click = document.getElementById("tab_click");
var container_width = document.getElementById("container").style.width;
var div_length = tab_click.children.length ;
document.getElementById("tab_click").style.width=container_width;
var tab_chile_width=document.querySelectorAll('.tab_width');
for(var t=0;t<tab_chile_width.length;t++) {
    tab_chile_width[t].style.width=100/div_length+"%";
    if(t==tab_chile_width.length-1){
        tab_chile_width[t].style.borderTopRightRadius="10px";
        tab_chile_width[t].style.overflow="hidden";
    }else if(t==0){
        tab_chile_width[t].style.borderTopLeftRadius="10px";
        tab_chile_width[t].style.overflow="hidden";
        tab_chile_width[t].style.background="white";
        tab_chile_width[t].style.color="black";
    }
}
<!--表格id-->
//    按回车键触发搜索按钮
document.getElementById("sou_sou").onkeydown = function () {
    if (event.keyCode == 13) {
        find_btt.click();
    }
};

//*****************************     select列表，input，日期 切换 start     ***********************
function prov_change() {
    var opt = "";
    var sel = document.getElementById("prov");
    if (sel.value == "export_campus" || sel.value == "fold_campus") {
        for (var key in arr_class[0]) {
            opt = opt + '<option value="' + arr_class[0][key] + '">' + arr_class[0][key]+ '</option>';
        }
    } else if (sel.value == "export_post" || sel.value == "fold_post") {
        for (var key in arr_class[2]) {
            opt = opt + '<option value="' + arr_class[2][key] + '">' + arr_class[2][key]+ '</option>';
        }
    }
    document.getElementById("city").innerHTML = opt;
}
document.getElementById("prov").addEventListener("change", function () {
    var porv_val = document.getElementById("prov").value;
    if (porv_val == "name") {
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        var find_name=document.getElementById("find");
        document.getElementById("click_findname").onclick=function(){
            document.getElementById("show_li").style.display="block";
        }
        $(function(){
            for(var i=0;i<arr_staff.length;i++){
                var show_li=document.createElement("li");
                document.getElementById("show_ul").appendChild(show_li).innerHTML="<div class='none_li'>"+arr_staff[i].name+"</div>";
            }
            $("#find").keyup(function(){

                $("#show_ul li").hide();
                $("#show_ul li:contains('"+$("#find").val()+"')").show();
            });
        });
        document.getElementById("show_ul").addEventListener("click",function(l){
            var target= l.target;
            find_name.value=target.innerHTML;
            $("#show_ul li").hide();
        });
        window.onclick=function(){
            $("#show_ul li").hide();
        }
        city.style.display = "none";
    }else if(porv_val=="user"){
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        city.style.display = "none";
    } else if (porv_val == "time") {
        document.getElementById("prov_append").innerHTML = "<input placeholder='最小时间' class='laydate-icon' onclick='laydate()' id='laydate_icon' name='date_star'><input placeholder='最大时间' class='laydate-icon' name='date_over' onclick='laydate()' id='laydate_icon'>";
        city.style.display = "none";
    } else if (porv_val == "0") {
        document.getElementById("prov_append").innerHTML = "";
        city.style.display = "none";
    } else {
        document.getElementById("prov_append").innerHTML = "";
        city.style.display = "block";
    }

});
//*****************************     select列表，input，日期 切换 end     ***********************







//页面加载事件  start
window.onload = function () {
    var reques = new XMLHttpRequest();
    reques.open("get", "/Content/post");
    reques.send();
    reques.onreadystatechange = function () {
        if (reques.readyState == 4 && reques.status == 200) {
            var json_text = JSON.parse(reques.responseText);

            for (var key in json_text) {
                arr_class.push(json_text[key]);
            }
        }
    }
    var check_append = [];
    $.ajax({
        url: "/Index/modules",
        data: {"the_level": 1},
        type: "post",
        async: "false",
        success: function (data) {
            var check_append_modules = [];
            msg= JSON.parse(data);
            var arr_num = [];
            for (key in msg[0]) {
                if (key != "status" && msg["status"] != 2) {
                    check_append_modules.push(msg[0][key]);
                }
            }
            for (var i = 0; i < check_append_modules.length; i++) {
                var append_div = document.createElement("div");
                document.getElementById("tab_click").appendChild(append_div).innerHTML = "<div class='click_modules'>"+  check_append_modules[i].modules_name+"</div>";
            }
            var click_div=document.getElementById("tab_click");
            var tab_child=document.querySelectorAll('.click_modules');
            click_div.style.width="95%";
            var tab_child=document.querySelectorAll('.click_modules');
            for(var aa=0;aa<tab_child.length;aa++){
                tab_child[aa].parentNode.style.width=100/tab_child.length+"%";
                if(tab_child[aa].innerText==="员工调动"){
                    tab_child[aa].style.background="white";
                    tab_child[aa].style.color="black";

                };
                if(aa==0){
                    tab_child[aa].style.borderTopLeftRadius = "10px";
                }else if(aa==tab_child.length-1){
                    tab_child[aa].style.borderTopRightRadius = "10px";
                }
            }
            click_div.addEventListener("click", function (e) {
                var target = e.target;
                for (var j = 0; j < check_append_modules.length; j++) {
                    if (target.innerText == check_append_modules[j].modules_name) {
                        location.href = "" + check_append_modules[j].modules_file;
                    }
                }
            });
        }
    });
};
//页面加载事件  end

//alert(json_text[key][0].class);

/*
 * 循环显示内容函数
 * page=分页页码
 * num=排序正反
 * content=点击要排序字段
 * start
 * */
function ArrSort(page, num, content) {
    var len;
    var page_str;
    if (typeof page === "undefined") {
        page = 0;
        len = arr_staff.length;
    } else {
        if (arr_staff.length >= (page + 1) * PageSize) {
            len = (page + 1) * PageSize;
        } else {
            len = arr_staff.length;
        }
    }
    var arr_num = [];
    for (var l = 0; l < len; l++) {
        arr_num[l] = l + 1;
    }

    for (var j = page * PageSize; j < len; j++) {
            if(arr_staff[j].name == null){
                arr_staff[j].name = "无数据";
            }
            if(arr_staff[j].export_campus == null){
                arr_staff[j].export_campus = "无数据";
            }
            if(arr_staff[j].export_post == null){
                arr_staff[j].export_post = "无数据";
            }
            if(arr_staff[j].fold_campus == null){
                arr_staff[j].fold_campus = "无数据";
            }
            if(arr_staff[j].fold_post == null){
                arr_staff[j].fold_post = "无数据";
            }
            if(!arr_staff[j].time){
                arr_staff[j].time = "无数据";
            }
            if(!arr_staff[j].state){
                arr_staff[j].state = "无数据";
            }
            var tr = document.createElement("tr");
            tb.appendChild(tr).innerHTML =
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_num[j] + "</div>" + "</td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].user + "</div>" + "<input type='hidden' class='user_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].name + "</div>" + "<input type='hidden' class='name_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div id='diaochu_bumen' name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].export_campus + "</div>" + "<input type='hidden' class='export_campus_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].export_post + "</div>" + "<input type='hidden' class='export_post_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div id='diaoru_bumen' name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].fold_campus + "</div>" + "<input type='hidden' class='fold_campus_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].fold_post + "</div>" + "<input type='hidden' class='fold_post_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].time + "</div>" + "<input type='hidden' class='time_sort' value='" + 1 + "'></td>" + 
                "<td class='td_sty' id='td_styd'>" + "<div id='shiyou_inner' name='" + arr_staff[j].user + "'>" + arr_staff[j].state + "</div>" + "<input type='hidden' class='state_sort' value='" + 1 + "'></td>";
        pageall.style.display = "block";
        pagemin.style.display = "block";
        pagemax.style.display = "block";
        copy_excel.style.display = "block";
        copy_dayin.style.display = "block";
    }


    if (num == 2) {
        $("." + content + "_sort:first").val("2");
    } else {
        $("." + content + "_sort:first").val("1");
    }
    //下面是分页处理
    CountPage = arr_staff.length % PageSize === 0 ? (arr_staff.length / PageSize) : (parseInt(arr_staff.length / PageSize) + 1);
    var page_current = (parseInt(page) + 1);
    var page_top = "page_top";
    var page_next = "page_next";
    if (document.getElementById("page_min").style.display=="block") {
        page_str = "<input type='button' value='上一页' class='page_top' onclick='page_page(" + page_current + "," + '"' + page_top + '"' + ")' id='page_top'>";
    }else if(tb.innerHTML==""){
        document.getElementById("page_min").display="none";
        document.getElementById("page_max").display="none";
        document.getElementById("page_all").display="none";
    }

    //************************     判断当前页     *****************************
    if (page_current == 1 || page_current == 2 || page_current == 3) {
        if (CountPage <= 5) {
            for (var page_i = 1; page_i <= CountPage; page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        } else {
            for (var page_i = 1; page_i <= 5; page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        }

    } else if (page_current == CountPage || page_current == (CountPage - 1) || page_current == (CountPage - 2)) {
        if (CountPage <= 5) {
            for (var page_i = 1; page_i <= CountPage; page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        } else {
            for (var page_i = (CountPage - 4); page_i <= CountPage; page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        }
    } else {
        if (CountPage <= 5) {
            for (var page_i = 1; page_i <= CountPage; page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        } else {
            for (var page_i = (page_current - 2); page_i <= (page_current + 2); page_i++) {
                page_str += "<span class='page' id='page" + (page_i) + "' onclick='page_page(" + (page_i) + ")'>" + (page_i) + "</span>";
            }
        }
    }
    if (document.getElementById("page_min").style.display=="block") {
        page_str += "<input type='button' class='page_next' value='下一页' onclick='page_page(" + page_current + "," + '"' + page_next + '"' + ")' id='page_next'>";
    }
    $("#page_all").html(page_str);
    document.getElementById("page" + page_current).style.backgroundColor = "#00c8c8";
    document.getElementById("pageall").innerHTML = "共" + CountPage + "页";
}
//分页处理搜索数据等  end



//员工信息页面搜索按钮  start
find_btt.onclick = function () {
    document.getElementById("sousuo_img_out").style.display="block";
    document.querySelector(".hongwen_logo").style.display="none";
    var sel = document.getElementById("prov");
    if (sel.value == "export_campus") {
        var status = "export_campus";
        var content = $("#city").val();
    } else if (sel.value == "export_post") {
        var status = "export_post";
        var content = $("#city").val();
    } else if (sel.value == "fold_campus") {
        var status = "fold_campus";
        var content = $("#city").val();
    } else if (sel.value == "fold_post") {
        var status = "fold_post";
        var content = $("#city").val();
    } else if (sel.value == "time") {
        var date_star = document.getElementsByName("date_star")[0].value;
        var date_over = document.getElementsByName("date_over")[0].value;
        var dateStar_str = new Date(date_star).getTime();
        var dateOver_str = new Date(date_over).getTime();

        if (dateOver_str < dateStar_str) {
            alert("输入查询时间有误！");
            return false;
        } else {
            var status = "time";
            var content = date_star + "," + date_over;
        }

    } else if (sel.value == "name") {
        var status = "name";
        var content = $("#find").val();
    } else if (sel.value == "user") {
        var status = "user";
        var content = $("#find").val();
    }
    $("#tb tr:not(:first)").empty();
    arr_staff = [];
    var xhr = new XMLHttpRequest();
    xhr.open("get", "/Content/change_find/content/" + content + "/status/" + status);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("sousuo_img_out").style.display="none";
            var msg = JSON.parse(xhr.responseText);
            for (key in msg) {
                if(key!="status" && msg["status"]!=2){
                    arr_staff.push(msg[key]);
                    arr_staff.sort(function (b, a) {
                        return a["time"].localeCompare(b["time"]);
                    });
                }
            }
            if(msg["status"]!=2){
                ArrSort(0);
            }else{
                document.getElementById("sousuo_img_out").style.display = "none";
                document.querySelector(".hongwen_logo").style.display="block";
                alert("根据查询，无数据~！");
            }
        }
    }
};
document.getElementById("tb_parent").onscroll=function(){
    var top_nav=document.querySelectorAll(".top_nav div");
    var top_wid=document.querySelectorAll(".top_wid div");
    var float_tt=document.querySelector(".float_top_top");
    if (document.getElementById("tb_parent").scrollTop > 0) {
        document.getElementById("tb_parent").style.top="-30px";
        document.getElementById("page_all").style.position="relative";
        document.getElementById("page_min").style.position="relative";
        document.getElementById("page_max").style.position="relative";
        document.getElementById("pageall").style.position="relative";
        document.getElementById("copy_excel").style.position="relative";
        document.getElementById("copy_dayin").style.position="relative";
        document.getElementById("page_all").style.top="-30px";
        document.getElementById("page_min").style.top=0;
        document.getElementById("page_max").style.top=0;
        document.getElementById("pageall").style.top=0;
        document.getElementById("copy_excel").style.top="-30px";
        document.getElementById("copy_dayin").style.top="-30px";
        document.querySelector(".max_head").style.display="block";
        for(var i=0;i<top_nav.length;i++){
            for(var j=0;j<top_wid.length;j++){
                if(top_nav[i].innerText==top_wid[j].innerText){
                    top_nav[i].style.width=top_wid[j].clientWidth+"px";
                }
            }
        }
    } else {
        document.querySelector(".max_head").style.display="none";
        document.getElementById("tb_parent").style.top=0;
        document.getElementById("page_all").style.top=0;
        document.getElementById("page_min").style.top="30px";
        document.getElementById("page_max").style.top="30px";
        document.getElementById("pageall").style.top="30px";
        document.getElementById("copy_excel").style.top=0;
        document.getElementById("copy_dayin").style.top=0;
    }
};
//页面搜索按钮  end



//打印页面   start
function dayin_blank() {

    if ($("tr:first").text() != "") {
        window.open("/Staff/dayin");
    } else {
        alert("程序有误，请联系管理员！");
    }
}
//打印页面  end


//excel操作  start
function excel_blank() {

    if ($("tr:first").text() != "") {
        window.open("/Staff/php_excel");
    } else {
        alert("程序有误，请联系管理员！");
    }
}
//excel操作  end






//jQuery的makeArray
function makeArray(array) {
    var ret = [];
    if (array != null) {
        var i = array.length;
        // The window, strings (and functions) also have 'length' 
        if (i == null || typeof array === "string" || jQuery.isFunction(array) || array.setInterval)
            ret[0] = array;
        else
            while (i)
                ret[--i] = array[i];
    }
    return ret;
}


//分页点击事件上，页码，下
function page_page(page, page_status) {
    if (page_status == "page_top" && page != 1) {
        tb.innerHTML = tb_content;
        ArrSort(page - 2)
    } else if (page_status == "page_next" && page != CountPage) {
        tb.innerHTML = tb_content;
        ArrSort(page)
    } else {
        tb.innerHTML = tb_content;
        ArrSort(page - 1)
    }
}

/*
 * 排序函数
 * content=点击要排序字段
 * */
function namesort(content) {
    var sort = $("." + content + "_sort:first").val();
    if (sort == 1) {
        arr_staff.sort(function (a, b) {
            return a[content].localeCompare(b[content]);
        });
        tb.innerHTML = tb_content;
        ArrSort(0, 2, content);
    } else {
        arr_staff.sort(function (b, a) {
            return a[content].localeCompare(b[content]);
        });
        tb.innerHTML = tb_content;
        ArrSort(0, 1, content);
    }
}

//*************************     时间插件     ************************
!function () {
    laydate({
        elem: '#demo'
    })
}();


