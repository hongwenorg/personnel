/**
 * Created by Administrator on 2016/3/30.
 */



//******************************     全局变量  start    ***************************
var copy_excel = document.getElementById("copy_excel");
var copy_dayin = document.getElementById("copy_dayin");
var city = document.getElementById("city");
var tabb1 = document.getElementById("tabb1");
var tabb2 = document.getElementById("tabb2");
var tabb3 = document.getElementById("tabb3");
var tabb4 = document.getElementById("tabb4");
var tabb5 = document.getElementById("tabb5");
var tabb6 = document.getElementById("tabb6");
var tabb7 = document.getElementById("tabb7");
var rule_mintime_all = "";
var rule_maxtime_all = "";

//***************************     全局变量  end    ***************************

var tab_click = document.getElementById("tab_click");
var container_width = document.getElementById("container").style.width;
var div_length = tab_click.children.length;
//document.getElementById("tab_click").style.width=container_width;
var tab_chile_width = document.querySelectorAll('.tab_width');
for (var t = 0; t < tab_chile_width.length; t++) {
    tab_chile_width[t].style.width = 100 / div_length + "%";
    if (t == tab_chile_width.length - 1) {
        tab_chile_width[t].style.borderTopRightRadius = "10px";
        tab_chile_width[t].style.overflow = "hidden";
    } else if (t == 0) {
        tab_chile_width[t].style.borderTopLeftRadius = "10px";
        tab_chile_width[t].style.overflow = "hidden";
        tab_chile_width[t].style.background = "white";
        tab_chile_width[t].style.color = "black";
    }
}
//页面加载事件

var text_arr = [];
window.onload = function () {
    var str = "";
    $.ajax({
        url: "/Checking/rule_load",
        type: "post",
        async: 'false',
        success: function (msg) {
            var json_text = JSON.parse(msg);
            for (key in json_text) {
                if (key != "status" && json_text["status"] != 2) {
                    text_arr.push(json_text[key]);
                }
            }
            for (var i = 0; i < text_arr.length; i++) {
                str += "<div class='check_out'><div class='closed' onclick='toggle(this)' id='group_send_open'><input type='hidden' value='" + text_arr[i].id + "' id='id"+i+"'><div id='group_send' onclick= content_click(" + i + ",'" + text_arr[i].class + "') >" + text_arr[i].class + "</div></div><table name='"+text_arr[i].class+"' class='hide' status='hide' cellpadding='2' cellspacing='0' id='tb" + i + "'></table></div>";
            }
            $("#campus_content").html(str);
        }
    });

    var check_append = [];
    $.ajax({
        url: "/Index/modules",
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
                document.getElementById("tab_click").appendChild(append_div).innerHTML = "<div class='click_modules'>" + check_append_modules[i].modules_name + "</div>";
            }
            var click_div = document.getElementById("tab_click");
            var tab_child = document.querySelectorAll('.click_modules');
            click_div.style.width = "95%";
            for (var aa = 0; aa < tab_child.length; aa++) {
                tab_child[aa].parentNode.style.width = 100 / tab_child.length + "%";
                if (tab_child[aa].innerText == "考勤规则") {
                    tab_child[aa].style.background = "white";
                    tab_child[aa].style.color = "black";
                }
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
                        location.href = check_append_modules[j].modules_file;
                    }
                }
            });
        }
    });


};
//页面加载事件

//点击显示/切换/与隐藏

HTMLElement.prototype.check_rules = function (selector) {
    return this.querySelectorAll(selector);
};
window.check_rules = function (selector) {
    return document.querySelectorAll(selector);
};
function toggle(open_click) {
    var tabes = open_click.parentNode.check_rules("table")[0];
    if (open_click.className == "open") {
        open_click.className = "closed";
        tabes.className = "hide";
    } else {
        var openddiv = $("div.check_tree .open")[0];
        if (openddiv) {
            openddiv.className = "closed";
            $("div.check_tree .show")[0].className = "hide";
        }
        open_click.className = "open";
        tabes.className = "show";
    }
}

//获取员工数据
var group_all = [];
var arr_num = [];
var staff_id = [];
var staff_name = [];

