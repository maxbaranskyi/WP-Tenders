<?php

/*
 * Plugin Name: Ставки
 * Author: Unknown
 * Version: 0.1
 * Author URI: www.null.com
 *
 *  Copyright 2018  UNKNOWN  (email: noname@null.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook(__FILE__, 'rating_on');
register_deactivation_hook(__FILE__, 'rating_off');
register_uninstall_hook(__FILE__, 'rating_remove');

add_action('admin_menu', 'rating_admin_menu');
add_filter('the_content', 'do_shortcode');

add_shortcode('rate_form', 'show_rate_form');

function rating_on()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rate';

    if($wpdb->get_var("SHOW TABLES LIKE $table_name") != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_user` int(11) NOT NULL,
                    `id_post` int(11) NOT NULL,
                    `price` float NOT NULL,
                    `rate_time` timestamp NOT NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`id`) REFERENCES wp_posts (`ID`) ON DELETE CASCADE
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ";
        $wpdb->query($sql);
    }
}

function rating_off()
{

}

function rating_remove()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rate';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name";
    $wpdb->query($sql);
}

function rating_admin_menu()
{
    add_submenu_page('edit.php?post_type=tenders', 'Ставки', 'Ставки', 8, 'rating', 'rating_editor');
}

function rating_editor()
{
    switch ($_GET['c']) {
        case 'add':
            $action = 'add';
        case 'edit':
            $action = 'edit';
        default:
            $action = 'all';
    }

    include_once("includes/$action.php");
}

function show_rate_form() {
    //ТУТ треба забабахати форму і запис в базу
}