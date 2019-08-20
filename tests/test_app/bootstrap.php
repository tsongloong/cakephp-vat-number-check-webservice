<?php
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once 'vendor' . DS . 'autoload.php';

define('ROOT', dirname(__DIR__) . DS);
define('APP_DIR', 'src');

define('TMP', sys_get_temp_dir() . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('SESSIONS', TMP . 'sessions' . DS);

define('CAKE_CORE_INCLUDE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

define('TESTS', ROOT . 'tests' . DS);
define('TEST_APP', TESTS . 'test_app' . DS);
define('APP', TEST_APP . 'src' . DS);
define('WWW_ROOT', TEST_APP . 'webroot' . DS);
define('CONFIG', TEST_APP . 'config' . DS);

$TMP = new Folder(TMP);
$TMP->create(CACHE . 'models');
$TMP->create(CACHE . 'persistent');
$TMP->create(CACHE . 'views');
$TMP->create(LOGS);
$TMP->create(SESSIONS);

// Custom
$controllers = [
    'VatNumberChecks'
];
foreach ($controllers as $controller) {
    $TMP->create(CACHE . 'plugins' . DS . 'VatNumberCheck' . DS . $controller);
}

require_once CORE_PATH . 'config' . DS . 'bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'VatNumberCheck\Test\TestApp',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => APP_DIR,
    'webroot' => 'webroot',
    'wwwRoot' => WWW_ROOT,
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [TEST_APP . 'Plugin' . DS],
        'templates' => [APP . 'Template' . DS],
        'locales' => [APP . 'Locale' . DS],
    ]
]);

Configure::write('Session', [
    'defaults' => 'php'
]);

Cache::setConfig([
    'default' => [
        'engine' => 'Cake\Cache\Engine\FileEngine',
        'prefix' => 'default_',
        'serialize' => true
    ],
    '_cake_core_' => [
        'engine' => 'Cake\Cache\Engine\FileEngine',
        'prefix' => 'cake_core_',
        'serialize' => true
    ],
    '_cake_model_' => [
        'engine' => 'Cake\Cache\Engine\FileEngine',
        'prefix' => 'cake_model_',
        'serialize' => true
    ],
    '_cake_routes_' => [
        'engine' => 'Cake\Cache\Engine\FileEngine',
        'prefix' => 'cake_model_',
        'serialize' => true
    ],
]);

if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}

$config = [
    'url' => getenv('db_dsn'),
    'timezone' => 'UTC',
];

ConnectionManager::setConfig('test', $config);

Plugin::getCollection()->add(new \VatNumberCheck\Plugin());

class_alias('VatNumberCheck\Test\TestApp\Controller\AppController', 'App\Controller\AppController');