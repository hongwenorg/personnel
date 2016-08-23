/**
 * Created by Administrator on 2016/3/30.
 */



//******************************     全局变量  start    ***************************
var arr_staff = [];
var arr_class = new Array();
var CurrentPage = 0, CountPage, PageSize = 50;
var find_btt = document.getElementById("find_btt");
var close_diaodong = document.getElementById("close_diaodong");
var close_jiangcheng = document.getElementById("close_jiangcheng");
var close_ruzhi = document.getElementById("close_ruzhi");
var pageall = document.getElementById("pageall");
var pagemin = document.getElementById("page_min");
var pagemax = document.getElementById("page_max");
var copy_excel = document.getElementById("copy_excel");
var copy_dayin = document.getElementById("copy_dayin");
var tb = document.getElementById("tb");
var city = document.getElementById("city");
var tb_content = tb.innerHTML;
//******************************     全局变量  end    ***************************
//自适应宽度
//<!--表格id-->
//    按回车键触发搜索按钮
document.getElementById("sousuokuang").onkeydown = function () {
    if (event.keyCode == 13) {
        find_btt.click();
    }
};

//*****************************     select列表，input，日期 切换 start     ***********************
function prov_change() {
    var opt = "";
    var sel = document.getElementById("prov");
    if (sel.value == "campus") {
        for (var key in arr_class[0]) {
            opt = opt + '<option value="' + arr_class[0][key] + '">' + arr_class[0][key]+ '</option>';
        }
    } else if (sel.value == "post") {
        for (var key in arr_class[2]) {
            opt = opt + '<option value="' + arr_class[2][key] + '">' + arr_class[2][key] + '</option>';
        }
        for (var key in arr_class[3]) {
            opt = opt + '<option value="' + arr_class[3][key] + '">' + arr_class[3][key] + '</option>';
        }
    }
    document.getElementById("city").innerHTML = opt;

}
document.getElementById("click_findname").onclick = function () {
    document.getElementById("show_li").style.display = "block";
};
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
        };
        city.style.display = "none";
    } else if (porv_val == "user") {
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
    };
    var check_append = [];
    $.ajax({
        url: "/Index/modules",
        data: {"the_level": 1},
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
                document.getElementById("tab_click").appendChild(append_div).innerHTML = "<div class='click_modules'>" + check_append_modules[i].modules_name + "</div>";
            }
            var click_div = document.getElementById("tab_click");
            var tab_child = document.querySelectorAll('.click_modules');
            click_div.style.width = "95%";
            for (var aa = 0; aa < tab_child.length; aa++) {
                tab_child[aa].parentNode.style.width = 100 / tab_child.length + "%";
                if (tab_child[aa].innerText === "员工信息") {
                    tab_child[aa].parentNode.style.background = "white";
                    tab_child[aa].parentNode.style.color = "black";
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
        if (arr_staff[j].name == null) {
            arr_staff[j].name = "无数据";
        }if (arr_staff[j].entry_date == null) {
            arr_staff[j].entry_date = "无数据";
        }if (arr_staff[j].campus == null) {
            arr_staff[j].campus = "无数据";
        }if (arr_staff[j].post == null) {
            arr_staff[j].post = "无数据";
        }if (arr_staff[j].telephone == null) {
            arr_staff[j].telephone = "无数据";
        }if (!arr_staff[j].sex) {
            arr_staff[j].sex = "无数据";
        }if (!arr_staff[j].nation) {
            arr_staff[j].nation = "无数据";
        }if (!arr_staff[j].card) {
            arr_staff[j].card = "无数据";
        }if (!arr_staff[j].marriage) {
            arr_staff[j].marriage = "无数据";
        }if (!arr_staff[j].birthday) {
            arr_staff[j].birthday = "无数据";
        }if (!arr_staff[j].polity) {
            arr_staff[j].polity = "无数据";
        }if (!arr_staff[j].residence_booklet) {
            arr_staff[j].residence_booklet = "无数据";
        }if (!arr_staff[j].contacts) {
            arr_staff[j].contacts = "无数据";
        }if (!arr_staff[j].urgency_telephone) {
            arr_staff[j].urgency_telephone = "无数据";
        }if (!arr_staff[j].school) {
            arr_staff[j].school = "无数据";
        }if (!arr_staff[j].major) {
            arr_staff[j].major = "无数据";
        }if (!arr_staff[j].seniority) {
            arr_staff[j].seniority = "无数据";
        }if (!arr_staff[j].wechat) {
            arr_staff[j].wechat = "无数据";
        }if (!arr_staff[j].email) {
            arr_staff[j].email = "无数据";
        }if (!arr_staff[j].telephone) {
            arr_staff[j].telephone = "无数据";
        }if (!arr_staff[j].qq) {
            arr_staff[j].qq = "无数据";
        }
        var tr = document.createElement("tr");
        tb.appendChild(tr).innerHTML =
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_num[j] + "</div>" + "</td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].user + "</div>" + "<input type='hidden' class='user_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].name + "</div>" + "<input type='hidden' class='name_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].sex + "</div>" + "<input type='hidden' class='sex_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].nation + "</div>" + "<input type='hidden' class='nation_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].card + "</div>" + "<input type='hidden' class='card_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].marriage + "</div>" + "<input type='hidden' class='marriage_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].birthday + "</div>" + "<input type='hidden' class='birthday_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].polity + "</div>" + "<input type='hidden' class='polity_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].residence_booklet + "</div>" + "<input type='hidden' class='residence_booklet_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].live + "</div>" + "<input type='hidden' class='residence_live' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].contacts + "</div>" + "<input type='hidden' class='contacts_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].urgency_telephone + "</div>" + "<input type='hidden' class='urgency_telephone_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].school + "</div>" + "<input type='hidden' class='school_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].major + "</div>" + "<input type='hidden' class='major_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].seniority + "</div>" + "<input type='hidden' class='seniority_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].campus + "</div>" + "<input type='hidden' class='campus_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].post + "</div>" + "<input type='hidden' class='post_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].entry_date + "</div>" + "<input type='hidden' class='entry_date_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].telephone + "</div>" + "<input type='hidden' class='telephone_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].qq + "</div>" + "<input type='hidden' class='qq_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].wechat + "</div>" + "<input type='hidden' class='wechat_sort' value='" + 1 + "'></td>" +
            "<td class='td_sty'>" + "<div name='" + arr_staff[j].user + "' class='div_inner'>" + arr_staff[j].email + "</div>" + "<input type='hidden' class='email_sort' value='" + 1 + "'></td>";
        pageall.style.display = "block";
        pagemin.style.display = "block";
        pagemax.style.display = "block";
        copy_excel.style.display = "block";
        copy_dayin.style.display = "block";
        console.log(arr_staff[j]);
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
    document.querySelector(".hongwen_logo").style.display="none";

    var sel = document.getElementById("prov");
    if (sel.value == "campus") {
        var status = "campus";
        var content = $("#city").val();
        document.getElementById("sousuo_img_out").style.display = "block";
    } else if (sel.value == "teacher") {
        var status = "teacher";
        var content = $("#city").val();
        document.getElementById("sousuo_img_out").style.display = "block";
    } else if (sel.value == "post") {
        var status = "post";
        var content = $("#city").val();
        document.getElementById("sousuo_img_out").style.display = "block";
    } else if (sel.value == "time") {
        var date_star = document.getElementsByName("date_star")[0].value;
        var date_over = document.getElementsByName("date_over")[0].value;
        var dateStar_str = new Date(date_star).getTime();
        var dateOver_str = new Date(date_over).getTime();
        document.getElementById("sousuo_img_out").style.display = "block";

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
        document.getElementById("sousuo_img_out").style.display = "block";
    } else if (sel.value == "user") {
        var status = "user";
        var content = $("#find").val();
        document.getElementById("sousuo_img_out").style.display = "block";
    }
    $("#tb tr:not(:first)").empty();

    arr_staff = [];
    var xhr = new XMLHttpRequest();
    xhr.open("get", "/Content/page_con/content/" + content + "/status/" + status);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("sousuo_img_out").style.display = "none";
            var msg = JSON.parse(xhr.responseText);
            for (key in msg) {
                if (key != "status" && msg["status"] != 2) {
                    arr_staff.push(msg[key]);
                    arr_staff.sort(function (b, a) {
                        return a["entry_date"].localeCompare(b["entry_date"]);
                    });
                }
            }
            if (msg["status"] != 2) {
                ArrSort(0);
            } else {
                document.getElementById("sousuo_img_out").style.display = "none";
                document.querySelector(".hongwen_logo").style.display="block";
                alert("根据查询，无数据~！");
            }
        }
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


