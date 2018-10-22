<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 17:47
 */

class TenderWinners
{
    /**
     * Check for winners in tenders, that was closed, in database
     *
     * @param array $late_posts
     * @return array $winners
     */
    public function findTheWinners($late_posts)
    {
        global $wpdb;
        $winners = [];
        $table_rate = $wpdb->prefix . 'rate';

        foreach ($late_posts as $post) {
            $post_id = $post['post_id'];
            $take_win = "SELECT user_id, MIN(price) AS price, post_id 
                      FROM (SELECT * FROM $table_rate WHERE post_id = $post_id) AS closed";
            $win_result = $wpdb->get_results($take_win, ARRAY_A);
            $winners[] = $win_result[0] ;
            return $winners;
        }
    }
}

