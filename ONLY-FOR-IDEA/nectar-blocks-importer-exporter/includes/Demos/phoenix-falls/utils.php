<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {
  ImportUtils::assign_menu('cafe-left-menu', 'top_nav_pull_left');
  ImportUtils::assign_menu('cafe-right-menu', 'top_nav_pull_right');
  ImportUtils::assign_front_page('Café Landing');
}
