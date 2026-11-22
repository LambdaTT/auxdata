<?php

namespace Auxdata;

use SplitPHP\Request;
use SplitPHP\WebService;
use SplitPHP\Exceptions\Unauthorized;
use SplitPHP\ModsManager;

class Routes extends WebService
{
  public function init()
  {
    $this->addEndpoint('GET', '/v1/?entity?/?key?', function (Request $req) {
      $params = $req->getRoute()->params;
      $entity = strtolower($params['entity']);
      $this->auth([
        "auxdata.{$entity}.read",
        "auxdata.{$entity}.manage"
      ]);

      $result = $this->getService("auxdata/{$params['entity']}")->get(['ds_key' => $params['key']]);

      if (empty($result)) return $this->response->withStatus(404);

      return $this->response
        ->withStatus(200)
        ->withData($result);
    });

    $this->addEndpoint('GET', '/v1/?entity?', function (Request $req) {
      $entity = $req->getRoute()->params['entity'];
      $entitySlug = strtolower($entity);
      $this->auth([
        "auxdata.{$entitySlug}.read",
        "auxdata.{$entitySlug}.manage"
      ]);

      $params = $req->getBody();
      $result = $this->getService("auxdata/{$entity}")->list($params);

      return $this->response
        ->withStatus(200)
        ->withData($result);
    });

    $this->addEndpoint('POST', '/v1/?entity?', function (Request $req) {
      $entity = $req->getRoute()->params['entity'];
      $this->auth("auxdata." . strtolower($entity) . ".manage");

      $data = $req->getBody();
      $result = $this->getService("auxdata/{$entity}")->create($data);

      return $this->response
        ->withStatus(201)
        ->withData($result);
    });

    $this->addEndpoint('PUT', '/v1/?entity?/?key?', function (Request $req) {
      $params = $req->getRoute()->params;
      $this->auth("auxdata." . strtolower($params['entity']) . ".manage");

      $filters = [
        'ds_key' => $params['key']
      ];
      $data = $req->getBody();
      $result = $this->getService("auxdata/{$params['entity']}")->upd($filters, $data);
      if ($result < 1) return $this->response->withStatus(404);

      return $this->response
        ->withStatus(204);
    });

    $this->addEndpoint('DELETE', '/v1/?entity?/?key?', function (Request $req) {
      $params = $req->getRoute()->params;
      $this->auth("auxdata." . strtolower($params['entity']) . ".manage");

      $filters = [
        'ds_key' => $params['key']
      ];
      $result = $this->getService("auxdata/{$params['entity']}")->remove($filters);
      if ($result < 1) return $this->response->withStatus(404);

      return $this->response
        ->withStatus(204);
    });
  }

  private function auth(array|string $permissions)
  {
    if (!ModsManager::modExists('iam')) return;

    // Auth user login:
    if (!$this->getService('iam/session')->authenticate())
      throw new Unauthorized("Não autorizado.");

    // Validate user permissions:
    $this->getService('iam/permission')->requireAny($permissions);
  }
}
