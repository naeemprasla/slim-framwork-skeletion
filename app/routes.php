<?php 

$app->get('/', 'HomeController:index' )->setName('home');



$app->get('/auth/signup', 'AuthController:getSignUp' )->setName('auth.signup');
$app->get('/auth/signup/get', 'AuthController:getall' );
$app->post('/auth/signup', 'AuthController:postSignUp' );


$app->get('/auth/signin', 'AuthController:getSignIn' )->setName('auth.signin');
$app->post('/auth/signin', 'AuthController:postSignIn' );