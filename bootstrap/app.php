<?php 
require __DIR__ . '/../vendor/autoload.php';

use Respect\Validation\Factory;

session_start();


$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'codecourse',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ]
    ],
]);


$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function ($container) use ($capsule){
    return $capsule;
};


$container['view'] = function ($container){
    $view = new \Slim\Views\Twig( __DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    
    $view->getEnvironment()->addGlobal('auth', [
        
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
    ]);
    
    
    
    return $view;
};


$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};
$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};
$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['csrf'] = function ($container){
    return new \Slim\Csrf\Guard;
};
$container['auth'] = function ($container){
    return new \App\Auth\Auth;
};



$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);



Factory::setDefaultInstance(
    (new Factory())
        ->withRuleNamespace('App\\Validation\\Rules')
        ->withExceptionNamespace('App\\Validation\\Exceptions')
);

require __DIR__ . '/../app/routes.php';
