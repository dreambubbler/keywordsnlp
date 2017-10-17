<?php

/**
 * @file
 * Contains \Drupal\keywordsnlp\Plugin\migrate\process\keywordsnlpProcess.
 */

namespace Drupal\keywordsnlp\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "keywordsnlp"
 * )
 */
class keywordsnlpProcess extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Transform the URL into Keywords.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $init_url = $value;
    $clean_url = filter_var($init_url, FILTER_SANITIZE_URL);

    $as_text = "";

    // validate URL
    if (!filter_var($clean_url, FILTER_VALIDATE_URL) === false) {
      $clean_url = escapeshellarg($clean_url);

      $result = shell_exec("python3 /var/www/drupalvm/drupal/newspaper3k/keywordsnlp.py " . $clean_url . " 2>&1");

      $words = json_decode($result);

      $as_text = "is clean";

      if (isset($words) && !empty($words)) {
        $as_text = implode('|||', $words);
      }
    }
    else {
      $as_text = "not valid url";
    }

    return $as_text;
  }
}
