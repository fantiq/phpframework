<?php

namespace framework\debug;

/**
 * 调试工具组件
 * @date 2015-01-25
 * @author vito_v5
 *
 * 这是一个好的想法，但是这个class可能会被移除 调试的输入输出会
 * 移动到输出流里面实现
 */
class Debug {

    /**
     * 打印返回的数组或字符串
     * @param mix $obj
     */
    public static function p($obj) {
        if (is_array($obj)){
            echo '<pre>' . print_r($obj,true) . '</pre>';
        }else{
		    echo $obj;
		}
    }
    
}

?>
