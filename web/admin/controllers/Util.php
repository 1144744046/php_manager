<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-5-28
 * Time: 下午1:11
 */
class Util extends Frontend_Controller
{


    public function __construct()
    {
        error_reporting(E_ERROR | E_WARNING);
        parent::__construct();
        H('dir');
    }

    /**
     * 图片上传
     *
     */
    public function  public_image_upload()
    {
        $pdata = $this->input->post(null, true);
        $title = htmlspecialchars($pdata['pictitle'], ENT_QUOTES);
        $upload_allow = C('upload_allow');
        $config['fileField'] = 'upfile';
        $config['maxSize'] = $upload_allow['image_allow']['max_size'];
        $config['allowFiles'] = $upload_allow['image_allow']['exts'];
        if (isset($pdata['savePath']) && !empty($pdata['savePath'])) {
            $config['savePath'] = $pdata['savePath'];
        }
        if (isset($pdata['site']) && !empty($pdata['site'])) {
            $config['site'] = $pdata['site'];
        }

        if (isset($pdata['upload_path']) && !empty($pdata['upload_path'])) {
            $config['upload_path'] = $pdata['upload_path'];
        }

        R('uploader', $config);
        $info = $this->uploader->getFileInfo();
        $data = array(
            'title' => $title,
            'url' => $info["url"],
            'fileType' => $info["type"],
            'original' => $info["originalName"],
            'state' => $info["state"],
        );
        echo json_encode($data);
    }

    /**
     * 附件上传
     */
    public function public_file_upload()
    {
        $pdata = $this->input->post(null, true);
        $title = htmlspecialchars($pdata['pictitle'], ENT_QUOTES);
        $upload_allow = C('upload_allow');
        $config['fileField'] = 'upfile';
        $config['maxSize'] = $upload_allow['attachment_allow']['max_size'];
        $config['allowFiles'] = $upload_allow['attachment_allow']['exts'];
        if (isset($pdata['savePath']) && !empty($pdata['savePath'])) {
            $config['savePath'] = $pdata['savePath'];
        }
        if (isset($pdata['site']) && !empty($pdata['site'])) {
            $config['site'] = $pdata['site'];
        }

        if (isset($pdata['upload_path']) && !empty($pdata['upload_path'])) {
            $config['upload_path'] = $pdata['upload_path'];
        }

        R('uploader', $config);
        $info = $this->uploader->getFileInfo();
        $data = array(
            'title' => $title,
            'url' => $info["url"],
            'fileType' => $info["type"],
            'original' => $info["originalName"],
            'state' => $info["state"],
        );
        echo json_encode($data);
    }

    /**
     * 视频上传
     */
    public function public_flash_upload()
    {
        $pdata = $this->input->post(null, true);
        $title = htmlspecialchars($pdata['pictitle'], ENT_QUOTES);
        $upload_allow = C('upload_allow');
        $config['fileField'] = 'upfile';
        $config['maxSize'] = $upload_allow['flash_allow']['max_size'];
        $config['allowFiles'] = $upload_allow['flash_allow']['exts'];
        if (isset($pdata['savePath']) && !empty($pdata['savePath'])) {
            $config['savePath'] = $pdata['savePath'];
        }
        if (isset($pdata['site']) && !empty($pdata['site'])) {
            $config['site'] = $pdata['site'];
        }

        if (isset($pdata['upload_path']) && !empty($pdata['upload_path'])) {
            $config['upload_path'] = $pdata['upload_path'];
        }

        R('uploader', $config);
        $info = $this->uploader->getFileInfo();
        $data = array(
            'title' => $title,
            'url' => $info["url"],
            'fileType' => $info["type"],
            'original' => $info["originalName"],
            'state' => $info["state"],
        );
        echo json_encode($data);
    }
}