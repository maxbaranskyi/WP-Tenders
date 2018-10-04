<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 17:47
 */

class Tender_Winners
{
    // Check for winners in tenders, that was closed, in database
    public function find_the_winners($late_posts)
    {
        global $wpdb;
        $winners = array();
        $table_rate = $wpdb->prefix . 'rate';

        foreach ($late_posts as $post) {
            $post_id = $post['post_id'];
            $take_win = "SELECT id_user, MIN(price) AS price, id_post 
                      FROM (SELECT * FROM $table_rate WHERE id_post = $post_id) AS closed";
            $win_result = $wpdb->get_results($take_win, ARRAY_A);
            array_push($winners, $win_result[0]);
            return $winners;
        }
    }
}