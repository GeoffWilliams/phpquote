<?php
date_default_timezone_set("Etc/UTC");

require __DIR__ . '/res/config/config.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\DoctrineServiceProvider;

// web/index.php
require_once __DIR__.'/silex/vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\DoctrineServiceProvider(), $db_options);

$app->get('/', function () use ($app) {
    return '***MAIN***';
});

$app->get('/quote/{id}', function ($id) use ($app) {
    $sql = "SELECT * FROM quote WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $id));
    return "<tt>{$post['quote']}</tt><br/>" .
           "{$post['author']}, {$post['ts']}";
});


$app->post('/quote', function () use ($app) {
    return '***helo***';
});

$app->delete('/quote/{id}', function ($id) use ($app) {
    return '***helo***';
});


$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});




$app->error(function (Exception $e, $code) { #, Request $request, $code) {
    return new Response("Error {$code}: {$e->getMessage()}");
});


$lb_ip='192.168.1.10';
Request::setTrustedProxies(array($lb_ip)); 
$app->run();
?>
