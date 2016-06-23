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
    if (sel.value == "place") {
        $.ajax({
            url: "/index.php/Home/Staff/area_school",
            data: {},
            type: "post",
            async: "false",
            traditional: "true",
            cache: "false",
            success: function (msg) {
                var msgs = JSON.parse(msg);
                for (var i in msgs) {
                    opt += "<option value='" + msgs[i].name + "'>" + msgs[i].name + "</option>";
                }
            document.getElementById("city").innerHTML = opt;
            }

        });
    }

}
document.getElementById("click_findname").onclick=function(){
    document.getElementById("show_li").style.display="block";
}
document.getElementById("prov").addEventListener("change", function () {
    var porv_val = document.getElementById("prov").value;
    if (porv_val == "name") {
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        var find_name=document.getElementById("find");

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
    }else if (porv_val == "user") {
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
    reques.open("get", "/index.php/Home/Content/post");
    reques.send();
    reques.onreadystatechange = function () {
        if (reques.readyState == 4 && reques.status == 200) {
            var json_text = JSON.parse(reques.responseText);

            for (var key in json_text) {
                arr_class.push(json_text[key]);
            }
        }
    }
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
            var tr = document.createElement("tr");
            tb.appendChild(tr).innerHTML =
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_num[j] + "</div>" + "</td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].user + "</div>" + "<input type='hidden' class='user_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].name + "</div>" + "<input type='hidden' class='name_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].campus + "</div>" + "<input type='hidden' class='campus_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].post + "</div>" + "<input type='hidden' class='post_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].is_no + "</div>" + "<input type='hidden' class='is_no_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].condition + "</div>" + "<input type='hidden' class='condition_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].begin_time + "</div>" + "<input type='hidden' class='begin_time_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].place + "</div>" + "<input type='hidden' class='place_sort' value='" + 1 + "'></td>";
        pageall.style.display = "block";
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
    var sel = document.getElementById("prov");
    if (sel.value == "place") {
        var status = "place";
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
    xhr.open("get", "/index.php/Home/Content/insure_find/content/" + content + "/status/" + status);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("sousuo_img_out").style.display="none";
            var msg = JSON.parse(xhr.responseText);
            for (key in msg) {
                if(key!="status" && msg["status"]!=2){
                    arr_staff.push(msg[key]);
                    arr_staff.sort(function (b, a) {
                        return a["begin_time"].localeCompare(b["begin_time"]);
                    });
                }
            }
            if(msg["status"]!=2){
                ArrSort(0);
            }else{
                document.getElementById("sousuo_img_out").style.display = "none";
                alert("根据查询，无数据~！");
            }
        }
    }
};
//页面搜索按钮  end



//打印页面   start
function dayin_blank() {

    if ($("tr:first").text() != "") {
        window.open("/index.php/Home/Staff/dayin");
    } else {
        alert("程序有误，请联系管理员！");
    }
}
//打印页面  end


//excel操作  start
function excel_blank() {

    if ($("tr:first").text() != "") {
        window.open("/index.php/Home/Staff/php_excel");
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


//*************************     布局分页     *************************
function blk(tabb) {
    if (tabb == 1) {
        location.href = "/index.php/Home/Content/staff";
    } else if (tabb == 2) {
        location.href = "/index.php/Home/Content/staff_move";
    } else if (tabb == 3) {
        alert("暂无此页面");
        location.href="/index.php/Home/Content/staff_insurance";
    } else if (tabb == 4) {
        location.href="/index.php/Home/Content/staff_jiangfa"
    } else if (tabb == 5) {
        alert("暂无此页面");
        location.href="/index.php/Home/Content/staff_insurance";
    } else if (tabb == 6) {
        location.href="/index.php/Home/Content/staff_insurance"
    } else if (tabb == 7) {
        location.href = "/index.php/Home/Content/staff_leave"
    }
}

var tab = document.getElementById("tab_click");
tab.addEventListener("click", function (e) {
    var target = e.target;
    tabb1.style.background = "#bbbbbb";
    tabb2.style.background = "#bbbbbb";
    tabb3.style.background = "#bbbbbb";
    tabb4.style.background = "#bbbbbb";
    tabb5.style.background = "#bbbbbb";
    tabb6.style.background = "#bbbbbb";
    tabb7.style.background = "#bbbbbb";
    tabb1.style.color = "white";
    tabb2.style.color = "white";
    tabb3.style.color = "white";
    tabb4.style.color = "white";
    tabb5.style.color = "white";
    tabb6.style.color = "white";
    tabb7.style.color = "white";
    target.style.background = "white";
    target.style.color = "black";
}, false);
