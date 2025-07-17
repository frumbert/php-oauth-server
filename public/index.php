<?php

define('BASE_PATH', realpath(__DIR__ . '/..'));
define('URL', $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["HTTP_HOST"]);

require_once BASE_PATH . '/vendor/autoload.php';

use Idp\Handlers\{
  AuthorizeHandler,
  TokenHandler,
  UserInfoHandler,
  OpenIdConfigurationHandler
};

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];

switch ($requestUri) {
    case '/authorize':
        (new AuthorizeHandler())->handle($_GET, $_POST);
        break;
    case '/token':
        (new TokenHandler())->handle();
        break;
    case '/userinfo':
        (new UserInfoHandler())->handle();
        break;
    case '/.well-known/openid-configuration':
        (new OpenIdConfigurationHandler())->handle();
        break;
    case '/.well-known/jwks.json':
        require BASE_PATH . '/src/jwks.php';
        exit;
    default:
        http_response_code(404);
        echo "Not Found";
}