//***************************     点击table 页面显示相对应人名的数据   start   ***********************
tb.addEventListener("click", function (d) {
    var target_div = $(d.target).attr("name");
    for (var i = 0; i < arr_staff.length; i++) {
        if (arr_staff[i].user == target_div) {
            var staff_all = document.getElementById("staff_all");
            var staff_out = document.getElementById("staff_out");
            var jC_inner = document.getElementById("jiangcheng_inner");
            var ruzhi_inner = document.getElementById("ruzhi_inner");
            var dD_inner = document.getElementById("diaodong_inner");
            var xg_inner = document.getElementById("staff_xiugai_inner");
            staff_out.style.display = "block";
            staff_all.style.display = "block";
            $("#user_card").val(target_div);
            jC_inner.innerText = "员工 " + arr_staff[i].name + " 奖励/处罚";
            dD_inner.innerText = "员工 " + arr_staff[i].name + " 岗位/职位调动";
            xg_inner.innerText = "员工 " + arr_staff[i].name + " 功能修改";
            ruzhi_inner.innerText = "员工 " + arr_staff[i].name + " 入职/离职";
        }
    }
});
//***************************     点击table 页面显示相对应人名的数据   end   ***********************


//奖惩记录 start
$("#but_record").click(function () {
    if ($("#shape").val() == "奖励/处罚") {
        alert("请选择奖惩形式！");
        return false;
    }
    if ($("#time").val() == "") {
        alert("请选择奖惩时间！");
        return false;
    }
    if ($("#result").val() == "") {
        alert("奖惩结果不能为空！");
        return false;
    }
    if ($("#why_chengfa").val() == "") {
        alert("奖惩原因不能为空！");
        return false;
    }
    //所有信息无误走提交
    if (confirm("是否保存员工信息")) {
        var data = $('#record_form').serialize();//将表单数据以字符串形式获取
        var user_card = $("#user_card").val();
        $.ajax({
            url: "/Staff/staff_record",
            data: {'data': data, 'user_card': user_card},
            type: "post",
            async: "false",
            traditional: "true",
            cache: "false",
            success: function (msg) {
                if (msg == 1) {
                    alert("保存成功！");
                    document.getElementById("staff_out").style.display = "none";
                    document.getElementById("staff_all").style.display = "none";
                    close_jiangcheng.click();
                    find_btt.click();
                } else {
                    alert("保存失败！");
                }

            }

        });
    }
});
//奖惩记录 end


