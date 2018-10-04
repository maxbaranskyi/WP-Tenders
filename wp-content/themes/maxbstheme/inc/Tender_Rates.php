<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:20
 */

include_once('Tender_Rates_DB.php');

class Tender_Rates
{
    public function __construct()
    {
        add_action('wp_ajax_rate', array($this, 'tender_ajax_rate'));
        add_action('wp_ajax_nopriv_rate', array($this, 'tender_ajax_rate'));
    }

    // Validate rate, and in success write rate to database
    public function tender_ajax_rate() {
        $tender_rates_db = new Tender_Rates_DB();

        $start_rate = get_post_meta($_POST['id_post'], 'start_rate', true);
        $step = get_post_meta($_POST['id_post'], 'step', true);
        $status = get_post_meta($_POST['id_post'], 'status', true);

        $min_rate = $tender_rates_db->get_minimal_rate($_POST['id_post']);

        if ($_POST['price'] < $start_rate
            && ($min_rate - $_POST['price'] >= $step)
            && $status != 'close'
        ) {
            // Made a function
            $result = $tender_rates_db->write_rate_to_db();

            if ($result === false) {
                wp_die('DB ERROR');
            }
            $ajax_res = array(
                'success' => __('Ваша ставка прийнята', 'maxbstheme'),
                'err' => '123'
            );
        } elseif ($status == 'close') {
            $ajax_res = array(
                'success' => __("Вибачте, але тендер вже закритий", 'maxbstheme'),
                'err' => '140'
            );
        } else {
            $ajax_res = array(
                'success' => __("Ваша ставка не відповідає умові", 'maxbstheme'),
                'err' => '123'
            );
        }
        echo json_encode($ajax_res);
        wp_die();
    }
}