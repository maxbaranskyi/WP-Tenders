<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 19:53
 */

include_once 'TenderRatesDB.php';

class TenderInfoShow
{
    public function __construct()
    {
        add_shortcode('min_rate', array($this, 'minimal_rate'));
        add_shortcode('date_start', array($this, 'get_date_start'));
        add_shortcode('date_end', array($this, 'get_date_end'));
    }

    /**
     * Return minimal rate for current or specified post
     *
     * @return integer $min_rate
     */
    public function minimal_rate() {
        global $post;
        $tender_rates_db = new TenderRatesDB();

        $min_rate = $tender_rates_db->getMinimalRate($post->ID);
        return $min_rate;
    }

    /**
     * Return time and date in European format
     *
     * @param string $date
     * @return string
     */
    private function timePostFormat($date)
    {
        $info_date = new DateTime($date);
        return $info_date->format('H:i d.m.Y');
    }

    /**
     * Return date and time of starting tender
     *
     * @return string
     */
    public function get_date_start() {
        global $post;
        $date_start = get_post_meta($post->ID, 'date_start', true);
        return $this->timePostFormat($date_start);
    }

    /**
     * Return date and time of clossing tender
     *
     * @return string
     */
    public function get_date_end() {
        global $post;
        $date_end = get_post_meta($post->ID, 'date_end', true);
        return $this->timePostFormat($date_end);
    }
}