//入职离职 start
$("#entry_leave").click(function () {
    //所有信息无误走提交
    if (confirm("是否保存员工信息")) {
        var data = $('#ruzhi_form').serialize();//将表单数据以字符串形式获取
        var user_card = $("#user_card").val();
        $.ajax({
            url: "/Staff/leave_pro",
            data: {'data': data, 'user_card': user_card},
            type: "post",
            async: "false",
            traditional: "true",
            cache: "false",
            success: function (msg) {
                if (msg == 1) {
                    alert("保存成功！");
                    document.getElementById("staff_out").style.display = "none";
                    document.getElementById("staff_all").style.display = "none";
                    close_ruzhi.click();
                    find_btt.click();
                } else {
                    alert("保存失败！");
                }

            }

        });
    }
});
//入职离职 end


//岗位调动操作 start
$("#but_post").click(function () {
    var export_campus = $("#export_campus").val();
    var export_post = $("#export_post").val();
    var fold_campus = $("#fold_campus").val();
    var fold_post = $("#fold_post").val();
    var staff_time = $("#staff_time").val();
    if (fold_campus == "请选择") {
        alert("请选择调入后单位！");
        return false;
    }
    if (fold_post == "请选择") {
        alert("请选择调入后职务！");
        return false;
    }
    if (staff_time == "") {
        alert("调动时间不能为空！");
        return false;
    }
    //所有信息无误走提交
    if (confirm("是否保存员工信息")) {
        var data = $('#export_fold').serialize();//将表单数据以字符串形式获取
        var user_card = $("#user_card").val();
        $.ajax({
            url: "/Staff/export_fold_pro",
            data: {'data': data, 'user_card': user_card},
            type: "post",
            async: "false",
            traditional: "true",
            cache: "false",
            success: function (msg) {
                alert(msg);
                return;
                if (msg == 1) {
                    alert("保存成功！");
                    document.getElementById("staff_out").style.display = "none";
                    document.getElementById("staff_all").style.display = "none";
                    close_diaodong.click();
                    find_btt.click();
                } else {
                    alert("保存失败！");
                }

            }

        });
    }
});
//岗位调动操作 end


