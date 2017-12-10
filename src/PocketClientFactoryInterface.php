<?php

namespace Drupal\pocket;

/**
 * Interface for the client factory.
 */
interface PocketClientFactoryInterface {

  /**
   * @param string $accessToken
   *
   * @return \Drupal\pocket\PocketClientInterface
   */
  public function getClient(string $accessToken): PocketClientInterface;

}
