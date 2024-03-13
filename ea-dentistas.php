<?php

/**
 * Plugin Name: EA Dentistas
 * Plugin URI: https://agencialaf.com
 * Description: Descrição do EA Dentistas.
 * Version: 0.1.2
 * Author: Ingo Stramm
 * Text Domain: ea-dentistas
 * License: GPLv2
 */

defined('ABSPATH') or die('No script kiddies please!');

define('EA_DENTISTAS_DIR', plugin_dir_path(__FILE__));
define('EA_DENTISTAS_URL', plugin_dir_url(__FILE__));

function ea_dentistas_debug($debug)
{
    echo '<pre>';
    var_dump($debug);
    echo '</pre>';
}

require_once 'functions.php';
require_once 'tgm/tgm.php';
require_once 'classes/classes.php';
require_once 'scripts.php';
require_once 'cmb.php';
require_once 'settings.php';
require_once 'post-type.php';
require_once 'shortcode.php';

require 'plugin-update-checker-4.10/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/IngoStramm/ea-dentistas/master/info.json',
    __FILE__,
    'ea-dentistas'
);