//添加新员工按钮出发事件  start
$("#btt").click(function () {
    var close_append = document.getElementById("close_append");
    var append_out = document.getElementById("append_out");
    $.get('append_staff.html', function (data) {
        $('.my_form_div').prepend(data);
        $(".my_div").css("display", "block");
        append_out.style.display = "block";
        close_append.style.display = "block";
        //    获取当前时间
    })
});


//添加新员工按钮出发事件  end


//弹出框的关闭按钮  start
$("#close_append").click(function sdc() {
    var close_append = document.getElementById("close_append");
    var append_out = document.getElementById("append_out");
    $('.my_form_div').html("");
    $(".my_div").css("display", "none");
    append_out.style.display = "none";
    close_append.style.display = "none";
});
//弹出框的关闭按钮  end


append_out.onmousewheel = function () {
    return true;
};


//点击入职离职时调用  start
$("#staff_rz").click(function () {
    document.getElementById("append_out").style.display = "block";
    document.getElementById("staff_ruzhi").style.display = "block";
    $("#contract_date").val("");
    $("#entry_date").val("");
    $("#predict_date").val("");
    $("#reality_date").val("");
    $("#leave_type").val("");
    $("#leave_reason").val("");
    $("#leave_date").val("");
    $("#tail_after_value").val("");
    var user_card = $("#user_card").val();
    var data_arr = [];
    var post_post = $("#post_post");
    var campus_post = $("#campus_post");
    post_post.children().remove();
    campus_post.children().remove();
    //获取员工信息
    $.ajax({
        url: "/Staff/staff_entry_leave",
        data: {"user": user_card},
        type: "post",
        async: "false",
        success: function (data) {
            var txt = JSON.parse(data);
            data_arr = txt;
            $("#contract_date").val(data_arr.contract_date);
            $("#entry_date").val(data_arr.entry_date);
            $("#reality_date").val(data_arr.reality_date);
            $("#leave_type").val(data_arr.leave_type);
            $("#leave_reason").val(data_arr.leave_reason);
            $("#leave_date").val(data_arr.leave_date);
            $("#tail_after_value").val(data_arr.tail_after_value);
            var campus = data_arr.campus;
            var post = data_arr.post;
            var str = "<option value='请选择'>请选择</option>";
            var opt = "<option value='请选择'>请选择</option>";
            for (var key in arr_class[0]) {
                if (campus.indexOf(arr_class[0][key]) != -1) {
                    str += '<option selected value="' + arr_class[0][key] + '">' + arr_class[0][key] + '</option>';
                } else {
                    str += '<option value="' + arr_class[0][key] + '">' + arr_class[0][key] + '</option>';
                }
            }
            campus_post.prepend(str);
            for (var key in arr_class[2]) {
                if (post.indexOf(arr_class[2][key]) != -1) {
                    opt += '<option selected value="' + arr_class[2][key] + '">' + arr_class[2][key] + '</option>';
                } else {
                    opt += '<option value="' + arr_class[2][key] + '">' + arr_class[2][key] + '</option>';
                }
            }
            post_post.prepend(opt);
        }
    });
});
//点击入职离职时调用  end


