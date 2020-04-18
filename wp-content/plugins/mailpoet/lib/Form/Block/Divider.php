<?php

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


class Divider {
  public function render($block): string {
    $classes = isset($block['params']['class_name']) ? " " . $block['params']['class_name'] : '';
    return '<hr class="mailpoet_divider' . $classes . '" />';
  }
}
