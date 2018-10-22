<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 16:47
 */

// All methods of this class are public, because they all are in using
// in out of class

class TenderRatesDB
{
    /**
     * Write rate to database and use in other class
     *
     * @param integer $user_id
     * @param integer $post_id
     * @param $price integer
     * @return boolean
     */
    public function writeRateToDB($user_id, $post_id, $price)
    {
        global $wpdb;
        $rate = $wpdb->prefix . 'rate';

        $query_string = "INSERT INTO $rate (user_id, post_id, price) VALUES ('%d', '%d', '%f')";
        $query_ready = $wpdb->prepare($query_string, $user_id, $post_id, $price);
        return $wpdb->query($query_ready);
    }

    /**
     * Get a rating of specified post
     *
     * @param integer $post
     * @return array|null
     */
    public function getRating($post)
    {
        global $wpdb;
        $rate = $wpdb->prefix . 'rate';

        $query_string = "SELECT 
            {$wpdb->users}.display_name,
            $rate.price, 
            $rate.rate_time 
        FROM $rate INNER JOIN {$wpdb->posts} ON $rate.post_id = {$wpdb->posts}.ID 
        INNER JOIN {$wpdb->users} ON $rate.user_id = {$wpdb->users}.ID WHERE $rate.post_id = %d;";
        $query_ready = $wpdb->prepare($query_string, $post);
        return $wpdb->get_results($query_ready, ARRAY_A);
    }

    /**
     * Get minimal rate to show in tender page and for some methods
     *
     * @param $post int
     * @return $result int|mixed
     */
    public function getMinimalRate($post)
    {
        global $wpdb;

        $rate = $wpdb->prefix . 'rate';
        $min_rate = $wpdb->get_var("SELECT MIN(price) AS min_price FROM $rate WHERE post_id = '$post'");
        $result = intval($min_rate);

        if(!$result){
            $result = get_post_meta($post, 'start_rate', true);
        }

        return $result;
    }
}