function content_click(num, content) {
    if ($("#tb" + num).attr("class") == "hide") {
        var html_str = "";
        $.ajax({
            url: "/Checking/rule_select",
            data: {"content": content},
            type: "post",
            async: 'false',
            success: function (msg) {
                var json_text = JSON.parse(msg);
                for (key in json_text) {
                    if (key != "status" && json_text["status"] != 2) {
                        group_all.push(json_text[key]);
                        for (var l = 0; l < group_all.length; l++) {
                            arr_num[l] = l + 1;
                        }
                    }
                }
                html_str="<input type='hidden' value='"+$('#id'+num).val()+"' id='id_val'><tr><td><input type='button' onclick='formulate_time(this)' class='formulate_time'></td></tr>";
                for (var j = 0; j < group_all.length; j++) {
                    var tr = document.createElement("tr");
                    staff_name.push(group_all[j].name);
                    staff_id.push(group_all[j].id);
                    html_str += "<tr class='tr_content'><td class='td_sty'>" + arr_num[j] + "</td>" + "<td class='td_sty'>" + group_all[j].name + "</td>" + "<td class='td_sty'>" + group_all[j].post + "</td>" +
                        "<td class='td_sty td_width'>" + "<input type='time' readonly='readonly' class='input_time_' value='" + group_all[j].rule_min0 + "'>" + "~" + "<input type='time' class='input_time_' readonly='readonly' value='" + group_all[j].rule_max0 + "'>" + "</td>" +
                        "<td class='td_sty td_width'>" + "<input type='time' readonly='readonly' class='input_time_' value='" + group_all[j].rule_min1 + "'>" + "~" + "<input type='time' class='input_time_' readonly='readonly' value='" + group_all[j].rule_max1 + "'>" + "</td>" +
                        "<td class='td_sty td_width'>" + "<input type='time' readonly='readonly' class='input_time_' value='" + group_all[j].rule_min2 + "'>" + "~" + "<input type='time' class='input_time_' readonly='readonly' value='" + group_all[j].rule_max2 + "'>" + "</td>" +
                        "<td class='td_sty td_width'>" + "<input type='time'  readonly='readonly' class='input_time_' value='" + group_all[j].rule_min3 + "'>" + "~" + "<input type='time' class='input_time_' readonly='readonly' value='" + group_all[j].rule_max3 + "'>" + "</td>" +
                        "<td class='td_sty td_width'>" + group_all[j].week + "</td>" +
                        "<td class='td_sty rules_button'>" + "<div class='group_none' onclick='rule_up_click(this)'>" + group_all[j].name + "1" + "</div>" + "</td></tr>";
                }
                $("#tb" + num).html(html_str);
                var formulate_time=document.querySelectorAll(".formulate_time");
                for(var i=0;i<formulate_time.length;i++){
                    if(formulate_time[i].getAttribute("id")!="get_id"){
                        var formulate_time_parent=formulate_time[i].parentNode.parentNode.parentNode.parentNode;
                        formulate_time[i].value="制定 "+formulate_time_parent.getAttribute("name")+" 员工休息时间" ;
                    }
                }

            }
        });
        group_all = [];
        rule_save();

    }else{

        $("#id_val").remove();
    }

}






function formulate_time(obj){
        //$("#t2 tr:not(:first)").css("display","none");
        document.getElementById("t2").innerHTML="";
        var deposit_value=document.getElementById("id_val").value;
        document.getElementById("staff_rules_out").style.display="block";
        //document.querySelector(".rest_font").innerText=obj.value;
        document.getElementById("restTime_rules2").style.display="block";
        $.ajax({
            url: "/Checking/rule_week_select",
            data:{'campus_id':deposit_value},
            type: "post",
            async: "false",
            cache: "false",
            success:function(data){
                ajax_rest(data,"t2");
            }
        })

}


function formulate_time_02(obj,num){
        document.getElementById("t1").innerHTML="";
        document.getElementById("restTime_rules").style.display="block";
        document.getElementById("staff_rules_out").style.display = "block";
        $.ajax({
            url: "/Checking/rule_week_select",
            data:{},
            type: "post",
            async: "false",
            cache: "false",
            success:function(data){
                ajax_rest(data,"t1",num);
            }
        })

}


