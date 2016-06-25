
//弹出框
var append_alert;
var mask_out;
//弹出框里的文本以及图片内容
var append_alert_text;
function msgBox(obj, flag, title, width, fn) {
    document.getElementById("preson_out").style.display="block";
    append_alert  =  document.createElement("div");
    append_alert_text  =  document.createElement("div");
    append_alert_text.setAttribute("id", "hahaha");
//弹出框的头部
    var append_alert_head  =  document.createElement("div");
//弹出框的确认按扭
    var append_alert_yes  =  document.createElement("input");
//获取弹出框的高度
    var borwerHeight = document.documentElement.clientHeight;
//将弹出框 添加到 body下
    document.querySelector("body").appendChild(append_alert);



//弹出框样式
            append_alert.style.backgroundColor  =  "white";
            append_alert.style.width            =  width+"px";
            append_alert.style.marginLeft       =  -(width/2)+"px";
            append_alert.style.maxHeight        =  "900px";
            append_alert.style.position         =  'fixed';
            append_alert.style.left             =  "50%";
            append_alert.style.top              =  "50%";
            append_alert.style.zIndex           =  "9999";
            append_alert.style.paddingBottom    =  "20px";
            append_alert.style.border           =  "1px solid lightgray";
            append_alert.style.boxShadow        =   "0 0 50px gray";
            append_alert.style.opacity          =   0;
    //头部样式
            append_alert_head.style.backgroundColor  =  "#555a5a";
            append_alert_head.style.height           =  "50px";
            append_alert_head.style.color            =  "#00c8c8";
            append_alert_head.style.textAlign        =  "center";
            append_alert_head.style.fontSize         =  "25px";
            append_alert_head.style.fontWeight       =  "bold";
            append_alert_head.style.fontFamily       =  "微软雅黑";
            append_alert_head.style.lineHeight       =  "50px";
            append_alert_head.style.textShadow       =  "0 6px 15px black";
            append_alert.appendChild(append_alert_head).innerHTML = title + "<input type='button' id='close_append_alert' onclick='close_append_alert()' value='×'>";
    //确定按扭样式
            append_alert_yes.setAttribute("type", "button");
            append_alert_yes.setAttribute("onclick", "close_append_alert()");
            append_alert_yes.value                 =  "确定";
            append_alert_yes.style.marginLeft      =  "5%";
            append_alert_yes.style.width           =  "90%";
            append_alert_yes.style.height          =  "35px";
            append_alert_yes.style.color           =  "white";
            append_alert_yes.style.fontSize        =  "20px";
            append_alert_yes.style.fontWeight      =  "bold";
            append_alert_yes.style.backgroundColor =  "#00c8c8";
            append_alert_yes.style.border          =  "none";
            append_alert_yes.style.marginTop       =  "10px";
            append_alert_yes.style.cursor          =  "pointer";
    //关闭按钮样式
    var close_append_alert = document.getElementById("close_append_alert");
            close_append_alert.style.border           =  "none";
            close_append_alert.style.color            =  "#00c8c8";
            close_append_alert.style.cursor           =  "pointer";
            close_append_alert.style.backgroundColor  =  "#555a5a";
            //close_append_alert.style.float          =  "right";
            close_append_alert.style.position         =  "absolute";
            close_append_alert.style.right            =  "10px";
            close_append_alert.style.fontSize         =  "35px";

    //判断参数 obj flag 是否存在
    if (!obj || !flag) {
        console.error("flag 不存在");
        return false
    }

            //设置  弹出框标题默认为公告 ，宽度默认为700
            title = title || "公告";
            width = width || 700;

    //判断 参数flag
    switch (flag) {
        case "text":
            //如果为文本 就创建一个p标签   里面放文字
            var iOS_;
            var isAndroid_;
            var userType_ = {};
            var str = obj.split("  ");
            for (var i = 0; i < str.length; i++) {
                var  p = document.createElement("p");
                p.style.fontWeight  =  "bold";
                p.style.fontFamily  =  "微软雅黑";
                p.style.wordBreak   =  "break-all";
                //p.style.textIndent="32px";
                p.innerText ="　　"+str[i];
                append_alert.appendChild(append_alert_text).appendChild(p);
            }
    //设置移动端显示 页面样式
            if (navigator.userAgent.toLowerCase().match(/(android)/)||navigator.userAgent.toLowerCase().match(/(iphone|ipod|ipad)/)) {
                isAndroid_ = true;
                iOS_ = true;
                userType_.isAndroid = true;
                append_alert_text.style.fontSize    =   "40px";
                append_alert.style.width            =   "80%";
                append_alert_text.style.width       =  "80%";
                append_alert.style.marginLeft       =   "-40%";
            }else {
                iOS_ = false;
                isAndroid_ = false;
                append_alert_text.style.fontSize     =  "18px";
            }
            //弹出框里的 文本内容 样式
            append_alert_text.style.backgroundColor  =  "white";
            append_alert_text.style.width            =  "80%";
            append_alert_text.style.marginLeft       =  "10%";
            append_alert_text.style.minHeight        =  "20px";
            append_alert_text.style.overflowY        =  "auto";
            append_alert_text.style.maxHeight        =  "600px";
            append_alert_text.style.marginTop        =  "10px";
            append_alert.style.marginTop = -(append_alert.clientHeight / 2) + "px";
            //判断弹出框的高度 是否大于浏览器窗口高度，以及弹出框上方边缘  是否在浏览器上方边缘
            if (borwerHeight <= append_alert.clientHeight) {
                append_alert.style.height       =  borwerHeight-10+ "px";
                append_alert.style.top= 0;
                append_alert.style.marginTop    = 0;
                append_alert_text.style.height  =  borwerHeight-130+"px";
            }else if(append_alert.offsetTop  <= 0){
                append_alert.style.top          = 0;
                append_alert.style.marginTop    = 0;
            }
            break;
        //如果为图片 就创建一个img标签  里面放图片
        case "image":
            append_alert.appendChild(append_alert_text)
            var append_img = new Image();
            append_img.src = obj;
            append_img.onload = function () {
                append_img.style.maxWidth    = "80%";
                append_img.style.maxHeight   = "800px";
                append_alert.style.marginTop = -(append_alert.clientHeight / 2) + "px";
                //判断弹出框的高度 是否大于浏览器窗口高度，以及弹出框上方边缘  是否在浏览器上方边缘
                if (borwerHeight <= append_alert.clientHeight) {
                    append_alert.style.height    = borwerHeight-10+ "px";
                    append_img.style.height      = borwerHeight-130+"px";
                    append_alert.style.top       = 0;
                    append_alert.style.marginTop = 0;
                }else if(append_alert.offsetTop <= 0){
                    append_alert.style.top       = 0;
                    append_alert.style.marginTop = 0;
                }
            }
            append_alert_text.style.backgroundColor = "white";
            append_alert_text.style.width       = "90%";
            append_alert_text.style.textAlign   = "center";
            append_alert_text.style.marginLeft  = "5%";
            append_alert_text.style.minHeight   = "50px";
            append_alert_text.style.marginTop   = "10px";
            append_alert_text.style.maxHeight   = "800px";
            append_alert_text.appendChild(append_img);
            break;
    }
    append_alert.appendChild(append_alert_yes);
}
    //关闭弹出框
        close_append_alert = function () {
             append_alert.style.display = "none";
            document.getElementById("preson_out").style.display="none";
        }
    //公告弹出 动态效果
    var opcity_min = 0;
    var opcity_max = 100;
    var timer = setInterval(function () {
    var speed = 0;
        if (opcity_min > opcity_max) {
             speed = -10;
        } else {
             speed = 10;
        }
         if (opcity_min == opcity_max) {
             clearInterval(timer);
         } else {
             opcity_min += speed;
             append_alert.style.opacity = opcity_min / 100;
         }
    }, 100);

    window.onload = function () {
        var str="习近平回忆了3年前与美国总统奥巴马在安纳伯格庄园举行会晤的场景。习近平指出，双方同意加强战略沟通，拓展务实合作，妥善管控分歧，努力构建中美新型大国关系。一分耕耘，一分收获。3年耕耘，我们有了不少收获。两国贸易额和双向投资达到历史新高，人文和地方交流更加密切，网络、执法等领域合作和两军交往取得新进展。两国在朝鲜半岛核、伊朗核、阿富汗、叙利亚等热点问题上也保持了有效沟通和协调。这些合作给中美双方带来了实实在在的利益，也有力促进了亚太地区及世界和平、稳定、发展。习近平指出，3年的成果来之不易，也给了我们很多启示，最根本的一条就是双方要坚持不冲突不对抗、相互尊重、合作共赢的原则，坚定不移推进中美新型大国关系建设。这个选择符合中美两国人民根本利益，也是各国人民普遍愿望。无论国际风云如何变幻，我们都应该坚持这个大方向，毫不动摇为之努力。习近平指出，零和博弈、冲突对抗早已不合时宜，同舟共济、合作共赢成为时代要求。作为世界上最大的发展中国家、最大的发达国家和前两大经济体，中美两国更应该从两国人民和各国人民根本利益出发，勇于担当，朝着构建中美新型大国关系的方向奋力前行。 >>谈合作要防止浮云遮眼避免战略误判习近平指出，中美要增强两国互信。中国人历来讲究“信”。2000多年前，孔子就说：“人而无信，不知其可也。”信任是人与人关系的基础、国与国交往的前提。我们要防止浮云遮眼，避免战略误判，就要通过经常性沟通，积累战略互信。这个问题解决好了，中美合作基础就会更加坚实，动力就会更加强劲。要妥善管控分歧和敏感问题。中美两国各具特色，历史、文化、社会制度、民众诉求等不尽相同，双方存在一些分歧是难以避免的。世界是多样的，没有分歧就没有世界。一个家庭里还经常有这样那样的分歧。有了分歧并不可怕，关键是不要把分歧当成采取对抗态度的理由。有些分歧是可以通过努力解决的，双方应该加把劲，把它们解决掉。有些分歧可能一时解决不了，双方应该多从对方的具体处境着想，以务实和建设性的态度加以管控。只要双方遵循相互尊重、平等相待原则，坚持求同存异、聚同化异，就没有过不去的坎，中美两国关系就能避免受到大的干扰。习近平指出，宽广的太平洋不应该成为各国博弈的竞技场，而应该成为大家包容合作的大平台。中美在亚太地区拥有广泛共同利益，应该保持经常性对话，开展更多合作，应对各种挑战，努力培育两国共同而非排他的“朋友圈”，都做地区繁荣稳定的建设者和守护者。 >>谈未来中国坚定不移走和平发展道路习近平指出，今年是中国实施“十三五”规划开局之年。中国将贯彻全面建成小康社会、全面深化改革、全面依法治国、全面从严治党的战略布局，落实创新、协调、绿色、开放、共享的发展理念，着力推进供给侧结构性改革，推动转方式调结构，继续完善对外开放布局。我们对实现中国经济社会发展既定目标充满信心。中国将会为世界提供更多发展机遇，将会同包括美国在内的世界各国开展更密切的合作。中国坚定不移走和平发展道路，倡导各国共同走和平发展道路，推动构建以合作共赢为核心的新型国际关系，打造人类命运共同体。我们愿同世界各国加强合作，共同维护以联合国宪章宗旨和原则为核心的国际秩序和国际体系，推动国际秩序朝着更加公正合理的方向发展，让我们生活的这个星球更加美好。";
         msgBox(str, "text", "公告","700");
    }