//点击员工基本信息时调用  start
$("#staff_xinxi").click(function () {
    $.get('append_staff.html', function (data) {
        $('.my_form_div').prepend(data);
        $(".my_div").css("display", "block");
        append_out.style.display = "block";
        close_append.style.display = "block";
        $(".staf_bt1").val("员工号");
        document.getElementById("append_head").innerHTML = "修改员工信息";
        var user_card = $("#user_card").val();
        $(".user_div").text(user_card);
        $(".user").val(user_card);
        var basic_arr = [];
        var insure_arr = [];
        var work_undergo_arr = [];
        //获取员工信息
        $.ajax({
            url: "/Staff/staff_user",
            data: {"user": user_card},
            type: "post",
            async: "false",
            success: function (data) {
                var txt = JSON.parse(data);
                if (txt.status == 2) {
                    alert("程序出错，请联系管理员！");
                    return false;
                } else {
                    basic_arr = txt.basic;
                    insure_arr = txt.insure;
                    work_undergo_arr = txt.work_undergo;
                    $("#append_name").val(basic_arr.name);
                    $("#nation").val(basic_arr.nation);
                    $("#sex").val(basic_arr.sex);
                    $("#marriage").val(basic_arr.marriage);
                    $("#card").val(basic_arr.card);
                    $("#birthday").val(basic_arr.birthday);
                    $("#polity").val(basic_arr.polity);
                    $("#residence_booklet").val(basic_arr.residence_booklet);
                    $("#live").val(basic_arr.live);
                    $("#append_tel").val(basic_arr.telephone);
                    $("#contacts").val(basic_arr.contacts);
                    $("#urgency_telephone").val(basic_arr.urgency_telephone);
                    $("#school").val(basic_arr.school);
                    $("#major").val(basic_arr.major);
                    $("#education").val(basic_arr.education);
                    $("#degree").val(basic_arr.degree);
                    $("#seniority").val(basic_arr.seniority);
                    $("#qq").val(basic_arr.qq);
                    $("#email").val(basic_arr.email);
                    $("#wechat").val(basic_arr.wechat);
                    $("#check").val(basic_arr.check);
                    $("#user_id").val(basic_arr.user_id);
                    if (basic_arr.photo_max_url != "" || basic_arr.photo_max_url != null) {
                        $("#photo_img").attr("src", basic_arr.photo_max_url + '?' + new Date().getTime());
                    }

                    for (var insure_i in insure_arr) {
                        $("#is_no").val(insure_arr[insure_i].is_no);
                        $("#condition").val(insure_arr[insure_i].condition);
                        $("#begin_time").val(insure_arr[insure_i].begin_time);
                        $("#place").val(insure_arr[insure_i].place);
                    }
                    for (var work_i in work_undergo_arr) {
                        $("#check" + (parseInt(work_i) + 1)).attr("checked", "true");
                        $("#work_id" + work_i).val(work_undergo_arr[work_i].id);
                        $("#entry_time" + work_i).removeAttr("disabled");
                        $("#entry_time" + work_i).val(work_undergo_arr[work_i].entry_time);
                        $("#leave_time" + work_i).removeAttr("disabled");
                        $("#leave_time" + work_i).val(work_undergo_arr[work_i].leave_time);
                        $("#duties" + work_i).removeAttr("disabled");
                        $("#duties" + work_i).val(work_undergo_arr[work_i].duties);
                        $("#company_name" + work_i).removeAttr("disabled");
                        $("#company_name" + work_i).val(work_undergo_arr[work_i].company_name);
                    }
                }
            }
        });
    });
});
//点击员工基本信息时调用  end


//员工岗位调动  start
document.getElementById("staff_gangwei").onclick = function () {
    document.getElementById("append_out").style.display = "block";
    document.getElementById("staff_diaodong").style.display = "block";
    $("#staff_time").val("");
    $("#staff_state").val("");
    var user_card = $("#user_card").val();
    var data_arr = [];
    var export_post = $("#export_post");
    var export_campus = $("#export_campus");
    var fold_post = $("#fold_post");
    var fold_campus = $("#fold_campus");
    export_post.children().remove();
    export_campus.children().remove();
    fold_post.children().remove();
    fold_campus.children().remove();
    var export_campus_str = "<option value='请选择'>请选择</option>";
    var export_post_str = "<option value='请选择'>请选择</option>";
    var fold_campus_str = "<option value='请选择'>请选择</option>";
    var fold_post_str = "<option value='请选择'>请选择</option>";
    //获取员工信息
    $.ajax({
        url: "/Staff/staff_post_leave",
        data: {"user": user_card},
        type: "post",
        async: "false",
        success: function (data) {
            var txt = JSON.parse(data);
            data_arr = txt;
            $("#staff_time").val(data_arr.time);
            $("#staff_state").val(data_arr.state);
            var export_campus_opt = data_arr.campus;
            var export_post_opt = data_arr.post;
            for (var key in arr_class[0]) {
                if (export_campus_opt.indexOf(arr_class[0][key]) != -1) {
                    export_campus_str += '<option selected value="' + arr_class[0][key] + '">' + arr_class[0][key] + '</option>';
                } else {
                    export_campus_str += '<option value="' + arr_class[0][key] + '">' + arr_class[0][key] + '</option>';
                }
            }
            export_campus.prepend(export_campus_str);

            if ($('#export_campus').val() == '集团') {
                for (var key in arr_class[2]) {
                    if (export_post_opt.indexOf(arr_class[2][key]) != -1) {
                        export_post_str += '<option selected value="' + arr_class[2][key] + '">' + arr_class[2][key]+ '</option>';
                    } else {
                        export_post_str += '<option value="' + arr_class[2][key]+ '">' + arr_class[2][key]+ '</option>';
                    }
                }
                export_post.prepend(export_post_str);
            } else {
                for (var key in arr_class[3]) {
                    if (export_post_opt.indexOf(arr_class[3][key]) != -1) {
                        export_post_str += '<option selected value="' + arr_class[3][key] + '">' + arr_class[3][key]  + '</option>';
                    } else {
                        export_post_str += '<option value="' + arr_class[3][key] + '">' + arr_class[3][key] + '</option>';
                    }
                }
                export_post.prepend(export_post_str);
            }

            for (var key in arr_class[0]) {
                fold_campus_str += '<option value="' + arr_class[0][key] + '">' + arr_class[0][key] + '</option>';
            }
            fold_campus.prepend(fold_campus_str);
        }
    });
};
//员工岗位调动  end


