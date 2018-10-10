<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.10.18
 * Time: 17:49
 */

class Tender_Email
{
    // Taking information about win and sending email for winners
    public function send_mail_to_winners($winners)
    {
        foreach ($winners as $winner) {
            $user = get_userdata($winner['id_user']);
            $user_name = $user->display_name;
            $price = $winner['price'];
            $tender_name = get_post($winner['id_post'])->post_title;
            $message = "Вітаю $user_name, ви виграли тендер $tender_name зі ставкою $price";
            wp_mail($user->user_email, 'Tenders' , $message);
        }
    }
}