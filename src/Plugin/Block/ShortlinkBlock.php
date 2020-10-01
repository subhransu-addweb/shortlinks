<?php

namespace Drupal\shortlinks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MydataBlock' block.
 *
 * @Block(
 *  id = "shortlinks_block",
 *  admin_label = @Translation("Shortlink block"),
 * )
 */
class ShortlinkBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\shortlinks\Form\GenerateShortLink');
    return $form;
  }

}
