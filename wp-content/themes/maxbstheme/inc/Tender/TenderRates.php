<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:20
 */

include_once('TenderRatesDB.php');

class TenderRates
{
    public function __construct()
    {
        add_action('wp_ajax_rate', array($this, 'tender_ajax_rate'));
        add_action('wp_ajax_nopriv_rate', array($this, 'tender_ajax_rate'));
    }

    // Validate rate, and in success write rate to database
    public function tender_ajax_rate()
    {
        $tender_rates_db = new TenderRatesDB();

        // Information about rate and clean values
        $post_id = filter_var($_POST['post_id'], FILTER_SANITIZE_NUMBER_INT);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
        $user_id = get_current_user_id();

        // Information about tender for using in condition
        $start_rate = get_post_meta($post_id, 'start_rate', true);
        $step = get_post_meta($post_id, 'step', true);
        $status = get_post_meta($post_id, 'status', true);
        $min_rate = $tender_rates_db->getMinimalRate($post_id);

        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce( $nonce, 'submit_rate' ) ) {
            wp_die();
        }

        // Write a rate to dababase if rate is less than start_rate,
        // difference between rate and start_price is bigger than step
        // and tender are opening
        if ($price < $start_rate
            && ($min_rate - $price >= $step)
            && $status !== 'close'
        ) {
            // Made a function
            $result = $tender_rates_db->writeRateToDB($user_id, $post_id, $price);

            if ($result === false) {
                wp_die('DB ERROR');
            }
            $ajax_res = array(
                'success' => __('Ваша ставка прийнята', 'maxbstheme'),
            );
        } elseif ($status === 'close') {
            $ajax_res = array(
                'success' => __('Вибачте, але тендер вже закритий', 'maxbstheme'),
            );
        } else {
            $ajax_res = array(
                'success' => __('Ваша ставка не відповідає умові', 'maxbstheme'),
            );
        }
        echo json_encode($ajax_res);
        wp_die();
    }
}

