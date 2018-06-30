<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 用户数据模型
|--------------------------------------------------------------------------
|
*/

class Sys_user_detail_model extends Common_model
{

    const TBNAME = 'sys_user_detail';

    public function __construct()
    {
        $this->table_name = self::TBNAME;
        parent::__construct();
    }

    public function update_credit($uid, $rule)
    {
        if (!intval($uid)) return false;
        $this->db->set('extcredits1', 'extcredits1+' . $rule['extcredits1'], FALSE);
        $this->db->set('extcredits2', 'extcredits2+' . $rule['extcredits2'], FALSE);
        $this->db->set('extcredits3', 'extcredits3+' . $rule['extcredits3'], FALSE);
        $this->db->set('extcredits4', 'extcredits4+' . $rule['extcredits4'], FALSE);
        $this->db->set('extcredits5', 'extcredits5+' . $rule['extcredits5'], FALSE);
        $this->db->set('extcredits6', 'extcredits6+' . $rule['extcredits6'], FALSE);
        $this->db->set('extcredits7', 'extcredits7+' . $rule['extcredits7'], FALSE);
        $this->db->set('extcredits8', 'extcredits8+' . $rule['extcredits8'], FALSE);
        $this->db->where('uid', $uid)->update(self::TBNAME);
        return true;
    }


}