<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {

  ImportUtils::assign_menu('aura-menu', 'off_canvas_nav');
  ImportUtils::assign_front_page('Aura Home');

  $term_placeholders = [
    'nb_term__culture',
    'nb_term__fashion',
    'nb_term__living',
    'nb_term__wellness',
    'nb_term__inspiration'
  ];

  // Replace the term placeholders in the post content.
  ImportUtils::nectar_replace_term_placeholders('Aura Home', $term_placeholders);
}
