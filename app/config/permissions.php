<?php
/* * **************
 * Permissions config
 *
 */
$config['roles'] = array(
  'admin',
  'editor', 
  'consilier',
  'specialist',
  'pay24_comenzi',
);
$config['permissions'] = array();
$config['permissions']['backend'] = array(
  'access',
);
$config['permissions']['backend']['account-profile'] = array(
  'access',
  'save',
);
$config['permissions']['backend']['accounts'] = array(
);
$config['permissions']['backend']['accounts']['roles'] = array(
  'access' => $config['roles'],
  'save',
);
$config['permissions']['backend']['accounts']['admin'] = array(
  'access' => $config['roles'],
  'add' => $config['roles'],
  'view' => $config['roles'],
  'edit' => $config['roles'],
  'delete' => $config['roles'],
  'own' => array(
    'access' => $config['roles'],
    'view' => $config['roles'],
    'edit' => $config['roles'],
    'delete' => $config['roles'],
  ),
);
$config['permissions']['backend']['accounts']['customer'] = array(
  'access',
  'view',
  'add',
  'edit',
  'delete',
  'own' => array(
    'access',
    'view',
    'edit',
    'delete',
  ),
);
$config['permissions']['backend']['trip']['orders'] = array(
  'access',
  'view',
  'add',
  'edit',
  'delete',
  'own' => array(
    'access',
    'view',
    'edit',
    'delete',
  ),
);
$config['permissions']['backend']['ticketing'] = array(
  'access',
  // 'assign',
  'view',
  'add',
  'edit',
  'delete',
  'own' => array(
    'access',
    'view',
    'edit',
    'delete',
  ),
);
$config['permissions']['backend']['config'] = array(
  'access',
  'save'
);
$config['permissions']['backend']['cms']['layouts'] = array(
  'access',
  'view',
  'add',
  'edit',
  'delete'
);

$config['permissions']['backend']['cms']['pages'] = array(
  'access',
  'view',
  'add',
  'edit',
  'delete',
  'own' => array(
    'access',
    'view',
    'edit',
    'delete',
  ),
);

$config['permissions']['backend']['cms']['resources'] = array(
  'access',
);

/* $config['permissions']['backend']['social_networks'] = array(
  'access',
  'save',
); */