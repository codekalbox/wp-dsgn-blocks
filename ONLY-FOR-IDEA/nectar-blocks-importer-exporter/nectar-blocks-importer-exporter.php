<?php
/**
 * Plugin Name:       Nectarblocks Importer/Exporter
 * Description:       A WordPress Importer Exporter to support the Nectarblocks plugin.
 * Version:           2.4.0
 * Requires at least: 6.2
 * Tested up to:      6.8.0
 * Requires PHP:      7.4
 * Author:            NectarBlocks
 * Author URI:        https://nectarblocks.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       nectar-blocks-importer-exporter
 * Domain Path:       /languages
 */

namespace Nectar;

$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once( $autoload );

require_once( 'nectar-vars.php' );
define( 'NB_IE_VERSION', '2.4.0' );
define( 'NB_IE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NB_IE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

use Nectar\Update\NectarBlocksIEUpdater;

class IEPlugin {
  function __construct() {}

  public function init() {
    $updater = new NectarBlocksIEUpdater();
  }
}

$ie_plugin = new IEPlugin();
$ie_plugin->init();