function ajax_rest(data,obj,num){
     var rest_day=[];
     var html_str = "";
     var json_str = '{"0":"星期日","1":"星期一","2":"星期二","3":"星期三","4":"星期四","5":"星期五","6":"星期六"}';
     var json_arr = JSON.parse(json_str);
     var num = '';
     var rest_date = JSON.parse(data);
     document.getElementById("t1").innerHTML="";
     document.getElementById("t2").innerHTML="";
     for(key in rest_date){
         rest_day.push(rest_date[key]);
     }
    for(var i=0;i<rest_day.length;i++){
        var creat_trr=document.createElement("tr");
            creat_trr.id=rest_day[i].rule_name_id;
        var arr_week=rest_day[i].week_num.split(" ");

        html_str ="<td>"+"<input type='text' readonly='true' class='read_input' value='"+rest_day[i].rules_name+"'>"+"</td>"+
            "<td>"+"<input type='text' readonly='true' class='read_input' value='"+rest_day[i].post_name+"'>"+"</td>"+
            "<td>";
        for(var key in json_arr){
            if(key == '0'){
                num = 7;
            }else{
                num = key;
            }
            html_str += "<input type='checkbox' disabled='disabled' id='box"+i+num+"' value='"+num+"'>"+json_arr[key]+" ";
        }
        html_str += "</td>"+"<td>"+"<input type='button' value='修改' class='xg_tj' onclick='xg_tj(this,"+num+")'>"+
            "<input type='button' value='删除' class='rm_tr' onclick='rm_tr(this)'>"+"</td>";
        creat_trr.innerHTML = html_str;
        document.getElementById(obj).appendChild(creat_trr);
        for(var key in json_arr){
            for(var k in arr_week){
                if(key == '0'){
                    num = 7;
                }else{
                    num = key;
                }
                if(json_arr[key] == arr_week[k]){
                    $("#box"+i+num).attr("checked","checked");
                }
            }
        }

    }
}



//制定员工休息日  删除按钮
var rm_tr=function(obj){
    var obj_pare=obj.parentNode.parentNode;
    $.ajax({
        url: "/Checking/rule_week_delete",
        data:{id:obj_pare.getAttribute("id")},
        type: "post",
        async: "false",
        cache: "false",
        success:function(data){
        }
    });
    obj_pare.parentNode.removeChild(obj_pare);
};
//制定员工休息日 修改 提交按钮
var xg_tj=function(obj,num){
    var obj_pare=obj.parentNode.parentNode;
    var xginput_01=obj.parentNode.parentNode.childNodes[0].childNodes[0];
    var xginput_02=obj.parentNode.parentNode.childNodes[1].childNodes[0];
    var xgcheck_01=obj.parentNode.parentNode.childNodes[2].childNodes[0];
    var xgcheck_02=obj.parentNode.parentNode.childNodes[2].childNodes[2];
    var xgcheck_03=obj.parentNode.parentNode.childNodes[2].childNodes[4];
    var xgcheck_04=obj.parentNode.parentNode.childNodes[2].childNodes[6];
    var xgcheck_05=obj.parentNode.parentNode.childNodes[2].childNodes[8];
    var xgcheck_06=obj.parentNode.parentNode.childNodes[2].childNodes[10];
    var xgcheck_07=obj.parentNode.parentNode.childNodes[2].childNodes[12];
    var xgcheck_all=obj.parentNode.parentNode.childNodes[2].childNodes;

    if(obj.value=="修改"){
        obj.value="提交";
        obj.style.backgroundColor="orange";
        xginput_01.readOnly = false;
        xginput_02.readOnly = false;
        for(var i=0;i<xgcheck_all.length;i++){
            xgcheck_all[i].disabled=false;
        }
        var arr_post=[];
        var reques = new XMLHttpRequest();
        reques.open("get", "/Content/post");
        reques.send();
        reques.onreadystatechange = function () {
            if (reques.readyState == 4 && reques.status == 200) {
                var json_text = JSON.parse(reques.responseText);

                for (var key in json_text) {
                    arr_post.push(json_text[key]);
                }
                var option_text = "";
                var select_ = document.createElement("select");
                select_.setAttribute("class","select_kuang");
                for (var key in arr_post[2]) {
                    option_text += "<option value='" + key + "'>" + arr_post[2][key] + "</option>";
                }
                for (var key in arr_post[3]) {
                    option_text += "<option value='" + key + "'>" + arr_post[3][key] + "</option>";
                }
                select_.innerHTML=option_text;
                var xg_select = obj.parentNode.parentNode.childNodes[1];
                xg_select.removeChild(obj.parentNode.parentNode.childNodes[1].childNodes[0]);
                xg_select.appendChild(select_);
            }
        }

    }else if(obj.value == "提交"){

        if(xginput_01.value==""){
            alert("请填写名称");
            return;
        }
        if(xginput_02=="选择职务"){
            alert("请选择职务");
            return;
        }
        var xgcheck_val="";
        for(var j=0;j<xgcheck_all.length;j++) {
            if (xgcheck_01.checked == false &&
                xgcheck_02.checked == false &&
                xgcheck_03.checked == false &&
                xgcheck_04.checked == false &&
                xgcheck_05.checked == false &&
                xgcheck_06.checked == false &&
                xgcheck_07.checked == false) {
                alert("至少制定一个休息日");
                return;
            }
            if(xgcheck_all[j].checked==true){
                xgcheck_val+=xgcheck_all[j].value+",";

            }

        }
        var msg = '';
        var campus_id = '';
        if(num != 1){
            if($('#id_val').val()){
                campus_id = $('#id_val').val();
            }
        }

        $.ajax({
            url: "/Checking/rule_week_save",
            data: {
                id: obj_pare.getAttribute("id"),
                'name': xginput_01.value,
                'post': xginput_02.value,
                'week': xgcheck_val,
                'campus_id':campus_id
            },
            type: "post",
            async: false,
            cache: "false",
            success: function (data) {
                if(data == 1 || data == 3){
                    alert("提交成功");
                    msg = data;
                }else{
                    alert("提交失败");return;
                }
            }
        });
        if(msg == 1){
            formulate_time_02();
            rest_tr();
        }
        if(msg == 3){
            formulate_time();
            rest_tr();
        }
    }
};



