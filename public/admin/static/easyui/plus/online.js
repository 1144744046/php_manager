document.write('在线总数:<span id="index_online_num_span"></span>');
//更新在线会员数量
var index_online_num_span = $("#index_online_num_span"), change_index_online_num = function () {
    $.get('/admin/user/public_online_num', function (data) {
        if (data.retcode == 0) {
            index_online_num_span.text(data.msg);
        } else {
            //show_msg(data.msg, 'error');
        }
    }, 'json');
};
change_index_online_num();
window.setInterval(change_index_online_num, 120 * 1000);