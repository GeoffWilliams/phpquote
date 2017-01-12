<?php
date_default_timezone_set("Etc/UTC");

if (getenv('DOCKER_MODE')) {
  require __DIR__ . '/res/config/dev_config.php';
} else {
  require __DIR__ . '/res/config/config.php';
  $db_options['db.options'] = array(
    "driver"    => "pdo_mysql",
    "user"      => $db_user,
    "password"  => $db_password,
    "dbname"    => $db_name,
    "host"      => $db_host,
  );
}

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\Debug;

# Database operations use Doctrine DBAL2, see
# http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/
use Silex\Provider\DoctrineServiceProvider;

// web/index.php
require_once __DIR__.'/silex/vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\DoctrineServiceProvider(), $db_options);

# twig templating
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


# http://silex.sensiolabs.org/doc/cookbook/json_request_body.html#parsing-the-request-body
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});


$app->get('/', function () use ($app) {
  return $app['twig']->render('index.twig', array());
});

$app->get('/quote/new', function () use ($app) {
  return $app['twig']->render('new.twig', array());
});

$app->get('/quote/{id}', function ($id) use ($app) {
    if ($id == "random") {
      $sql  = "SELECT * FROM quote ORDER BY RAND() LIMIT 1";
      $bind = array();
    } else {
      $sql  = "SELECT * FROM quote WHERE id = ?";
      $bind = array((int) $id);
    }
    $data = $app['db']->fetchAssoc($sql, $bind);
    return $app['twig']->render('display.twig', array(
      'quote'   => $data['quote'],
      'author'  => $data['author'],
      'ts'      => $data['ts'],
    )
  );
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
      'quote'   => $request->request->get('quote'),
      'author'  => $request->request->get('author')
    ));
    return '***SAVED***';

});

$app->delete('/quote/{id}', function ($id) use ($app) {
  $app['db']->delete('quote', array('id' => $id));
  return $app['twig']->render('deleted.twig', array(
      'id'   => $id,
    )
  );
});



$app->error(function (Exception $e, $code) { #, Request $request, $code) {
    return new Response("Error {$code}: {$e->getMessage()}");
});


$lb_ip='192.168.1.10';
Request::setTrustedProxies(array($lb_ip));

// Enable PHP Error level
error_reporting(E_ALL);
ini_set('display_errors','On');
// Enable debug mode
$app['debug'] = true;
// Handle fatal errors
ErrorHandler::register();
Debug::enable();

$app->run();
?>
