<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('xml2obj')) {
  function xml2obj($xml, $parentage = array()) {
    $obj = new stdClass;
    foreach ($xml as $element) {
      $tag = $element->getName();
      
      $has_parentage = is_array($parentage) && isset($parentage[$tag]);
      $is_array = $has_parentage && ($parentage[$tag] === true || (is_array($parentage[$tag]) && isset($parentage[$tag][0]) && $parentage[$tag][0] === true));
      $vars = get_object_vars($element);
      $attributes = array();
			if($vars && isset($vars[0])){
				unset($vars[0]);
			}
      $has_vars = !empty($vars);
      if ($has_vars) {
        if(isset($vars['@attributes'])){
          $attributes = $vars['@attributes'];
          unset($vars['@attributes']);
        }
      }
			
      $val = '';
      if ($has_vars) {
        if(!empty($vars) && $element instanceof SimpleXMLElement){
          $val = xml2obj($element, $has_parentage ? $parentage[$tag] : false);
          if(!empty($attributes)){
            $val->{'@'} = new stdClass;
            foreach($attributes as $k=>$v){
              $val->{'@'}->{$k} = $v;
            }
          }
        } else {
          $val = new stdClass;
          $val->_ = trim($element);
          if(!empty($attributes)){
            foreach($attributes as $k=>$v){
              $val->$k = $v;
            }
          }
        }
      }
      else {
        $val = trim($element);
      }
      if($is_array){
        if(!isset($obj->$tag)){
          $obj->$tag = array();
        }
        $obj->{$tag}[] = $val;
      }
      else {
        $obj->$tag = $val;
      }
    }
    return $obj;
  }
}
if ( ! function_exists('arr2xml')) {
  function arr2xml($arr, $level = 0, $parent_tag = null) {
    $white_space_prefix = '';
    if($level > 0){
      $white_space_prefix = str_repeat('  ', $level);
    }
    $xml = '';
    if(empty($arr)){
      return $xml;
    }
    foreach($arr as $key => $obj){
      $tag = isset($parent_tag) ? $parent_tag : $key;
      if(is_object($obj)){
        $obj = get_object_vars($obj);
      }
      if(is_array($obj)){
        if(!empty($obj)){
          if(isset($obj['_'])){
            $text = $obj['_'];
            unset($obj['_']);
            $xml .= $white_space_prefix . '<' . $tag . ' ' . implode(' ', array_map(
                function ($k, $v) {
                  if(is_bool($v)){
                    $v = $v ? 'true' : 'false';
                  }
                  return $k .'="'. htmlspecialchars($v) .'"'; 
                },
                array_keys($obj), $obj
            ));
            if(!strlen($text)){
              $xml .= '/>' . PHP_EOL;
            } else {
              $xml .= '>' . htmlspecialchars($text) . '</' . $tag . '>' . PHP_EOL;
            }
          } else {
            $same = isset($obj[0]);
            if($same){
              $xml .= arr2xml($obj, $level, $tag);
            } else {
              if(isset($obj['@'])){
                $attributes = $obj['@'];
                unset($obj['@']);
                $xml .= $white_space_prefix . '<' . $tag . ' ' . implode(' ', array_map(
                    function ($k, $v) {
                      if(is_bool($v)){
                        $v = $v ? 'true' : 'false';
                      }
                      return $k .'="'. htmlspecialchars($v) .'"'; 
                    },
                    array_keys($attributes), $attributes
                ));
              } else {
                $xml .= $white_space_prefix . '<' . $tag;
              }
              if(empty($obj)){
                $xml .= '/>' . PHP_EOL;
              } else {
                $xml .= '>' . PHP_EOL;
                $xml .= arr2xml($obj, $level+1);
                $xml .= $white_space_prefix . '</' . $tag . '>' . PHP_EOL;
              }
            }
          }
        } else {
          $xml .= $white_space_prefix . '<' . $tag . '/>' . PHP_EOL;
        }
      } else {
        if(strlen($obj)){
          $xml .= $white_space_prefix . '<' . $tag . '>' . htmlspecialchars($obj) . '</' . $tag . '>' . PHP_EOL;
        } else {
          $xml .= $white_space_prefix . '<' . $tag . '/>' . PHP_EOL;
        }
      }
    }
    return $xml;
  }
}