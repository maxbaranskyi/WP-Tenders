<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.09.18
 * Time: 10:14
 */

class Tenders
{

    public function __construct()
    {
        add_action('init', array($this, 'test_post_types'));
        add_action('add_meta_boxes', array($this, 'tender_meta_box'));
        add_action('save_post', array($this, 'save_tender_meta_fields'));
        add_action('wp_ajax_rate', array($this, 'test_ajax_rate'));
        add_action('wp_ajax_nopriv_rate', array($this, 'test_ajax_rate'));
        add_filter('cron_schedules', array($this, 'cron_add_one_min'));
        add_action('init', array($this, 'checking_activation'));
        add_action('check_result', array($this, 'check_result_tenders'));
        add_shortcode('min_rate', array($this, 'minimal_rate'));
    }

    // Making custom type of posts and taxonomy for them
    public function test_post_types() {
        register_post_type('tenders', [
            'labels' => [
                'name'               => 'Тендери',
                'singular_name'      => 'Тендер',
                'add_new'            => 'Створити тендер',
                'add_new_item'       => 'Створення тендеру',
                'edit_item'          => 'Редагування тендеру',
                'new_item'           => 'Новий тендер',
                'view_item'          => 'Дивитися тендер',
                'search_items'       => 'Шукати тендер',
                'not_found'          => 'Не знайдено',
                'not_found_in_trash' => 'Не знайдено у кошику',
                'menu_name'          => 'Тендери',
            ],
            'public'        => true,
            'menu_position' => 25,
            'menu_icon'     => 'dashicons-format-quote',
            'hierarchical'  => false,
            'support'       => array('title', 'editor', 'thumbnail'),
            'has_archive'   => true,
        ]);

        register_taxonomy('tendertype', array('tenders'), array(
            'labels' => array(
                'name'          => 'Категорії',
                'singular_name' => 'Категорія',
                'search_items'  => 'Знайти категорію',
                'all_items'     => 'Всі категорії',
                'view_item'     => 'Подивитися категорію',
                'edit_item'     => 'Редагувати категорію',
                'update_item'   => 'Оновити категорію',
                'add_new_item'  => 'Додати нову категорію',
                'new_item_name' => 'Нова категорія',
                'menu_name'     => 'Категорії',
            ),
            'description'  => '',
            'public'       => true,
            'hierarchical' => false
        ));
    }

    // Adding meta boxes for additional information of tenders
    public function tender_meta_box() {
        add_meta_box(
            'tender_meta_box',
            'Налаштування тенедеру',
            array($this, 'show_tender_metabox'),
            'tenders',
            'normal',
            'high'
        );
    }

    public function tender_meta_fields()
    {
        return array(
            array(
                'label' => 'Дата початку',
                'desc'  => 'Виберіть дату відкриття тендеру',
                'id'    => 'date_start',
                'type'  => 'datetime-local'
            ),
            array(
                'label' => 'Дата кінця',
                'desc'  => 'Виберіть дату закриття тендеру',
                'id'    => 'date_end',
                'type'  => 'datetime-local'
            ),
            array(
                'label' => 'Статус',
                'desc'  => '',
                'id'    => 'status',
                'type'  => 'select',
                'options' => array(
                    'one' => array(
                        'label' => 'Відкритий',
                        'value' => 'Відкритий'
                    ),
                    'two' => array(
                        'label' => 'Закритий',
                        'value' => 'Закритий'
                    ),
                )
            ),
            array(
                'label' => 'Початкова ставка',
                'desc'  => '',
                'id'    => 'start_rate',
                'type'  => 'number'
            ),
            array(
                'label' => 'Крок ставки',
                'desc'  => '',
                'id'    => 'step',
                'type'  => 'number'
            ),
            array(
                'desc'  => 'Ставки даного поста',
                'type'  => 'table',
            )
        );
    }

