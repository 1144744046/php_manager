<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


if (!function_exists('my_debug')) {
    function my_debug()
    {
        //benchmarks 信息
        foreach (compile_benchmarks() as $header) {
            echo "\n<br />$header\n";
        }
        //sql 信息
        foreach (compile_queries() as $header) {
            echo "\n<br />$header\n";
        }
    }
}

/**
 * Compile Queries
 *
 * @return    string
 */
if (!function_exists('compile_queries')) {
    function compile_queries()
    {
        $dbs = array();
        $result = array();

        // Let's determine which databases are currently connected to
        foreach (get_object_vars(CI()) as $CI_object) {
            if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB')) {
                $dbs[] = $CI_object;
            }
        }

        $query_no = 0;
        foreach ($dbs as $db) {
            if (count($db->queries) > 0) {
                foreach ($db->queries as $key => $val) {
                    $time = number_format($db->query_times[$key], 4);
                    //time val
                    $query_no++;
                    /*
                    $val = str_replace("\n", " ", $val);
                    $val = str_replace("\r", " ", $val);
                    $val = str_replace("\t", " ", $val);
                    //$val = substr($val, 0, 150);
                    */
                    $result[] = "SQL_{$query_no}:{$time}s|" . $val;
                }
            }
        }
        return $result;
    }
}

/*
* benchmarks
*/
if (!function_exists('compile_benchmarks')) {
    function compile_benchmarks()
    {
        $result = array();
        $profile = array();
        foreach (CI()->benchmark->marker as $key => $val) {
        // We match the "end" marker so that the list ends
        // up in the order that it was defined
            if (preg_match("/(.+?)_end/i", $key, $match)) {
                if (isset(CI()->benchmark->marker[$match[1] . '_end']) AND isset(CI()->benchmark->marker[$match[1] . '_start'])) {
                    $profile[$match[1]] = CI()->benchmark->elapsed_time($match[1] . '_start', $key);
                }
            }
        }

        $i = 0;
        foreach ($profile as $key => $val) {
            $i++;
            $key = ucwords(str_replace(array('_', '-'), ' ', $key));
            $result[] = "Benchmark_$i:$key:$val";
        }
        return $result;
    }
}