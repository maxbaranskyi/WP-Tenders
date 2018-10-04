<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 16:45
 */

require_once('wp_bootstrap_navwalker.php');
require_once('Tender_Posts.php');
require_once('Tender_Metaboxes.php');
require_once('Tender_Rates.php');
require_once('Tender_Win.php');
require_once('Theme_Settings.php');
require_once('Tender_Info_Show.php');

$tender_posts = new Tender_Posts();
$tender_metaboxes = new Tender_Metaboxes();
$tender_rates = new Tender_Rates();
$tender_win = new Tender_Win();
$settings = new Theme_Settings();
$show_info = new Tender_Info_Show();