    private function fields_markup($fields, $rating)
    {
        global $post;

        echo '<table class="form-table">';
        foreach ($fields as $field) {
            $meta = get_post_meta($post->ID, $field['id'], true);

            echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';

            switch ($field['type']) {
                case 'select':
                    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                    };
                    echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                case 'number':
                case 'datetime-local':
                    $current_value = get_post_meta($_GET['post'], $field['id'], true);
                    echo '<input type="'.$field['type'].'" id="'.$field['id'].'" name="'.$field['id'].'" value="'. $current_value .'">
                <br/><span class="description">'.$field['desc'].'</span>';
                    break;
                case 'table': {
                    echo '<span class="description">' . $field['desc'] . '</span><table>';

                    foreach ($rating as $rate) {
                        echo '<tr><td>' . $rate['display_name'] . '</td><td>' . $rate['post_title'] . '</td><td>' . $rate['price'] . ' грн.</td><td>' . $rate['rate_time'] . '</td></tr>';
                    }
                    echo '</table>';
                }
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }

    public function show_tender_metabox() {
        $tender_meta_fields = $this->tender_meta_fields();

        global $wpdb;
        $table = $wpdb->prefix . 'rate';
        $current_post = $_GET['post'];

        $query = "SELECT 
            wp_users.display_name,
            wp_rate.price, 
            wp_rate.rate_time 
        FROM $table INNER JOIN wp_posts ON wp_rate.id_post = wp_posts.ID 
        INNER JOIN wp_users ON wp_rate.id_user = wp_users.ID WHERE wp_rate.id_post = $current_post;";
        $rating = $wpdb->get_results($query, ARRAY_A);

        echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

        $this->fields_markup($tender_meta_fields, $rating);
    }

    public function save_tender_meta_fields($post_id) {
        $tender_meta_fields = $this->tender_meta_fields();

        if(!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
            return $post_id;
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        if('tenders' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        foreach ($tender_meta_fields as $field) {
            $old = get_post_meta($post_id, $field['id'], true);

            $new = $_POST[$field['id']];
            if($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
                echo 1;
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
                echo 2;
            }
        }
    }

    private function get_minimal_rate($post_id)
    {
        global $wpdb;
        $start_rate = get_post_meta($post_id, 'start_rate', true);

        $table = $wpdb->prefix . 'rate';
        $query_string = "SELECT MIN(price) AS min_price FROM $table WHERE id_post = '%d'";
        $query_ready = $wpdb->prepare($query_string, $post_id);
        $min_rate = $wpdb->get_results($query_ready, ARRAY_A);
        $some = $min_rate[0]['min_price'];

        if(!$some){
            $some = $start_rate;
        }

        return $some;
    }

    public function test_ajax_rate() {
        global $wpdb;

        $start_rate = get_post_meta($_POST['id_post'], 'start_rate', true);
        $step = get_post_meta($_POST['id_post'], 'step', true);
        $status = get_post_meta($_POST['id_post'], 'status', true);

        $min_rate = $this->get_minimal_rate($_POST['id_post']);

        $table = $wpdb->prefix . 'rate';

        if($_POST['price'] < $start_rate && ($min_rate - $_POST['price'] >= $step) && $status != 'Закритий') {
            $query_string = "INSERT INTO $table (id_user, id_post, price) VALUES ('%d', '%d', '%f')";
            $query_ready = $wpdb->prepare($query_string, $_POST['id_user'], $_POST['id_post'], $_POST['price']);
            $result = $wpdb->query($query_ready);

            if ($result === false) {
                wp_die('DB ERROR');
            }
            $ajax_res = array(
                'success' => 'Ваша ставка прийнята',
                'err' => '123'
            );
        } elseif ($status == 'Закритий') {
            $ajax_res = array(
                'success' => "Вибачте, але тендер вже закритий",
                'err' => '140'
            );
        } else {
            $ajax_res = array(
                'success' => "Ваша ставка не відповідає умові",
                'err' => '123'
            );
        }
        echo json_encode($ajax_res);
        wp_die();
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

    // Closing the tenders and getting their ID's
    private function get_late_posts()
    {
        global $wpdb;

        $table_meta = $wpdb->prefix . 'postmeta';
        $close_query = "SELECT post_id 
              FROM $table_meta 
              WHERE meta_key = 'date_end' AND meta_value < CURRENT_TIMESTAMP 
              AND $table_meta.post_id IN (SELECT post_id 
                                          FROM $table_meta 
                                          WHERE meta_key = 'status' AND meta_value = 'Відкритий' )";
        return $wpdb->get_results($close_query, ARRAY_A);
    }

    // Check for winners in tenders, that was closed, in database
    private function find_the_winners($late_posts)
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

    // Sending email for winners
    private function send_mail_to_winners($winners)
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

    // This method close tenders and check for winners.
    // Next for winners method sends email with congratulations
    public function check_result_tenders() {
        $late_posts = $this->get_late_posts();

        foreach ($late_posts as $post) {
            update_post_meta($post['post_id'], 'status', 'Закритий');
        }

        $winners = $this->find_the_winners($late_posts);
        $this->send_mail_to_winners($winners);
    }

    public function minimal_rate($atts)
    {
        global $post;
        $min_rate = $this->get_minimal_rate($post->ID);
        return $min_rate;
    }
}

