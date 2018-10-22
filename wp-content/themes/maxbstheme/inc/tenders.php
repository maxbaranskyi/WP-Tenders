<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 16:45
 */

require_once('wp_bootstrap_navwalker.php');
require_once('ThemeSettings.php');

require_once(__DIR__ . '/Tender/TenderPosts.php');
require_once(__DIR__ . '/Tender/TenderMetaboxes.php');
require_once(__DIR__ . '/Tender/TenderRates.php');
require_once(__DIR__ . '/Tender/TenderWin.php');
require_once(__DIR__ . '/Tender/TenderInfoShow.php');

$settings = new themeSettings();

$tender_posts = new TenderPosts();
$tender_metaboxes = new TenderMetaboxes();
$tender_rates = new TenderRates();
$tender_win = new TenderWin();
$show_info = new TenderInfoShow();

