<?
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_BASE', 'k2_demo');

define('LANG', 'ru');
define('CHMOD_DIR', 0755);
define('CHMOD_FILE', 0644);
define('PASSWORD_SALT', 'bbce2');

umask(0);
?>