function rule_save() {
    //获取时间数据   放到select列表下
    var select_time = [];
    var html_time = "";
    $(".select_time_child").nextAll().remove();
    $.ajax({
        url: "/Checking/rule_all_select",
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            var json_text = JSON.parse(msg);
            for (key in json_text) {
                select_time.push(json_text[key]);
                select_time.sort(function (a, b) {
                    return a["rule_mintime"].localeCompare(b["rule_mintime"]);
                });
            }
            for (var i = 0; i < select_time.length; i++) {
                html_time += "<option value='" + select_time[i].id + "'>" + select_time[i].rule_mintime + "~" + select_time[i].rule_maxtime + "</option>";
            }
            $(".select_time_child").after(html_time);
        }
    })
}


//点击制定时间规则时操作
function rule_ajax() {
    var rule_arr = [];
    var html_str = "";
    if ($(".tr_content").html()) {
        $(".tr_content").remove();
    }
    $.ajax({
        url: "/Checking/rule_all_select",
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            var json_text = JSON.parse(msg);
            for (key in json_text) {
                rule_arr.push(json_text[key]);
                rule_arr.sort(function (a, b) {
                    return a["rule_mintime"].localeCompare(b["rule_mintime"]);
                });
            }
            for (var i = 0; i < rule_arr.length; i++) {
                html_str += "<tr class='tr_content' id='rule_tr" + rule_arr[i].id + "'><input type='hidden' name='' value='" + rule_arr[i].id + "' /><td id='rule_min_td" + rule_arr[i].id + "'>" + rule_arr[i].rule_mintime + "</td><td id='rule_max_td" + rule_arr[i].id + "'>" + rule_arr[i].rule_maxtime + "</td><td><input type='button' class='xiugai_time' value='修改' onclick=rule_update('" + rule_arr[i].id + "') /><input type='button' class='remove_time' value='删除'  onclick=rule_delete('" + rule_arr[i].id + "') /></td></tr>";
            }
            $(".last_tr").before(html_str);

        }
    });
    $(".append_staff_rules").css("display", "block");
    document.getElementById("staff_rules_out").style.display = "block";
    rule_save();
}


