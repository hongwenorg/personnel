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
    if (sel.value == "campus") {
        for (var i = 0; i < arr_class[0].length; i++) {
            opt = opt + '<option value="' + arr_class[0][i] + '">' + arr_class[0][i] + '</option>';
        }
    }
    document.getElementById("city").innerHTML = opt;

}
document.getElementById("click_findname").onclick = function () {
    document.getElementById("show_li").style.display = "block";
}
document.getElementById("prov").addEventListener("change", function () {
    var porv_val = document.getElementById("prov").value;
    if (porv_val == "name") {
        document.getElementById("prov_append").innerHTML = "<input type='text' id='find'>";
        var find_name = document.getElementById("find");

        $(function () {
            for (var i = 0; i < arr_staff.length; i++) {
                var show_li = document.createElement("li");
                document.getElementById("show_ul").appendChild(show_li).innerHTML = "<div class='none_li'>" + arr_staff[i].name + "</div>";
            }
            $("#find").keyup(function () {

                $("#show_ul li").hide();
                $("#show_ul li:contains('" + $("#find").val() + "')").show();
            });
        });
        document.getElementById("show_ul").addEventListener("click", function (l) {
            var target = l.target;
            find_name.value = target.innerHTML;
            $("#show_ul li").hide();
        });
        window.onclick = function () {
            $("#show_ul li").hide();
        }
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
        if (arr_staff[j].name == null) {
            arr_staff[j].name = "无数据";
        }
        if (arr_staff[j].level == null) {
            arr_staff[j].level = "无";
        }
        if (arr_staff[j].factor == null) {
            arr_staff[j].factor = "无";
        }
        if (arr_staff[j].allowance == null) {
            arr_staff[j].allowance = "无";
        }
        if (arr_staff[j].late == null) {
            arr_staff[j].late = "无";
        }
        if (arr_staff[j].early == null) {
            arr_staff[j].early = "无";
        }
        if (arr_staff[j].personal_leave == null) {
            arr_staff[j].personal_leave = "无";
        }
        if (arr_staff[j].sick_leave == null) {
            arr_staff[j].sick_leave = "无";
        }
        if (arr_staff[j].overtime == null) {
            arr_staff[j].overtime = "无";
        }
        if (arr_staff[j].absenteeism == null) {
            arr_staff[j].absenteeism = "无";
        }
        if (arr_staff[j].noclock == null) {
            arr_staff[j].noclock = "无";
        }
        if (arr_staff[j].absenteeism !== 0) {
            arr_staff[j].absenteeism = "<div class='absen_red'>" + arr_staff[j].absenteeism + "</div>";
        }
        ;
        var tr = document.createElement("tr");
        tb.appendChild(tr).innerHTML =
            "<td class='td_sty td_a1' num='1'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_num[j] + "</div>" + "</td>" +
            "<td class='td_sty td_a2' num='2'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].name + "</div>" + "<input type='hidden' class='name_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a3' num='3'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].campus + "</div>" + "<input type='hidden' class='campus_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a4' num='4'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].post + "</div>" + "<input type='hidden' class='post_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a5' num='5'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].level + "</div>" + "<input type='hidden' class='level_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a6' num='6'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].factor + "</div>" + "<input type='hidden' class='factor_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a7' num='7'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].allowance + "</div>" + "<input type='hidden' class='allowance_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a8' num='8'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].late + "</div>" + "<input type='hidden' class='late_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a9' num='9'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].early + "</div>" + "<input type='hidden' class='early_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a10' num='10'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].personal_leave + "</div>" + "<input type='hidden' class='personal_leave_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a11' num='11'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].personal_leave_count + "</div>" + "<input type='hidden' class='personal_leave_count_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a12' num='12'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].sick_leave + "</div>" + "<input type='hidden' class='sick_leave_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a13' num='13'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].sick_leave_count + "</div>" + "<input type='hidden' class='sick_leave_count_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a14' num='14'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].overtime + "</div>" + "<input type='hidden' class='overtime_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a15' num='15'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].absenteeism + "</div>" + "<input type='hidden' class='absenteeism_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a16' num='16'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].noclock + "</div>" + "<input type='hidden' class='noclock_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a17' num='17'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].noclock + "</div>" + "<input type='hidden' class='noclock_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a18' num='18'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].over_count_day + "</div>" + "<input type='hidden' class='over_count_day_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a19' num='19'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].count_yes + "</div>" + "<input type='hidden' class='count_yes_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty td_a20' num='20'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].is_no + "</div>" + "<input type='hidden' class='is_no_sort' value='" + 1 + "'></td>";
        pageall.style.display = "block";
        pageall.style.display = "block";
        pagemin.style.display = "block";
        pagemax.style.display = "block";
        copy_excel.style.display = "block";
        copy_dayin.style.display = "block";
    }

    $(".td_sty").mouseover(function () {
        var num = $(this).attr('num');
        $(".td_a" + num).css("background-color", "#efefef");
        $(this).parent().children().css("background-color", "#efefef");
    });

    $(".td_sty").mouseout(function () {
        $(".td_sty").css("background-color", "white");
    });


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
    if (document.getElementById("page_min").style.display == "block") {
        page_str = "<input type='button' value='上一页' class='page_top' onclick='page_page(" + page_current + "," + '"' + page_top + '"' + ")' id='page_top'>";
    } else if (tb.innerHTML == "") {
        document.getElementById("page_min").display = "none";
        document.getElementById("page_max").display = "none";
        document.getElementById("page_all").display = "none";
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
    if (document.getElementById("page_min").style.display == "block") {
        page_str += "<input type='button' class='page_next' value='下一页' onclick='page_page(" + page_current + "," + '"' + page_next + '"' + ")' id='page_next'>";
    }
    $("#page_all").html(page_str);
    document.getElementById("page" + page_current).style.backgroundColor = "#00c8c8";
    document.getElementById("pageall").innerHTML = "共" + CountPage + "页";
}
//分页处理搜索数据等  end


