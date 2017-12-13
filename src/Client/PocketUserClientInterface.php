<?php

namespace Drupal\pocket\Client;

use Drupal\Core\Url;
use Drupal\pocket\PocketItemInterface;

/**
 * Pocket client interface.
 */
interface PocketUserClientInterface {

  /**
   * Perform a request on 'v3/add'.
   *
   * @param \Drupal\Core\Url $url
   *   URL of the submitted content.
   * @param string[]         $tags
   *   (optional) list of tags.
   * @param string           $title
   *   (optional) title. Ignored if the URL provides its own title.
   *
   * @return \Drupal\pocket\PocketItemInterface
   *   The item metadata returned by Pocket.
   *
   * @see https://getpocket.com/developer/docs/v3/add
   */
  public function add(Url $url, array $tags = [], string $title = NULL): PocketItemInterface;

  /**
   * Perform a request on 'v3/send'.
   *
   * Success can be checked for each action via ::isSuccessful().
   *
   * @param \Drupal\pocket\Action\PocketActionInterface[] $actions
   *   An array of actions.
   *
   * @return bool
   *   TRUE if all actions succeeded.
   */
  public function modify(array $actions): bool;

}