//时间规则制定修改操作
function rule_update(id) {
    rule_mintime_all = $('#rule_min_td' + id).text();
    rule_maxtime_all = $('#rule_max_td' + id).text();
    var html_str = "<td><input type='time' value='" + rule_mintime_all + "' id='rule_min_td" + id + "'></td><td><input type='time' value='" + rule_maxtime_all + "'  id='rule_max_td" + id + "'></td><td><input type='button' class='xiugai_time' value='提交' onclick=rule_update_pro('" + id + "') /><input class='remove_time' type='button' onclick=rule_no_pro('" + id + "') value='取消'  /></td>";
    $("#rule_tr" + id).html(html_str);
}


//时间规则制定取消操作
function rule_no_pro(id) {
    var html_str = "<input type='hidden' name='' value='" + id + "' /><td id='rule_min_td" + id + "'>" + rule_mintime_all + "</td><td id='rule_max_td" + id + "'>" + rule_maxtime_all + "</td><td><input class='xiugai_time' type='button' value='修改' onclick=rule_update('" + id + "') /><input class='remove_time' type='button'  value='删除'  onclick=rule_delete('" + id + "') /></td>";
    $("#rule_tr" + id).html(html_str);
}


//时间规则制定修改程序
function rule_update_pro(id) {
    var rule_mintime = $('#rule_min_td' + id).val();
    var rule_maxtime = $('#rule_max_td' + id).val();
    $.ajax({
        url: "/Checking/rule_update",
        data: {'id': id, "rule_mintime": rule_mintime, "rule_maxtime": rule_maxtime},
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            if (msg == 1) {
                alert('操作成功！');
                rule_ajax();
                $(".last_tr").css("display", "block");
            }
        }
    })
}


//时间规则制定删除程序
function rule_delete(id) {
    $.ajax({
        url: "/Checking/rule_delete",
        data: {'id': id},
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            if (msg == 1) {
                alert('操作成功！');
                rule_ajax();
                $(".last_tr").css("display", "block");
            }
        }
    })
}

//点击添加时操作
$(".time_rules_tianjia").click(function () {
    var tr_html = "<tr class='tr_content'><td><input type='time' name='rule_mintime' class='rule_mintime'></td><td><input type='time' name='rule_maxtime' class='rule_maxtime'></td><td><input type='button' value='提交' class='rule_submit xiugai_time' onclick=rule_submit_pro(this)  /><input type='button' onclick=rule_reset_pro(this) value='取消' class='rule_reset remove_time' /></td></tr>";
    $(".last_tr").before(tr_html);
    $(".last_tr").css("display", "none");
});
function rule_week_add(num) {
    var arr_post=[];
    var reques = new XMLHttpRequest();
    reques.open("get", "/Content/post");
    reques.send();
    reques.onreadystatechange = function () {
        if (reques.readyState == 4 && reques.status == 200) {
            var json_text = JSON.parse(reques.responseText);

            for (var key in json_text) {
                arr_post.push(json_text[key]);
            }
            var option_text="";
            var select_=document.createElement("select");
            for (var key in arr_post[2]) {
                option_text+="<option value='"+key+"'>"+arr_post[2][key]+"</option>";
            }
            for (var key in arr_post[3]) {
                option_text+="<option value='"+key+"'>"+arr_post[3][key]+"</option>";
            }
            var tr_html = "<tr class='tr_content_min'>"+
                "<td>"+"<input type='text' placeholder='输入名称' class='append_mc' id='append_mc'>"+"</td>" +
                "<td>"+"<select class='append_sel' id='append_sel'>"+"<option>"+"选择职务"+"</option>"+option_text+"</select>"+"</td>" +
                "<td>"+ "<input type='checkbox' name='' class='checked_input' value='7'>" +"星期日"+" "+
                "<input type='checkbox' name='' class='checked_input' value='1'>" +"星期一"+" "+
                "<input type='checkbox' name='' class='checked_input' value='2'>" +"星期二"+" "+
                "<input type='checkbox' name='' class='checked_input' value='3'>" +"星期三"+" "+
                "<input type='checkbox' name='' class='checked_input' value='4'>" +"星期四"+" "+
                "<input type='checkbox' name='' class='checked_input' value='5'>" +"星期五"+" "+
                "<input type='checkbox' name='' class='checked_input' value='6'>" +"星期六"+" "+
                "</td>" +"<input type='button' class=''>"+
                "<td>"+"<input type='button' value='提交' style='margin-left: 25px' class=' xiugai_time tj_restTime' onclick=tj_restTime_(this,"+num+")  />" +
                "<input type='button' onclick=rest_tr() value='取消' class='rule_reset remove_time' />"+"</td>"+"</tr>";
            $(".rest_tr").before(tr_html);
            $(".rest_tianjia").css("display", "none");
        }
        var checked_input=document.querySelectorAll(".checked_input");
        for(var i=0;i<checked_input.length;i++){
            checked_input[i].checked=false;
            checked_input[i].onclick=function(){
                if(this.checked==true){
                    this.checked==false;
                }
                if(this.checked==false){                       
                    this.checked==true;
                }
            }
        }
    };
}



