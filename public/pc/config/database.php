<?php
/**
 * Created by PhpStorm.
 * User: siliang
 * Date: 2018/1/25
 * Time: 15:08
 */

$db['web'] = array(
    'dsn'	=> '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'si123456',
    'database' => 'adm',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

if(!empty(SITE)&& is_array($db[SITE]))
{
    $active_group=SITE;
    log_message('debug','Load Database: '.SITE);
}
