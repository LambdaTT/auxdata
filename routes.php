<?php

namespace Auxdata;

use SplitPHP\Request;
use SplitPHP\WebService;
use SplitPHP\Exceptions\Unauthorized;

class Routes extends WebService
{
  public function init()
  {
    $this->addEndpoint('GET', '/v1/?entity?/?key?', function (Request $req) {
      $params = $req->getRoute()->params;
      $tableName = strtoupper("AUX_{$params['entity']}");
      $this->auth([
        $tableName => 'R'
      ]);

      $result = $this->getService("auxdata/{$params['entity']}")->get(['ds_key' => $params['key']]);

      if (empty($result)) return $this->response->withStatus(404);

      return $this->response
        ->withStatus(200)
        ->withData($result);
    });

    $this->addEndpoint('GET', '/v1/?entity?', function (Request $req) {
      $entity = $req->getRoute()->params['entity'];
      $tableName = strtoupper("AUX_{$entity}");
      $this->auth([
        $tableName => 'R'
      ]);

      $params = $req->getBody();
      $result = $this->getService("auxdata/{$entity}")->list($params);

      return $this->response
        ->withStatus(200)
        ->withData($result);
    });

    $this->addEndpoint('POST', '/v1/?entity?', function (Request $req) {
      $entity = $req->getRoute()->params['entity'];
      $tableName = strtoupper("AUX_{$entity}");
      $this->auth([
        $tableName => 'C'
      ]);

      $data = $req->getBody();
      $result = $this->getService("auxdata/{$entity}")->create($data);

      return $this->response
        ->withStatus(201)
        ->withData($result);
    });

    $this->addEndpoint('PUT', '/v1/?entity?/?key?', function (Request $req) {
      $params = $req->getRoute()->params;
      $tableName = strtoupper("AUX_{$params['entity']}");
      $this->auth([
        $tableName => 'U'
      ]);

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
      $tableName = strtoupper("AUX_{$params['entity']}");
      $this->auth([
        $tableName => 'D'
      ]);

      $filters = [
        'ds_key' => $params['key']
      ];
      $result = $this->getService("auxdata/{$params['entity']}")->remove($filters);
      if ($result < 1) return $this->response->withStatus(404);

      return $this->response
        ->withStatus(204);
    });
  }

  private function auth(array $permissions)
  {
    if (!$this->getService('modcontrol/control')->moduleExists('iam')) return;

    // Auth user login:
    if (!$this->getService('iam/session')->authenticate())
      throw new Unauthorized("Não autorizado.");

    // Validate user permissions:
    $this->getService('iam/permission')
      ->validatePermissions($permissions);
  }
}
