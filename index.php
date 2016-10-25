<?php
date_default_timezone_set("Etc/UTC");

if (getenv('DOCKER_MODE')) {
  require __DIR__ . '/res/config/dev_config.php';
} else {
  require __DIR__ . '/res/config/config.php';
}

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

# Database operations use Doctrine DBAL2, see
# http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/
use Silex\Provider\DoctrineServiceProvider;

// web/index.php
require_once __DIR__.'/silex/vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\DoctrineServiceProvider(), $db_options);

# http://silex.sensiolabs.org/doc/cookbook/json_request_body.html#parsing-the-request-body
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});


$app->get('/', function () use ($app) {
    return '***MAIN***';
});

$app->get('/quote/{id}', function ($id) use ($app) {
    $sql = "SELECT * FROM quote WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $id));
    return "<tt>{$post['quote']}</tt><br/>" .
           "{$post['author']}, {$post['ts']}";
});

$app->get('/quotes', function () use ($app) {
    $output = "<h1>Famous Quotes</h1>";
    $sql = "SELECT * FROM quote";
    $stmt = $app['db']->query($sql);
    while ($row = $stmt->fetch()) {
      $output .=  "<tt>{$row['quote']}</tt><br/>" .
                  "{$row['author']}, {$row['ts']}" .
                  "<hr />";
    }

    return $output;
});


$app->post('/quote', function (Request $request) use ($app) {
    $app['db']->insert('quote', array(
      'quote'   => $request->data->quote,
      'author'  => $request->data->author)
    );
    return '***SAVED***';
    
});

$app->delete('/quote/{id}', function ($id) use ($app) {
  $app['db']->delete('quote', array('id' => $id));
  return '***DELETED***';
});



$app->error(function (Exception $e, $code) { #, Request $request, $code) {
    return new Response("Error {$code}: {$e->getMessage()}");
});


$lb_ip='192.168.1.10';
Request::setTrustedProxies(array($lb_ip)); 
$app->run();
?>