function fold_campus_save() {
    var fold_post = $("#fold_post");
    if ($('#fold_campus').val() == '集团') {
        fold_post.html("");
        var fold_post_str = "<option value='请选择'>请选择</option>";
        for (var key in arr_class[2]) {
            fold_post_str += '<option value="' + arr_class[2][key] + '">' + arr_class[2][key] + '</option>';
        }
        fold_post.prepend(fold_post_str);
    } else if ($('#fold_campus').val() == '请选择') {
        fold_post.html("");
    } else {
        var fold_post_str = "<option value='请选择'>请选择</option>";
        fold_post.html("");
        for (var key in arr_class[3]) {
            fold_post_str += '<option value="' + arr_class[3][key]+ '">' +arr_class[3][key] + '</option>';
        }
        fold_post.prepend(fold_post_str);
    }
}


document.getElementById("staff_jc").onclick = function () {
    document.getElementById("append_out").style.display = "block";
    document.getElementById("staff_jiangcheng").style.display = "block";
    $("#shape").val('');
    $("#time").val('');
    $("#why_chengfa").val('');
    $("#result").val('');
};
close_diaodong.onclick = function () {
    document.getElementById("append_out").style.display = "none";
    document.getElementById("staff_diaodong").style.display = "none";
};
close_jiangcheng.onclick = function () {
    document.getElementById("staff_jiangcheng").style.display = "none";
    document.getElementById("append_out").style.display = "none";
};
close_ruzhi.onclick = function () {
    document.getElementById("staff_ruzhi").style.display = "none";
    document.getElementById("append_out").style.display = "none";
};


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


document.getElementById("staff_all").onmousewheel = function () {
    return false;
};
document.getElementById("staff_out").onmousewheel = function () {
    return false;
};


document.getElementById("staff_out").onclick = function () {
    document.getElementById("staff_all").style.display = "none";
    document.getElementById("staff_out").style.display = "none";
};
document.getElementById("close_staff_xiugai").onclick = function () {
    document.getElementById("staff_all").style.display = "none";
    document.getElementById("staff_out").style.display = "none";
};


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


//*************************     滚动条事件     *************************


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
    document.querySelector(".top_nav").style.left=2-parseInt(document.getElementById("tb_parent").scrollLeft)+"px";
};


//鼠标拖拽员工添加，修改页面
var mouseX, mouseY;
var objX, objY;
var isDowm = false;
function mouseDown(obj, e) {
    obj.style.cursor = "move";
    objX = my_div.style.left;
    objY = my_div.style.top;
    mouseX = e.clientX;
    mouseY = e.clientY;
    isDowm = true;
}
function mouseMove(e) {
    var move_div = document.getElementById("my_div");
    var x = e.clientX;
    var y = e.clientY;
    if (isDowm) {
        move_div.style.left = parseInt(objX) + parseInt(x) - parseInt(mouseX) + "px";
        move_div.style.top = parseInt(objY) + parseInt(y) - parseInt(mouseY) + "px";
    }
}
function mouseUp(e) {
    if (isDowm) {
        var x = e.clientX;
        var y = e.clientY;
        var move_div1 = document.getElementById("my_div");
        move_div1.style.left = (parseInt(x) - parseInt(mouseX) + parseInt(objX)) + "px";
        move_div1.style.top = (parseInt(y) - parseInt(mouseY) + parseInt(objY)) + "px";
        mouseX = x;
        rewmouseY = y;
        move_div1.style.cursor = "default";
        isDowm = false;
    }
}