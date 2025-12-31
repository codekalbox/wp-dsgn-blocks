<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {
  ImportUtils::assign_menu('task-horizon-menu', 'top_nav');
  ImportUtils::assign_front_page('Task Horizon Landing');
  ImportUtils::add_hash_links('task-horizon-menu', [
    'Benefits' => 'benefits',
    'Reviews' => 'reviews',
    'Pricing' => 'pricing',
    'FAQ' => 'faq'
  ]);
}
