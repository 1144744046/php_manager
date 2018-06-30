<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//发送短信
//function send_sms($phone, $content, $send_time = '')
//{
//    if (!is_mobile_number($phone)) {
//        return '手机号码错误!';
//    }
//    if (empty($content)) {
//        return '短信内容不能为空!';
//    }
//    $url = "http://18dx.cn/API/Services.aspx?";
//    $para = "action=msgsend&" . sys_config_get('18dx_sms');
//    $para .= "&mobile=$phone&content=" . urlencode($content);
//    $url .= $para;
//
//    multi_thread($url);
//
//    return '短信发送成功!';
//}
  function makeCode(){
      $verifyCode = implode('', range(0, 9));
      //打乱字符串
      $verifyCode = str_shuffle($verifyCode);
      return substr($verifyCode, 0,4);
  }
 function send_sms($phone){
    R('cp/smsDemo');
    $Alisms = new SmsDemo();
    $mobile_code=makeCode();
    $response = $Alisms->sendSms(
        "红人在线", // 短信签名
        "SMS_89610034", // 短信模板编号
        $phone, // 短信接收者
        Array(  // 短信模板中字段的值
            "code"=>$mobile_code,
        )
    );
    if($response->Message=='OK'){
        set_sess("mobile_code",$mobile_code);
        set_sess("mobile",$phone);
        return true;
    }
    return false;
}