//员工信息页面搜索按钮  start
find_btt.onclick = function () {
    document.getElementById("sousuo_img_out").style.display = "block";
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
    } else {
        var status = "";
        var content = "";
    }
    $("#tb tr:not(:first)").empty();
    arr_staff = [];
    var xhr = new XMLHttpRequest();
    xhr.open("get", "/index.php/Home/Checking/check_count/content/" + content + "/status/" + status);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("sousuo_img_out").style.display = "none";
            var msg = JSON.parse(xhr.responseText);
            console.log(msg);
            for (key in msg) {
                if (key != "status" && key != "month" && key != "week_day" && key != "count_day" && key != "over_count_day" && msg["status"] != 2) {
                    arr_staff.push(msg[key]);
                }
                else if (msg["status"] != 2) {
                    $("#check_month").text(msg['month'] + "月份考勤数据");
                    $("#check_count_day").text("共有" + msg['count_day'] + "天" + msg['week_day'] + "个周日");
                    $("#check_day_day").text("应出勤" + msg['over_count_day'] + "天");
                }
            }
            if (msg["status"] != 2) {
                ArrSort(0);
            } else {
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
        window.open("/index.php/Home/Staff/check_excel");
    } else {
        alert("程序有误，请联系管理员！");
    }
}
//excel操作  end


