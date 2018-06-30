$(document).ready(function(){
    alert("tongji")
    // $.ajax({
    //     url: "/cp/api/user/",
    //     data:{time:gettime(),url:geturl(),
    //     ip:getip(),refer:getrefer(),user_agent:getuser_agent()},
    //     type: "GET",
    //     dataType:'json',
    //     success:function(result){
    //         if(result.retcode==0){
    //
    //         }
    //     },
    //     error:function(er){
    //         alert(JSON.stringify(er));
    //     }
    // });

})

function gettime(){
    var nowDate = new Date();
    return nowDate.toLocaleString();
}
function geturl(){
    return window.location.href;
}
function getip(){
    return returnCitySN["cip"]+','+returnCitySN["cname"];
}
function getrefer(){
    return document.referrer;
}
function getcookie(){
    return document.cookie;
}
function getuser_agent(){
    return navigator.userAgent;
}
