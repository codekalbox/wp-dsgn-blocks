<?php

use Nectar\Utilities\{ImportUtils};

function nectar_on_demo_success() {
  ImportUtils::assign_menu('nectar-solutions', 'top_nav');
  ImportUtils::assign_front_page('Nectar Solutions Home');
}
