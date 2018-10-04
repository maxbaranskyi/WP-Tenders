<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 17:41
 */

class Tender_Closing
{
    private $late_posts;
    // Getting a ID's of posts, that must be clossing
    private function search_late_posts()
    {
        global $wpdb;

        $table_meta = $wpdb->prefix . 'postmeta';
        $close_query = "SELECT post_id 
              FROM $table_meta 
              WHERE meta_key = 'date_end' AND meta_value < CURRENT_TIMESTAMP 
              AND $table_meta.post_id IN (SELECT post_id 
                                          FROM $table_meta 
                                          WHERE meta_key = 'status' AND meta_value = 'open' )";
        return $wpdb->get_results($close_query, ARRAY_A);
    }

    // Close the posts, after getting ID's
    public function close_tenders()
    {
        $this->late_posts = $this->search_late_posts();

        foreach ($this->late_posts as $post) {
            update_post_meta($post['post_id'], 'status', 'close');
        }

        return $this->late_posts;
    }

}