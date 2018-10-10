<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:20
 */

include_once('Tender_Closing.php');
include_once('Tender_Winners.php');
include_once('Tender_Email.php');

class Tender_Win
{
    public function __construct()
    {
        add_filter('cron_schedules', array($this, 'cron_add_one_min'));
        add_action('init', array($this, 'checking_activation'));
        add_action('check_result', array($this, 'check_result_tenders'));
    }

    // Setting of own interval
    public function cron_add_one_min( $schedules ) {
        $schedules['one_min'] = array(
            'interval' => 60 * 1,
            'display' => 'Every minute'
        );
        return $schedules;
    }

    // Activation of CRON interval
    public function checking_activation()
    {
        if (!wp_next_scheduled('check_result')) {
            wp_schedule_event(time(), 'one_min', 'check_result');
        }
    }

    // This method close tenders and check for winners.
    // Next for winners method sends email with congratulations
    public function check_result_tenders() {
        $tender_close = new Tender_Closing();
        $tender_winners = new Tender_Winners();
        $tender_email = new Tender_Email();

        $late_posts = $tender_close->close_tenders();
        $winners = $tender_winners->find_the_winners($late_posts);
        $tender_email->send_mail_to_winners($winners);
    }
}