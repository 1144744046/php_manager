<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| 数组
| -------------------------------------------------------------------
*/

/*
 * 二维数组排序
 */
if (!function_exists('my_sort')) {
    function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {
        if (is_array($arrays)) {
            foreach ($arrays as $array) {
                if (is_array($array)) {
                    $key_arrays[] = isset($array[$sort_key]) ? $array[$sort_key] : null;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
        if (isset($key_arrays) && $key_arrays) {
            array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
        }
        return $arrays;
    }
}
