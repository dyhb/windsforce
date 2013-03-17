<?php
 /* DoYouHaoBaby Framework Config File,Do not to modify this file! */ 
 return array (
  'DB_PASSWORD' => '123456',
  'DB_PREFIX' => 'windsforce_',
  'DB_NAME' => 'windsforce_v20120104',
  'DB_CACHE_FIELDS' => true,
  'DB_CACHE' => true,
  'DB_CACHE_TIME' => 86400000000,
  'APP_DEBUG' => true,
  'SHOW_RUN_TIME' => true,
  'SHOW_DB_TIMES' => true,
  'SHOW_GZIP_STATUS' => true,
  'RBAC_DATA_PREFIX' => 'windsforce_rbac_A51421_',
  'COOKIE_PREFIX' => 'windsforce_cB2655_',
  'RBAC_ROLE_TABLE' => 'role',
  'RBAC_USERROLE_TABLE' => 'userrole',
  'RBAC_ACCESS_TABLE' => 'access',
  'RBAC_NODE_TABLE' => 'node',
  'USER_AUTH_ON' => true,
  'USER_AUTH_TYPE' => 1,
  'USER_AUTH_KEY' => 'auth_id',
  'ADMIN_USERID' => '1',
  'ADMIN_AUTH_KEY' => 'administrator',
  'USER_AUTH_MODEL' => 'user',
  'AUTH_PWD_ENCODER' => 'md5',
  'USER_AUTH_GATEWAY' => 'home://public/login',
  'NOT_AUTH_MODULE' => 'public,api',
  'REQUIRE_AUTH_MODULE' => '',
  'NOT_AUTH_ACTION' => '',
  'REQUIRE_AUTH_ACTION' => '',
  'GUEST_AUTH_ON' => true,
  'GUEST_AUTH_ID' => '-1',
  'RBAC_ERROR_PAGE' => 'home://public/rbacerror',
  'RBAC_GUEST_ACCESS' => 
  array (
    'home@stat@*' => true,
    'home@apps@*' => true,
    'home@misc@*' => true,
    'home@attachmentdownload@*' => true,
    'home@attachmentread@*' => true,
    'home@attachment@*' => true,
    'home@attachment@show' => false,
    'home@attachment@add' => false,
    'home@attachment@normal_upload' => false,
    'home@attachment@flash_upload' => false,
    'home@attachment@add_attachmentcomment' => false,
    'home@space@*' => true,
    'home@homesite@*' => true,
    'home@homehelp@*' => true,
  ),
  'RBAC_USER_ACCESS' => 
  array (
    'home@spaceadmin@*' => true,
    'home@spaceadmin@transfer' => false,
    'home@spaceadmin@dotransfer' => false,
    'home@pm@*' => true,
    'home@notice@*' => true,
    'home@friend@*' => true,
    'home@ucenter@index' => true,
    'home@ucenter@homefreshtopic' => true,
    'home@ucenter@audit_homefreshcomment' => true,
  ),
  'TIME_ZONE' => 'Asia/Shanghai',
  'TEMPLATE_TAG_NOTE' => true,
  'APP_DEVELOP' => 0,
  'FRONT_TPL_DIR' => 'Default',
  'ADMIN_TPL_DIR' => 'Default',
  'CACHE_LIFE_TIME' => 8640000,
  'NOT_ALLOWED_EMPTYCONTROL_VIEW' => true,
  'COOKIE_LANG_TEMPLATE_INCLUDE_APPNAME' => false,
  'FRONT_LANGUAGE_DIR' => 'Zh-cn',
  'ADMIN_LANGUAGE_DIR' => 'Zh-cn',
  'LANG_SWITCH' => true,
  'DEFAULT_APP' => 'home',
  'URL_MODEL' => 1,
  'URL_DOMAIN' => '',
  'DB_HOST' => 'localhost',
  'DB_USER' => 'root',
)
?>