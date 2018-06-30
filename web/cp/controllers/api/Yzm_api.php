<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/2/3
 * Time: 2:11
 */

class Yzm_api extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    public function get_yzm(){
        $validateLength=4;
        $strToDraw="";
        $chars=[
            "0","1","2","3","4",
            "5","6","7","8","9",
            "a","b","c","d","e","f","g",
            "h","i","j","k","l","m","n",
            "o","p","q","r","s","t",
            "u","v","w","x","y","z",
            "A","B","C","D","E","F","G",
            "H","I","J","K","L","M","N",
            "O","P","Q","R","S","T",
            "U","V","W","X","Y","Z"
        ];
        $imgW=80;
        $imgH=40;
        $imgRes=imagecreate($imgW,$imgH);
        //白色背景
        $white = imagecolorallocate($imgRes, 255, 255, 255);
        //字体颜色
        $fontStyle = imagecolorallocate($imgRes, rand(0, 255),rand(0, 255), rand(0, 255));;
        imagefill($imgRes, 0, 0, $white);
        for($i=0;$i<$validateLength;$i++){
            $rand=rand(1,58);
            $strToDraw=$strToDraw." ".$chars[$rand];
        }
        imagestring($imgRes,15,0,10,$strToDraw,$fontStyle);
        for($i=0;$i<100;$i++){
            imagesetpixel($imgRes,rand(0,$imgW),rand(0,$imgH),$fontStyle);
        }
        header("Content-type: image/png");
        imagepng($imgRes);
        imagedestroy($imgRes);
        //存入sesion
        $arr=explode(" ",$strToDraw);
        $yzm_code=implode("",$arr);
        //注册是的
        set_sess("yzm_code",strtolower($yzm_code));
    }


}