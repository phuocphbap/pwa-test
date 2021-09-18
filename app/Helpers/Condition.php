<?php
namespace App\Helpers;
class Condition{
    function __construct($arr){
        foreach($arr as $k=>$v){
            $this->$k = $v;
        }
    }
    function has($attr){
        if(isset($this->$attr)) return true;
        return false;
    }
}