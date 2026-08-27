<?php

class FrontendMenuItem {
  public $current = false;
  public $open = false;
  public $active = false;
  public $allow = true;
  public $icon = '';
  public $key;
  public $keys = '';
  public $ul_class = '';
  public $ids = '';
  public $route;
  public $get;
  public $title;
  public $url;
  public $target;
  public $parent;
  public $level = 0;
  public $children = array();
  function __construct($menu_item = null, &$parent=null, $key=null){
    $menu_item = (array)$menu_item;
    if(isset($menu_item['allow']) && is_bool($menu_item['allow'])){
      $this->allow = $menu_item['allow'];
    }
    if(!$this->allow){
      return;
    }
    if(isset($menu_item['active'])){
      $this->active = $menu_item['active'];
    }
    if(isset($menu_item['ul_class'])){
      $this->ul_class = $menu_item['ul_class'];
    }
    if(isset($menu_item['render_function'])){
      $this->render_function = $menu_item['render_function'];
    }
    $this->parent = $parent;
    if($this->parent){
      $this->level = $this->parent->level + 1;
    }
    $this->key = $key;
    if(isset($menu_item['key'])){
      $this->key = $menu_item['key'];
    }
    if($this->parent){
      $this->keys .= $parent->keys . '-';
      $this->ids .= $parent->ids . '_';
    }
    $this->keys .= $this->key;
    $this->ids .= $this->key;
    if(isset($menu_item['target']) && is_string($menu_item['target'])){
      $this->target = $menu_item['target'];
    }
    if(isset($menu_item['title']) && is_string($menu_item['title'])){
      $this->title = $menu_item['title'];
    }
    if(isset($menu_item['icon']) && is_string($menu_item['icon'])){
      $this->icon = $menu_item['icon'];
    }
    
    if(isset($menu_item['url']) && is_string($menu_item['url']) && strlen($menu_item['url'])){
      $this->url = $menu_item['url'];
      $url_parsed = parse_url($this->url);
      if(!isset($url_parsed['host']) && !isset($url_parsed['scheme'])){
        if(isset($url_parsed['path'])){
          $this->route = trim($url_parsed['path'],' /');
        }
        if(isset($url_parsed['query'])){
          parse_str($url_parsed['query'],$this->get);
        }
        if(strlen($this->url)){
          $this->url = base_url($this->url);
        }
      }
    }
    if(isset($this->route)){
      $current_route = CI::$APP->uri->uri_string();
      if($this->route == $current_route){
        $this->active = true;
        if($this->active && isset($this->get)){
          $get = CI::$APP->input->get();
          $get_intersect = array_intersect_key($this->get, $get);
          $this->active = $this->get === $get_intersect;
        }
      }
    }
    $this->current = $this->active;
    if(isset($menu_item['children']) && is_array($menu_item['children']) && !empty($menu_item['children'])){
      if(!isset($this->url)){
        $this->allow = false;
      }
      $this->addChildren($menu_item['children']);
      if($this->children){
        $this->allow = true;
      }
    }
  }
  function addChild($child, $k=null){
    $new_child = new FrontendMenuItem($child,$this,$k);
    if(!$new_child->allow){
      unset($new_item);
      return;
    }
    if(!$this->open){
      if($new_child->open || $new_child->active){
        $this->open = true;
      }
    }
    $this->children[] = $new_child;
    unset($new_child);
  }
  function addChildren($children){
    foreach($children as $k=>&$child){
      $this->addChild($child, $k);
    }
  }
  
