<?php

class BackendMenuItem {
  public $current = false;
  public $open = false;
  public $active = false;
  public $allow = true;
  public $icon = '';
  public $key;
  public $keys = '';
  public $ids = '';
  public $route;
  public $get;
  public $title;
  public $parent;
  public $items = array();
  function __construct($menu_item = null, &$parent=null, $key=null){
    if(isset($menu_item['allow']) && is_bool($menu_item['allow'])){
      $this->allow = $menu_item['allow'];
    }
    if(!$this->allow){
      return;
    }
    if(isset($menu_item['active'])){
      $this->active = $menu_item['active'];
    }
    $this->parent = $parent;
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
    if(isset($menu_item['title']) && is_string($menu_item['title'])){
      $this->title = $menu_item['title'];
    } else {
      if(isset($this->key) && $this->parent){
        $this->title = lang($this->ids);
      }
    }
    if(isset($menu_item['icon']) && is_string($menu_item['icon'])){
      $this->icon = $menu_item['icon'];
    } else {
      if(isset($this->key) && $this->parent){
        $this->icon = lang($this->ids . '_icon/html');
      }
    }
    
    if(isset($menu_item['url']) && is_string($menu_item['url'])){
      $this->url = $menu_item['url'];
      $url_parsed = parse_url($this->url);
      if(!isset($url_parsed['host']) && !isset($url_parsed['scheme'])){
        $this->route = $url_parsed['path'];
        parse_str($url_parsed['query'],$this->get);
        $this->url = base_url($this->url);
      }
    } else {
      if(isset($menu_item['get']) && is_array($menu_item['get']) && !empty($menu_item['get'])){
        $this->get = $menu_item['get'];
      }
      if(isset($menu_item['route']) && is_string($menu_item['route'])){
        $this->route = $menu_item['route'];
      }
      if(isset($this->route)){
        $this->url = site_url($this->route . (isset($this->get) ? '?' . http_build_query($this->get) : ''));
      }
    }
    if(isset($this->route)){
      $current_route = CI::$APP->uri->uri_string();
      if($this->route == $current_route){
        $this->active = true;
        if($this->active && isset($this->get)){
          $get = CI::$APP->input->get();
          $get_intersect = array_intersect($this->get, $get);
          $this->active = $this->get === $get_intersect;
        }
      }
    }
    $this->current = $this->active;
    $this->open = $this->active;
    if(isset($menu_item['items']) && is_array($menu_item['items']) && !empty($menu_item['items'])){
      if(!isset($this->url)){
        $this->allow = false;
      }
      $this->addItems($menu_item['items']);
      if($this->items){
        $this->allow = true;
      }
    }
  }
  function addItem($item, $k=null){
    $new_item = new BackendMenuItem($item,$this,$k);
    if(!$new_item->allow){
      unset($new_item);
      return;
    }
    if(!$this->open){
      if($new_item->open){
        $this->open = $new_item->open;
      }
    }
    $this->items[] = $new_item;
    unset($new_item);
  }
  function addItems($items){
    foreach($items as $k=>&$item){
      $this->addItem($item, $k);
    }
  }
  function render(){
    if(!$this->allow){
      return;
    }
    $id_prefix = $this->keys . '_nav';
    if(isset($this->title)){
      $url = 'javascript:void(0);';
      $attributes = array();
      if($this->items){
        $attributes['aria-expanded'] = $this->open;
      }
      if(isset($this->url)){
        $url = $this->url;
      } elseif($this->items){
        $url = '#' . $id_prefix . '_list';
        $attributes['data-toggle']="collapse";
        $attributes['aria-expanded'] = $this->open;
      }
      
      ?><a id="<?php echo $id_prefix . '_item'; ?>" href="<?php echo htmlspecialchars($url); ?>" <?php foreach($attributes as $k=>$v){echo ' ' . $k . '=' . json_encode($v);} ?>>
        <?php if($this->items){ ?>
        <div class="arrow pull-right">
          <i class="fa fa-angle-down"></i>
        </div>
        <?php } ?>
        <?php echo $this->icon; ?>
        <span><?php echo htmlspecialchars($this->title); ?></span>
      </a><?php
    }
    if($this->items){
    ?><ul id="<?php echo $id_prefix . '_list'; ?>" class="<?php echo $this->keys; ?> list-unstyled<?php echo $this->parent ? ' collapse' . ($this->open ? ' show' : '') : ''; ?>"><?php
      foreach($this->items as &$item){ ?><li class="<?php echo $item->active ? 'active' . ($item->current ? ' current' : '') : ''; ?>"><?php
        $item->render();
        ?></li><?php
      }
    ?></ul><?php
      return;
    }
  }
}