//点击取消时操作
function rule_reset_pro(bu_this) {
    rule_ajax();
    $(".last_tr").css("display", "block");
}
function rest_tr() {
    $(".rest_tianjia").css("display", "block");
    var tr_content_min=document.querySelectorAll(".tr_content_min");
        for(var i=0; i<tr_content_min.length;i++){
            tr_content_min[i].parentNode.removeChild( tr_content_min[i]);
        }
}


//点击提交时操作
function rule_submit_pro(bu_this) {
    var rule_mintime = $(".rule_mintime").val();
    var rule_maxtime = $(".rule_maxtime").val();
    if (rule_mintime == "" || rule_maxtime == "") {
        alert("请输入正确的时间规则！");
        return false;
    }
    $.ajax({
        url: "/Checking/rule_submit",
        data: {"rule_mintime": rule_mintime, "rule_maxtime": rule_maxtime},
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            if (msg == 1) {
                rule_ajax();
                $(".last_tr").css("display", "block");
            }
        }
    })
}

//制定休息日，提交ajax
function tj_restTime_(obj,num){

    var append_mc=obj.parentNode.parentNode.childNodes[0].childNodes[0].value;
        if(append_mc==""){
            alert("请填写名称");
            return;
        }
    var append_sel=obj.parentNode.parentNode.childNodes[1].childNodes[0].value;
        if(append_sel=="选择职务"){
            alert("请选择职务");
            return;
        }
    var checked_input=document.querySelectorAll(".checked_input");
    var checked_value="";
    for(var i=0;i<checked_input.length;i++){
        if(checked_input[i].checked==true){
           checked_value+=checked_input[i].value+",";
        }
        if(checked_input[0].checked==false&&
            checked_input[1].checked==false&&
            checked_input[2].checked==false&&
            checked_input[3].checked==false&&
            checked_input[4].checked==false&&
            checked_input[5].checked==false&&
            checked_input[6].checked==false&&
            checked_input[7].checked==false&&
            checked_input[8].checked==false&&
            checked_input[9].checked==false&&
            checked_input[10].checked==false&&
            checked_input[11].checked==false&&
            checked_input[12].checked==false&&
            checked_input[13].checked==false){
            alert("至少制定一个休息日");
            return;
        }
    }
    var campus_id = '';
    if(num != 1){
        if($('#id_val').val()){
            campus_id = $('#id_val').val();
        }
    }
    var msg = "";
    $.ajax({
        url: "/Checking/rule_week",
        data: {'name':append_mc , 'post':append_sel , 'week':checked_value , 'campus_id':campus_id},
        type: "post",
        async: false,
        cache: "false",
        success: function (data) {
            if(data == 1 || data == 3){
                alert("提交成功");
                msg = data;
            }else{
                alert("提交失败");return;
            }
        }
    });
    if(msg == 1){
        formulate_time_02();
        rest_tr();
    }
    if(msg == 3){
        formulate_time();
        rest_tr();
    }
}







//点击弹出时间制定规则

