<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.18
 * Time: 11:09
 */

function rating_all()
{
    global $wpdb;
    $table = $wpdb->prefix . 'rate';
    $query = "SELECT 
        wp_users.display_name, 
        wp_posts.post_title, 
        wp_rate.price, 
        wp_rate.rate_time 
    FROM wp_rate INNER JOIN wp_posts ON wp_rate.id_post = wp_posts.ID 
    INNER JOIN wp_users ON wp_rate.id_user = wp_users.ID;";
    return $wpdb->get_results($query, ARRAY_A);
}