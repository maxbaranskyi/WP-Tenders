<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:20
 */

class TenderMetaboxes
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'tender_meta_box'));
        add_action('save_post_tenders', array($this, 'save_tender_meta_fields'), 10, 2);
    }

    // Create form for metaboxes
    public function tender_meta_box()
    {
        add_meta_box(
            'tender_meta_box',
            __('Налаштування тенедеру', 'maxbstheme'),
            array($this, 'show_tender_metabox'),
            'tenders',
            'normal',
            'high'
        );
    }


    /**
     * Return an array for making metaboxes
     *
     * @return array
     */
    public function tender_meta_fields()
    {
        return array(
            array(
                'label' => __('Дата початку', 'maxbstheme'),
                'desc'  => __('Виберіть дату відкриття тендеру', 'maxbstheme'),
                'id'    => 'date_start',
                'type'  => 'datetime-local'
            ),
            array(
                'label' => __('Дата кінця'),
                'desc'  => __('Виберіть дату закриття тендеру', 'maxbstheme'),
                'id'    => 'date_end',
                'type'  => 'datetime-local'
            ),
            array(
                'label' => __('Статус', 'maxbstheme'),
                'desc'  => '',
                'id'    => 'status',
                'type'  => 'select',
                'options' => array(
                    'one' => array(
                        'label' => __('Відкритий', 'maxbstheme'),
                        'value' => 'open'
                    ),
                    'two' => array(
                        'label' => __('Закритий', 'maxbstheme'),
                        'value' => 'close'
                    ),
                )
            ),
            array(
                'label' => __('Початкова ставка', 'maxbstheme'),
                'desc'  => '',
                'id'    => 'start_rate',
                'type'  => 'number'
            ),
            array(
                'label' => __('Крок ставки', 'maxbstheme'),
                'desc'  => '',
                'id'    => 'step',
                'type'  => 'number'
            ),
            array(
                'type'  => 'table',
                'id'    => 'rate_table',
                'label' => __('Ставки даного поста', 'maxbstheme')
            )
        );
    }


    /**
     * Markup for input "SELECT"
     *
     * @param array $field
     */
    private function selectMarkup($field)
    {
        global $post;
        $meta = get_post_meta($post->ID, $field['id'], true);
        $result = '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
        foreach ($field['options'] as $option) {
            $select = '';
            if ($meta === $option['value']){
                $select = ' selected="selected"';
            }
            $result .= '<option ' . $select .
                ' value="' . $option['value'] . '" >'  . $option['label'] . '</option>';
        }
        $result .= '</select><br/><span class="description">' . $field['desc'] . '</span>';

        echo $result;
    }


    /**
     * Markup for input "DATETIME-LOCAL" and "NUMBERS"
     *
     * @param $field
     */
    private function dateTimeAndNumberMarkup($field)
    {
        global $post;
        $current_value = get_post_meta($post->ID, $field['id'], true);

        if ($field['id'] === 'date_start' || $field['id'] === 'date_end') {
            $date = new DateTime($current_value);
            $current_value = $date->format('Y-m-d\TH:i');
        }
        printf('<input type="%s" id="%s" name="%s" value="%s"><br/><span class="description">%s</span>',
            $field['type'],
            $field['id'],
            $field['id'],
            $current_value,
            $field['desc']
        );
    }

    // Markup for rates table
    private function tableMarkup()
    {
        global $post;
        $tender_rate_db = new tenderRatesDB();
        $rating = $tender_rate_db->getRating($post->ID);

        $result = '<table>';
        foreach ($rating as $rate) {
            $result .= '<tr><td>' . $rate['display_name'] .
                ' </td><td>' . $rate['price'] .
                ' грн.</td><td>' . $rate['rate_time'] . '</td></tr>';
        }

        echo $result . '</table>';
    }

    // Rendering of fields
    private function fieldsMarkup($field)
    {
        switch ($field['type']) {
            case 'select':
                $this->selectMarkup($field);
                break;
            case 'number':
            case 'datetime-local':
                $this->dateTimeAndNumberMarkup($field);
                break;
            case 'table':
                $this->tableMarkup();
        }
    }

    // Show fields in form
    private function showFields($fields)
    {
        echo '<table class="form-table">';
        foreach ($fields as $field) {
            printf('<tr><th><label for="%s">%s</label></th><td>',
                $field['id'],
                $field['label']
            );
            $this->fieldsMarkup($field);
            echo '</td></tr>';
        }
        echo '</table>';
    }

    // Show metabox and create nonce
    public function show_tender_metabox() {
        $tender_meta_fields = $this->tender_meta_fields();

        echo '<input type="hidden" name="custom_meta_box_nonce" value="' .
            wp_create_nonce(basename(__FILE__)) .
            '" />';

        $this->showFields($tender_meta_fields);
    }


    /**
     * Take values form $_POST, filter this values for using in other methods
     *
     * @return $result array
     */
    private function clearArray() {
        $result = [];

        $result['start_rate'] = filter_var($_POST['start_rate'], FILTER_SANITIZE_NUMBER_INT);
        $result['step'] = filter_var($_POST['step'], FILTER_SANITIZE_NUMBER_INT);

        $status = $_POST['status'];
        if ($status === 'open' || $status === 'close') {
            $result['status'] = $status;
        }

        try {
            $check_start_date = new DateTime($_POST['date_start']);
            $result['date_start'] = $check_start_date->format('Y-m-d H:i');
            $check_end_date = new DateTime($_POST['date_end']);
            $result['date_end'] = $check_end_date->format('Y-m-d H:i');

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $result;
    }

    /**
     * Write values to database's table for metadata
     *
     * @param array $fields fields
     * @param string $post_id post_id
     */
    private function writeMetaToDB($fields, $post_id)
    {
        $meta_values = $this->clearArray();

        foreach ($fields as $field) {
            $field_id = $field['id'];
            $old = get_post_meta($post_id, $field_id, true);

            if ($field_id === 'rate_table') {
                continue;
            }

            $new = $meta_values[$field_id];

            if ($new && $new !== $old) {
                update_post_meta($post_id, $field_id, $new);
            } elseif ('' === $new && $old) {
                delete_post_meta($post_id, $field_id, $old);
            }
        }
    }

    /**
     * Check values from metaboxes and write to database
     *
     * @param $post_id
     * @return mixed
     */
    public function save_tender_meta_fields($post_id, $post) {
        if ($post->post_status === 'auto-draft') {
            return ;
        }

        if (isset($_GET['action']) && in_array($_GET['action'], ['trash', 'untrash'])) {
            return;
        }

        $tender_meta_fields = $this->tender_meta_fields();

        if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // Made function
        $this->writeMetaToDB($tender_meta_fields, $post_id);
    }
}