function rule_up_click(e) {
    document.getElementById("rest_show_date").innerHTML="";
    for (var i = 0; i < staff_name.length; i++) {
        if (e.innerHTML == staff_name[i] + "1") {
            document.getElementById("staff_rules").style.display = "block";
            document.getElementById("staff_rules_out").style.display = "block";
            document.getElementById("staff_rules_name").innerHTML = "员工 " + staff_name[i] + " 定制考勤规则";
            var basic_id = staff_id[i];
            $("#id_card").val(basic_id);
        }
    }
    var e_prev = e.parentNode.previousSibling.innerHTML;
    var arr_prev = e_prev.split(" ");
    var creat_div = document.createElement("div");
    var html_str = "";
    var json_str = '{"0":"星期日","1":"星期一","2":"星期二","3":"星期三","4":"星期四","5":"星期五","6":"星期六"}';
    var json_arr = JSON.parse(json_str);
    var num = '';
    var arr_week = arr_prev;
    for (var key in json_arr) {
        if (key == '0') {
            num = 7;
        } else {
            num = key;
        }
        html_str += "<input type='checkbox' onclick='click_checkbox(this)' class='week_num' id='box" + i + num + "' value='" + num + "'>" + json_arr[key] + " ";
    }
    creat_div.innerHTML = html_str;
    creat_div.setAttribute("class","myy");
    document.getElementById("rest_show_date").appendChild(creat_div);
    for (var key in json_arr) {
        for (var k in arr_week) {
            if (key == '0') {
                num = 7;
            } else {
                num = key;
            }
            if (json_arr[key] == arr_week[k]) {
                $("#box" + i + num).attr("checked", "checked");
            }
        }
    }



    var content_arr = [];
    var rule = [];
    var rule_disable = [];
    $(".rules_disable").text("禁用");
    $(".rules_disable").css("background-color", "#ff6400");
    $(".staff_rules_time").removeAttr("readonly");
    $(".staff_rules_time").val("");
    $(".select_time_rules").val("0");
    $.ajax({
        url: "/Checking/check_rule_find",
        data: {'basic_id': basic_id},
        type: "post",
        async: "false",
        cache: "false",
        success: function (msg) {
            var json_text = JSON.parse(msg);
            for (key in json_text) {
                if (key != "status" && json_text["status"] != 2) {
                    content_arr.push(json_text[key]);
                }
            }
            var s = 0;
            if (json_text['rule']) {
                var rule = json_text['rule'];
                for (var j = 0; j < rule.length; j++) {
                    $("#time_rule" + s).val(rule[j].id);
                    s++;
                }
            }
            if (json_text['rule_disable']) {
                var rule_disable = json_text['rule_disable'];
                for (var j = 0; j < rule_disable.length; j++) {
                    $("#time_rule" + s).val(rule_disable[j].id);
                    document.getElementById("rules_disable" + s).innerHTML = "启用";
                    document.getElementById("rules_disable" + s).style.background = "gray";
                    $("#time_rule" + s).attr("readonly", "readonly");
                    s++;
                }
            }
            var week_check=document.querySelectorAll(".week_num");
            $("#staff_rules_btt1").click(function () {
                //所有信息无误走提交
                var user_card = $("#id_card").val();
                var data = [];
                var arr = [];
                for (var i = 0; i < 6; i++) {
                    if ($("#time_rule" + i).val() != "0" && i == 0) {
                        arr = [$("#time_rule" + i).val(), '0'];
                        data.push(arr);
                    } else if ($("#time_rule" + i).val() != "0" && i != 0 && $("#rules_disable" + i).text() == "启用") {
                        arr = [$("#time_rule" + i).val(), '2'];
                        data.push(arr);
                    } else if ($("#time_rule" + i).val() != "0" && i != 0 && $("#rules_disable" + i).text() == "禁用") {
                        arr = [$("#time_rule" + i).val(), '1'];
                        data.push(arr);
                    }
                }
                var week = "";
                for (var j = 0; j < week_check.length; j++) {
                    if (week_check[j].checked == true) {
                        week += week_check[j].value + ",";
                    }
                }
                var data_json = JSON.stringify(data);
                $.ajax({
                    url: "/Checking/check_rule_pro",
                    data: {'data': data_json, 'user_id': user_card, 'week':week},
                    type: "post",
                    async: "false",
                    traditional: "true",
                    cache: "false",
                    success: function (msg) {
                        if (msg == 1) {
                            document.getElementById("staff_rules").style.display = "none";
                            document.getElementById("staff_rules_out").style.display = "none";
                        } else {
                            alert("保存失败！");
                        }
                    }
                });
            });
        }

    });




}
//关闭定制时间规则框
function staff_rules_clo() {
    $(".append_staff_rules").css("display", "none");
    $(".last_tr").css("display", "block");
    document.getElementById("staff_rules_out").style.display = "none";
}
function close_rest(a){
    $(".rest_tianjia").css("display", "block");
    var tr_content_min=document.querySelectorAll(".tr_content_min");
    for(var i=0; i<tr_content_min.length;i++){
        tr_content_min[i].parentNode.removeChild( tr_content_min[i]);
    }
    if(a==1){
        document.getElementById("restTime_rules").style.display="none";
        document.getElementById("staff_rules_out").style.display = "none";
    }else if(a==2){
        document.getElementById("restTime_rules2").style.display="none";
        document.getElementById("staff_rules_out").style.display = "none";
    }

}


