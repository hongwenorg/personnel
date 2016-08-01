var check_append = [];
window.onload = function () {
    $.ajax({
        url: "/Index/modules",
        data: {"the_level": 2},
        type: "post",
        async: "false",
        success: function (data) {
            var check_append_modules = [];
            msg = JSON.parse(data);
            var arr_num = [];
            for (key in msg) {
                if (key != "status" && msg["status"] != 2) {
                    check_append_modules.push(msg[key]);
                }
            }
            for (var i = 0; i < check_append_modules.length; i++) {
                var append_div = document.createElement("div");
                document.getElementById("tab_click").appendChild(append_div).innerHTML = "<div class='click_modules'>" + check_append_modules[i].modules_name + "</div>";
            }
            var click_div = document.getElementById("tab_click");
            click_div.style.width = "30%";
            var tab_child = document.querySelectorAll('.click_modules');
            for (var t = 0; t < tab_child.length; t++) {
                if (t == 0) {
                    for (var j = 0; j < check_append_modules.length; j++) {
                        if (tab_child[t].innerText == check_append_modules[j].modules_name) {
                            location.href = check_append_modules[j].modules_file;
                        }
                    }
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