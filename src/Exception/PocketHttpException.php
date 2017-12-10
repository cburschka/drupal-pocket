<?php

namespace Drupal\pocket\Exception;

use GuzzleHttp\Exception\ServerException;

class PocketHttpException extends ServerException {

  public function __construct(ServerException $exception) {
    parent::__construct(
      $exception->getMessage(),
      $exception->getRequest(),
      $exception->getResponse(),
      $exception->getPrevious(),
      $exception->getHandlerContext()
    );
  }

}