//个人考勤
document.getElementById("tb").addEventListener("click", function (e) {
    var target = e.target;
    for (var i = 0; i < arr_staff.length; i++) {
        if (arr_staff[i].name == target.innerHTML) {
            document.getElementById("kaoqin_out").style.display="block";
            var append_rules_yes;
            var append_rule;
            append_rule = document.createElement("div");
            append_rule.style.zIndex = "9999999";
            append_rule.style.boxShadow = "0 16px 100px gray";
            var append_rules_table = document.createElement("table");
            var append_rules_tr = document.createElement("tr");
            var append_rules_tr2 = "<td>" + arr_staff[i].name + "</td>" + "<td>" + "a" + "</td>" + "<td>" + "a" + "</td>" + "<td>" + "a" + "</td>" + "<td>" + "a" + "</td>";
            //弹出框的头部
            var append_rules_head = document.createElement("div");
            var append_rules_close = document.createElement("button");
            append_rules_head.style.width = "100%";
            append_rules_head.style.height = "40px";
            append_rules_head.style.background = "#555a5a";
            append_rules_head.style.color = "#00c8c8";
            append_rules_head.style.fontWeight = "bold";
            append_rules_head.style.lineHeight = "40px";
            append_rules_head.style.textAlign = "center";
            append_rules_head.innerText = "员工" + target.innerHTML + "打卡时间";
            append_rules_head.appendChild(append_rules_close);
            append_rules_close.style.position = "absolute";
            append_rules_close.style.right = 0;
            append_rules_close.style.width = "40px";
            append_rules_close.style.height = "40px";
            append_rules_close.style.fontSize = "30px";
            append_rules_close.style.lineHeight = 0;
            append_rules_close.style.border = "none";
            append_rules_close.style.backgroundColor = "#555a5a";
            append_rules_close.style.color = "#00c8c8";
            append_rules_close.innerText = "×";
            append_rules_close.style.right = 0;

            //弹出框的确认按扭
            append_rules_yes = document.createElement("input");
            append_rules_yes.style.width = "90%";
            append_rules_yes.style.height = "30px";
            append_rules_yes.style.marginLeft = "5%";
            append_rules_yes.style.position = "absolute";
            append_rules_yes.style.bottom = "10px";
            append_rules_yes.style.border = "none";
            append_rules_yes.style.backgroundColor = "#00c8c8";
            append_rules_yes.style.color = "white";

            //将弹出框 添加到 body下
            document.getElementById("append_rules").appendChild(append_rule);
            append_rule.style.background = "white";
            append_rule.style.width = "600px";
            append_rule.style.height = "400px";
            append_rule.style.position = "fixed";
            append_rule.style.left = "50%";
            append_rule.style.marginLeft = "-300px";
            append_rule.appendChild(append_rules_head);
            append_rule.appendChild(append_rules_table);
            append_rules_table.style.textAlign = "center";
            append_rules_table.style.width = "90%";
            append_rules_table.style.marginLeft = "5%";
            append_rules_table.style.border = "1px solid gray";
            append_rules_table.style.overflowY = "auto";
            append_rules_table.border = "1";
            append_rules_table.cellPadding = "2";  
            append_rules_table.ceelspacing = 0;
            append_rules_table.appendChild(append_rules_tr);
            append_rules_tr.innerHTML = "<td>" + "日期" + "</td>" + "<td>" + "上班时间" + "</td>" + "<td>" + "下班时间" + "</td>" + "<td>" + "是否合格" + "</td>" + "<td>" + "详情" + "</td>";
            append_rules_tr.insertAdjacentHTML("afterEnd", append_rules_tr2);
            append_rule.appendChild(append_rules_yes);
            append_rules_yes.type = "button";
            append_rules_yes.value = "确定";
            append_rules_yes.onclick = function () {
                append_rule.style.display = "none";
                document.getElementById("kaoqin_out").style.display="none";
            }
            append_rules_close.onclick = function () {
                append_rule.style.display = "none";
                document.getElementById("kaoqin_out").style.display="none";
            }
        }
    }
})


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
        location.href = "/index.php/Home/Checking/index";
    } else if (tabb == 2) {
        location.href = "/index.php/Home/Checking/check";
    } else if (tabb == 3) {
        location.href = "/index.php/Home/Checking/checkrules";
    }
}

var tab = document.getElementById("tab_click");
tab.addEventListener("click", function (e) {
    var target = e.target;
    tabb1.style.background = "#bbbbbb";
    tabb2.style.background = "#bbbbbb";
    tabb3.style.background = "#bbbbbb";
    tabb1.style.color = "white";
    tabb2.style.color = "white";
    tabb3.style.color = "white";
    target.style.background = "white";
    target.style.color = "black";
}, false);
document.onscroll = function () {
    if (scrollY > 200) {
        $("#trr").addClass("setcss_check1");
        $("#trr th").addClass("setcss_check");
        $("#trr th div").addClass("setcss_border");
    } else {
        $("#trr").removeClass("setcss_check1");
        $("#trr th").removeClass("setcss_check");
        $("#trr th div").removeClass("setcss_border");
    }
}