function staff_rules_close() {
    $(".append_staff_rules").css("display", "none");
    document.getElementById("staff_rules").style.display = "none";
    document.getElementById("staff_rules_out").style.display = "none";
}
var remove_rules_time=function(num) {
    document.getElementById("time_rule" + num).value = "";
    document.getElementById("time_rule" + num).value = "";
};
//点击切换  禁用  启用按扭
function disabled_rules_time(num) {
    if ($("#rules_disable" + num).text() == "禁用") {
        document.getElementById("rules_disable" + num).innerHTML = "启用";
        document.getElementById("rules_disable" + num).style.background = "gray";
        $("#min_time" + num).attr("readonly", "readonly");
        $("#max_time" + num).attr("readonly", "readonly");
    } else {
        document.getElementById("rules_disable" + num).innerHTML = "禁用";
        document.getElementById("rules_disable" + num).style.background = "#ff6400";
        $("#min_time" + num).removeAttr("readonly");
        $("#max_time" + num).removeAttr("readonly");
    }
}
//点击确定按扭 提交规定的时间规则 数据



var click_checkbox=function(obj){
        if(obj.checked==false){
            obj.checked==true;
        }
        if(obj.checked==true){
            obj.checked==false;
        }
    };


//*************************     布局分页     *************************
function blk(tabb) {
    if (tabb == 1) {
        location.href = "/Checking/check_index";
    } else if (tabb == 2) {
        location.href = "/Checking/check_check";
    } else if (tabb == 3) {
        location.href = "/Checking/check_checkrules";
    }
}


//鼠标拖拽时间添加规则，修改页面
var mouseX, mouseY;
var objX, objY;
var isDowm = false;
function mouseDown(obj, e) {
    obj.style.cursor = "move";
    objX = document.getElementById("move_staff_rules").style.left;
    objY = document.getElementById("move_staff_rules").style.top;
    mouseX = e.clientX;
    mouseY = e.clientY;
    isDowm = true;
}
function mouseMove(e) {
    var move_staff_rules = document.getElementById("move_staff_rules");
    var x = e.clientX;
    var y = e.clientY;
    if (isDowm) {
        move_staff_rules.style.left = parseInt(objX) + parseInt(x) - parseInt(mouseX) + "px";
        move_staff_rules.style.top = parseInt(objY) + parseInt(y) - parseInt(mouseY) + "px";
    }
}
function mouseUp(e) {
    if (isDowm) {
        var x = e.clientX;
        var y = e.clientY;
        var move_staff_rules1 = document.getElementById("move_staff_rules");
        move_staff_rules1.style.left = (parseInt(x) - parseInt(mouseX) + parseInt(objX)) + "px";
        move_staff_rules1.style.top = (parseInt(y) - parseInt(mouseY) + parseInt(objY)) + "px";
        mouseX = x;
        rewmouseY = y;
        move_staff_rules1.style.cursor = "default";
        isDowm = false;
    }
}