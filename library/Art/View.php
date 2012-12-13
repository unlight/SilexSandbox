<?php
namespace Art;

use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpFoundation\Request;

class View {
  private $app = null;
  private $blocks = array();

  public function __construct($app) {
    $this->app = $app;
  }

  public function render( $layout, $template, $vars = array() ) {
    $path = __DIR__ . '/../../view';
    
    // require_once ROOT . '/helpers.php';
    foreach ($vars as $key => $value) { $$key = $value; }
    $app = $this->app;
    ob_start();

    require $path . '/' . $template;

    $content = ob_get_clean();
    
    if ( null == $layout ) {
      return $content;
    }
    
    ob_start();
    require_once $path . '/' . $layout;
    $html = ob_get_clean();

    return $html;
  }
    
  function renderController($uri) {
    $request = $this->app['request'];
    $sign = strpos($uri, "?") ? "&" : "?";
    $uri = "{$uri}{$sign}subrequest=1";

    $subRequest = Request::create(
      $uri, 'get', array(), $request->cookies->all(), 
      array(), $request->server->all()
    );
    
    if ( $request->getSession() ) {
      $subRequest->setSession( $request->getSession() );
    }

    $response = $this->app->handle(
      $subRequest, HttpKernelInterface::SUB_REQUEST, false
    );

    if ( !$response->isSuccessful() ) {
      throw new \RuntimeException(sprintf(
        'Error when rendering "%s" (Status code is %s).', 
        $request->getUri(), $response->getStatusCode()
      ));
    }

    return $response->getContent();
  }

}