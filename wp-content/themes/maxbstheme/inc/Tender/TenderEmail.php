<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 17:49
 */

class TenderEmail
{
    /**
     * Taking information about win and sending email for winners
     *
     * @param array $winners
     */
    public function sendMailToWinners($winners)
    {
        foreach ($winners as $winner) {
            $user = get_userdata($winner['user_id']);
            $user_name = $user->display_name;
            $price = $winner['price'];
            $tender_name = get_post($winner['post_id'])->post_title;

            $message = "Вітаю $user_name, ви виграли тендер $tender_name зі ставкою $price";
            wp_mail($user->user_email, 'Tenders' , $message);
        }
    }
}