  function render(){
    if(!isset($this->render_function) && $this->parent){
      $this->render_function = $this->parent->render_function;
    }
    if(!isset($this->render_function) || !method_exists($this,$this->render_function)){
      $this->render_function = 'render_style_main';
    }
    $this->{$this->render_function}();
  }
  function render_style_main(){
    if(!$this->allow){
      return;
    }
    $this->render_function = __FUNCTION__;
    $id_prefix = $this->keys . '_nav';
    if(isset($this->title)){
      $url = 'javascript:void(0);';
      $attributes = array();
      $attributes['id'] = $id_prefix . '_item';
      $classes = array();
      if($this->level == 1){
        $classes[] = "nav-link";
      }
      if($this->children){
        $attributes['aria-expanded'] = $this->open;
      }
      if(isset($this->url)){
        $url = $this->url;
      }
      if($this->children){
        if($this->level == 1){
          $classes[] = "dropdown-toggle";
        }
        $attributes['aria-haspopup'] = 'true';
        $attributes['aria-expanded'] = 'false';
      }
      if($this->url && $this->level>1){
        $classes[] = "dropdown-item";
        if($this->active){
          $classes[] = "active";
        }
        if($this->current){
          $classes[] = "current";
        }
      }
      if($classes){
        $attributes['class'] = implode(' ', $classes);
      }
      if($this->url){
        $attributes['href'] = $this->url;
      }
      if(isset($this->target)){
        $attributes['target'] = $this->target;
      }
      ?><a <?php foreach($attributes as $k=>$v){echo ' ' . $k . '="' . htmlspecialchars($v) .'"';} ?>><?php 
      echo $this->icon; 
      echo htmlspecialchars($this->title); 
      ?></a><?php
    }
    if($this->children){ ?>
      <ul id="<?php echo $id_prefix . '_list'; ?>" class="<?php echo $this->ul_class; ?><?php echo $this->level ? ' dropdown-menu' : ''; ?>" <?php echo $this->level==1 ? 'style="margin-top:-1px;"' : ''; ?>><?php
      foreach($this->children as &$item){
        if(!$this->level || $item->children || ($this->level && !$item->url)){
        ?><li class="<?php echo $this->level ? ($item->children ? 'dropdown-submenu' : 'dropdown-item') : 'nav-item' . ($item->children ? ' dropdown clickable' : ''); ?><?php echo $item->active || ($item->children && $item->open) ? ' active' : '' ; ?><?php echo ($this->level && $item->children && $item->open) ? ' show' : '' ; ?><?php echo $item->current ? ' current' : ''; ?>"><?php
        $item->render();
        ?></li><?php
        } else {
          $item->render();
        }
      }
    ?></ul><?php
      return;
    }
  }
  function render_style_footer(){
    if(!$this->allow){
      return;
    }
    $this->render_function = __FUNCTION__;
    $id_prefix = $this->keys . '_nav';
    if(isset($this->title)){
      $url = 'javascript:void(0);';
      $attributes = array();
      $attributes['id'] = $id_prefix . '_item';
      if(isset($this->url)){
        $url = $this->url;
      } elseif($this->children){
        $url = '#' . $id_prefix . '_list';
      }
      if($this->url){
        $attributes['href'] = $this->url;
      }
      if(isset($this->target)){
        $attributes['target'] = $this->target;
      }
      
      ?><a <?php foreach($attributes as $k=>$v){echo ' ' . $k . '="' . htmlspecialchars($v) . '"';} ?>>
        <?php if($this->children){ ?>
        <?php } ?>
        <?php echo $this->icon; ?>
        <span><?php echo htmlspecialchars($this->title); ?></span>
      </a><?php
    }
    if($this->children){
    ?><ul id="<?php echo $id_prefix . '_list'; ?>" class="<?php echo $this->ul_class; ?> <?php echo $this->keys; ?> list-unstyled<?php echo $this->parent ? ' pl-3' : ''; ?>"><?php
      foreach($this->children as &$item){ ?><li class="<?php echo $item->active ? 'active' . ($item->current ? ' current' : '') : ''; ?>"><?php
        $item->render();
        ?></li><?php
      }
    ?></ul><?php
      return;
    }
  }
  function render_style_info(){
    if(!$this->allow){
      return;
    }
    $this->render_function = __FUNCTION__;
    $id_prefix = $this->keys . '_nav';
    if(isset($this->title)){
      $url = 'javascript:void(0);';
      $attributes = array();
      $attributes['id'] = $id_prefix . '_item';
      $classes = array();
      if(isset($this->url)){
        $url = $this->url;
      } elseif($this->children){
        $url = '#' . $id_prefix . '_list';
      }
      if($this->url){
        $attributes['href'] = $this->url;
      }
      if(isset($this->target)){
        $attributes['target'] = $this->target;
      }
      $classes[] = 'd-block';
      if($classes){
        $attributes['class'] = implode(' ', $classes);
      }
      ?><a <?php foreach($attributes as $k=>$v){echo ' ' . $k . '="' . htmlspecialchars($v) . '"';} ?>>
        <?php if($this->children){ ?>
        <?php } ?>
        <?php echo $this->icon; ?>
        <span><?php echo htmlspecialchars($this->title); ?></span>
      </a><?php
    }
    if($this->children){
    ?><ul id="<?php echo $id_prefix . '_list'; ?>" class="<?php echo $this->ul_class; ?> <?php echo $this->keys; ?> list-unstyled<?php echo $this->parent ? ' m-0' : ''; ?>"><?php
      foreach($this->children as &$item){ ?><li class="<?php echo $item->active ? 'active' . ($item->current ? ' current' : '') : ''; ?>"><?php
        $item->render();
        ?></li><?php
      }
    ?></ul><?php
      return;
    }
  }
}