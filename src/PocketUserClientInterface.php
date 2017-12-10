<?php

namespace Drupal\pocket;

use Drupal\Core\Url;

/**
 * Pocket client interface.
 */
interface PocketUserClientInterface {

  /**
   * @param \Drupal\Core\Url $url
   *   URL of the submitted content.
   * @param string[]         $tags
   *   (optional) list of tags.
   * @param string           $title
   *   (optional) title. Ignored if the URL provides its own title.
   *
   * @return array
   *   The item metadata returned by Pocket.
   *
   * @see https://getpocket.com/developer/docs/v3/add
   */
  public function add(Url $url, array $tags = [], string $title = NULL): array;

}
