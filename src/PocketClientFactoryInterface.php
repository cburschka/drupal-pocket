<?php

namespace Drupal\pocket;

/**
 * Interface for the client factory.
 */
interface PocketClientFactoryInterface {

  /**
   * @param string $accessToken
   *
   * @return \Drupal\pocket\PocketUserClientInterface
   */
  public function getUserClient(string $accessToken): PocketUserClientInterface;

  /**
   * @return \Drupal\pocket\PocketAuthClient
   */
  public function getAuthClient(): PocketAuthClient;

  /**
   * Check if the API key exists.
   *
   * @return bool
   */
  public function hasKey(): bool;

}
