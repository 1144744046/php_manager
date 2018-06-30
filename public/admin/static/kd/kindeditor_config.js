var editor;
$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [ o[this.name] ];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

document.writeln("<style>.ke-icon-mypage{width: 16px;height: 16px; background-position: 0px -1022px;}</style>");

KindEditor.lang({
    mypage : '分页符'
});

KindEditor.plugin('mypage', function(K) {
    var self = this;
    var name = 'mypage';
    var pagebreakHtml = K.undef(self.pagebreakHtml, '[page]');

    self.clickToolbar(name, function() {
        var cmd = self.cmd, range = cmd.range;
        self.focus();
        range.enlarge(true);
        cmd.split(true);
        var tail = self.newlineTag == 'br' || K.WEBKIT ? '' : '<p id="__kindeditor_tail_tag__"></p>';
        self.insertHtml(pagebreakHtml + tail);
        if (tail !== '') {
            var p = K('#__kindeditor_tail_tag__', self.edit.doc);
            range.selectNodeContents(p[0]);
            p.removeAttr('id');
            cmd.select();
        }
    });
});

/*
KindEditor.plugin('mypage', function (K) {
    var self = this, name = 'mypage';
    self.plugin.mypage = {
        edit: function () {
            var html = ['<div style="padding:20px;">',
                '<div class="ke-dialog-row">',
                '<label for="keName">分页副标题</label>',
                '<input class="ke-input-text" type="text" id="keName" name="name" value="" style="width:100px;" />',
                '</div>',
                '</div>'].join('');
            var dialog = self.createDialog({
                name: name,
                width: 300,
                title: '插入分页符号',
                body: html,
                yesBtn: {
                    name: self.lang('yes'),
                    click: function (e) {
                        //if (nameBox.val() == '') {
                            self.insertHtml('[page]').hideDialog().focus();
                        //} else {
                        //    self.insertHtml('[page]' + nameBox.val() + '[/page]').hideDialog().focus();
                        //}
                    }
                }
            });
            var div = dialog.div,
                nameBox = K('input[name="name"]', div);
            var img = self.plugin.getSelectedAnchor();
            if (img) {
                nameBox.val(unescape(img.attr('data-ke-name')));
            }
            nameBox[0].focus();
            nameBox[0].select();
        }
    };
    self.clickToolbar(name, self.plugin.mypage.edit);
});
*/
KindEditor.ready(function (K) {

    editor = K.create('#context', {
        dirName: 'allimg',
        //uploadJson: $CONFIG['adminUrl'] + 'public_backend/public_image_upload',
        //uploadJson: '/static/kd/php/upload_json.php?dir=image',
        uploadJson: '/admin/upload/image',
        items: [
            'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
            'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
            'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
            'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
            'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|',
            //'image','multiimage',
            '|','flash','|',
            'table', 'hr', 'emoticons', 'baidumap',
            'anchor', 'link', 'unlink', 'mypage', '|', 'about'
        ]

    });

});