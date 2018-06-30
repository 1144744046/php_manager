
var base_url="http://www.diao0.cn/";
function check_text(str){
    var reg = /^[0-9a-zA-Z]+$/;
    if(!reg.test(str)){
        return false;
    }
    return true;
}

function Toast(msg,duration){
    duration=isNaN(duration)?3000:duration;
    var m = document.createElement('div');
    m.innerHTML = msg;
    m.style.cssText="width: 50%;min-width: 3rem;opacity: 0.7;height: 2rem;color: rgb(255, 255, 255);line-height: 2rem;text-align: center;border-radius: 5px;position: fixed;top: 40%;left:30%;z-index: 999999;background: rgb(0, 0, 0);font-size: .7rem;";
    document.body.appendChild(m);
    setTimeout(function() {
        var d = 0.5;
        m.style.webkitTransition = '-webkit-transform ' + d + 's ease-in, opacity ' + d + 's ease-in';
        m.style.opacity = '0';
        setTimeout(function() { document.body.removeChild(m) }, d * 1000);
    }, duration);
}
