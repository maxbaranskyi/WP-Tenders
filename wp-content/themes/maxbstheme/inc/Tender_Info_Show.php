<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 19:53
 */
include_once ('Tender_Rates_DB.php');

class Tender_Info_Show
{
    public function __construct()
    {
        add_shortcode('min_rate', array($this, 'minimal_rate'));
        add_shortcode('date_start', array($this, 'get_date_start'));
        add_shortcode('date_end', array($this, 'get_date_end'));
    }

    public function minimal_rate($atts)
    {
        global $post;
        $tender_rates_db = new Tender_Rates_DB();

        $min_rate = $tender_rates_db->get_minimal_rate($post->ID);
        return $min_rate;
    }

    private function time_post_format($date)
    {
        $new = new DateTime($date);
        return $new->format('H:i d.m.Y');
    }

    public function get_date_start($atts)
    {
        global $post;
        $date_start = get_post_meta($post->ID, 'date_start', true);
        return $this->time_post_format($date_start);
    }

    public function get_date_end($atts)
    {
        global $post;
        $date_end = get_post_meta($post->ID, 'date_end', true);
        return $this->time_post_format($date_end);
    }
}