<?php

namespace framework\debug;

/**
 * 调试工具组件
 * @date 2015-01-25
 * @author vito_v5
 */
class Debug {

    /**
     * 打印返回的数组或字符串
     * @param mix $obj
     */
    public static function p($obj) {
        if (is_array($obj))
            echo '<pre>' . print_r($obj,true) . '</pre>';
        else
            echo $obj;
    }
    
}

?>
