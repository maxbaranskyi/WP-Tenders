<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 16:47
 */

class Tender_Rates_DB
{
    public function __construct()
    {
        add_shortcode('min_rate', array($this, 'minimal_rate'));
    }

    public function write_rate_to_db()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'rate';

        $query_string = "INSERT INTO $table (id_user, id_post, price) VALUES ('%d', '%d', '%f')";
        $query_ready = $wpdb->prepare($query_string, $_POST['id_user'], $_POST['id_post'], $_POST['price']);
        return $wpdb->query($query_ready);
    }

    public function get_rating($post){
        global $wpdb;
        $table = $wpdb->prefix . 'rate';

        $query = "SELECT 
            wp_users.display_name,
            wp_rate.price, 
            wp_rate.rate_time 
        FROM $table INNER JOIN wp_posts ON wp_rate.id_post = wp_posts.ID 
        INNER JOIN wp_users ON wp_rate.id_user = wp_users.ID WHERE wp_rate.id_post = $post;";
        return $wpdb->get_results($query, ARRAY_A);
    }

    public function get_minimal_rate($post)
    {
        $rating = $this->get_rating($post);
        $min_price = $rating[0]['price'];

        foreach ($rating as $rate){
            if ($rate['price'] < $min_price) {
                $min_price = $rate['price'];
            }
        }

        return $min_price;
    }

    public function minimal_rate($atts)
    {
        global $post;
        $min_rate = $this->get_minimal_rate($post->ID);
        return $min_rate;
    }
}