/**
 * Created by Administrator on 2016/3/30.
 */



//******************************     全局变量  start    ***************************
var arr_staff = [];
var arr_class = new Array();
var CurrentPage = 0, CountPage, PageSize = 50;
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
//var tb_content = tb.innerHTML;
//******************************     全局变量  end    ***************************

var tab_click = document.getElementById("tab_click");
//var container_width = document.getElementById("container").style.width;
var div_length = tab_click.children.length ;
//document.getElementById("tab_click").style.width=container_width;
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
    //按回车键触发搜索按钮
//document.getElementById("sou_sou").onkeydown = function () {
//    if (event.keyCode == 13) {
//        find_btt.click();
//    }
//};

//*****************************     select列表，input，日期 切换 start     ***********************
function prov_change() {
    var opt = "";
    var sel = document.getElementById("prov");
    if (sel.value == "campus") {
        for (var i = 0; i < arr_class[0].length; i++) {
            opt = opt + '<option value="' + arr_class[0][i] + '">' + arr_class[0][i] + '</option>';
        }
    } 
    document.getElementById("city").innerHTML = opt;

}
document.getElementById("prov").addEventListener("change", function () {
    var porv_val = document.getElementById("prov").value;
    if (porv_val == "name") {
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        city.style.display = "none";
    }else if (porv_val == "user") {
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        city.style.display = "none";
    } else if (porv_val == "time") {
        document.getElementById("prov_append").innerHTML = "<input type='month' placeholder='选取查询时间段' class='laydate-icon' id='laydate_icon' name='date_star'>";
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

    var check_append = [];
    $.ajax({
        url: "/index.php/Home/Index/modules",
        data: {"the_level": 2},
        type: "post",
        async: "false",
        success: function (data) {
            var check_append_modules = [];
            msg = JSON.parse(data);
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
            click_div.style.width="95%";
            var tab_child=document.querySelectorAll('.click_modules');
            for(var aa=0;aa<tab_child.length;aa++){
                tab_child[aa].parentNode.style.width=100/tab_child.length+"%";
                if(tab_child[aa].innerText=="打卡记录"){
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
                        location.href = "/index.php/Home/" + check_append_modules[j].modules_file;
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
            var tr = document.createElement("tr");
            tb.appendChild(tr).innerHTML =
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_num[j] + "</div>" + "</td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].user + "</div>" + "<input type='hidden' class='user_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].name + "</div>" + "<input type='hidden' class='name_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].campus + "</div>" + "<input type='hidden' class='campus_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].post + "</div>" + "<input type='hidden' class='post_sort' value='" + 1 + "'></td>" +
                "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].atten_time + "</div>" + "<input type='hidden' class='atten_time_sort' value='" + 1 + "'></td>";
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
    document.querySelector(".hongwen_logo").style.display="none";
    document.getElementById("sousuo_img_out").style.display="block";
    var sel = document.getElementById("prov");
    if (sel.value == "campus") {
        var status = "campus";
        var content = $("#city").val();
    } else if (sel.value == "time") {
        var status = "time";
        var content = $("#laydate_icon").val();
    } else if (sel.value == "name") {
        var status = "name";
        var content = $("#find").val();
    } else if (sel.value == "user") {
        var status = "user";
        var content = $("#find").val();
    }else{
        var status = "";
        var content = "";
    }
    $("#tb tr:not(:first)").empty();
    arr_staff = [];
    $.ajax({
        url: "/index.php/Home/Checking/index_select",
        data: {'content': content,'status':status},
        type: "post",
        async: "false",
        traditional: "true",
        cache: "false",
        success: function (xhr) {
            var msg = JSON.parse(xhr);
            for (key in msg) {
                if(key!="status" && msg["status"]!=2){
                    document.getElementById("sousuo_img_out").style.display="none";
                    arr_staff.push(msg[key]);
                }
            }
            if(msg["status"]!=2){
                ArrSort(0);
            }else{
                document.getElementById("sousuo_img_out").style.display="none";
                document.querySelector(".hongwen_logo").style.display="block";
                alert("根据查询，无数据~！");
            }

        }

    });
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
        window.open("/index.php/Home/Staff/check_index_excel");
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
        location.href = "/index.php/Home/Checking/check_index";
    } else if (tabb == 2) {
        location.href = "/index.php/Home/Checking/check_check";
    } else if (tabb == 3) {
        location.href = "/index.php/Home/Checking/check_checkrules";
    }
}

var tab = document.getElementById("tab_click");
//tab.addEventListener("click", function (e) {
//    var target = e.target;
//    tabb1.style.background = "#bbbbbb";
//    tabb2.style.background = "#bbbbbb";
//    tabb3.style.background = "#bbbbbb";
//    tabb1.style.color = "white";
//    tabb2.style.color = "white";
//    tabb3.style.color = "white";
//    target.style.background = "white";
//    target.style.color = "black";
//}, false);
document.onscroll = function () {
    if (scrollY > 200) {
        $("#trr").addClass("setcss_check_index");
        $("#trr th").addClass("setcss_check1_index");
        $("#trr th div").addClass("setcss_check_border");
    } else {
        $("#trr").removeClass("setcss_check_index");
        $("#trr th").removeClass("setcss_check1_index");
        $("#trr th div").removeClass("setcss_check_border");
    }
}
