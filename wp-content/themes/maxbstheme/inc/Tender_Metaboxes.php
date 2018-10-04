<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:20
 */

include_once('Tender_Rates_DB.php');

class Tender_Metaboxes
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'tender_meta_box'));
        add_action('save_post', array($this, 'save_tender_meta_fields'));
    }

    // Create form for metaboxes
    public function tender_meta_box() {
        add_meta_box(
            'tender_meta_box',
            __('Налаштування тенедеру', 'maxbstheme'),
            array($this, 'show_tender_metabox'),
            'tenders',
            'normal',
            'high'
        );
    }

    // Return an array for making metaboxes
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
                'desc'  => __('Ставки даного поста', 'maxbstheme'),
                'type'  => 'table',
            )
        );
    }

    // Markup for input "SELECT"
    private function select_markup($field)
    {
        global $post;
        $meta = get_post_meta($post->ID, $field['id'], true);
        printf('<select name="%s" id="%s">', $field['id'], $field['id']);
        foreach ($field['options'] as $option) {
            $select = '';
            if ($meta == $option['value']){
                $select = ' selected="selected"';
            } else {
                $select = '';
            }
            printf('<option %s value="%s" >%s</option>',
                $select,
                $option['value'],
                $option['label']
            );
        };
        printf('</select><br/><span class="description">%s</span>', $field['desc']);
    }

    // Markup for input "DATETIME-LOCAL" and "NUMBERS"
    private function datetime_and_number_markup($field)
    {
        $current_value = get_post_meta($_GET['post'], $field['id'], true);
        printf('<input type="%s" id="%s" name="%s" value="%s"><br/><span class="description">%s</span>',
            $field['type'],
            $field['id'],
            $field['id'],
            $current_value,
            $field['desc']
        );
    }

    // Markup for rates table
    private function table_markup($field)
    {
        global $post;
        $tender_rate_db = new Tender_Rates_DB();

        $rating = $tender_rate_db->get_rating($post->ID);

        printf('<span class="description">%s</span><table>', $field['desc']);
        foreach ($rating as $rate) {
            printf(
                '<tr><td>%s</td><td>%d грн.</td><td>%s</td></tr>',
                $rate['display_name'],
                $rate['price'],
                $rate['rate_time']
            );
        }
        echo '</table>';
    }

    // Rendering of fields
    private function fields_markup($field)
    {
        switch ($field['type']) {
            case 'select':
                $this->select_markup($field);
                break;
            case 'number':
            case 'datetime-local':
                $this->datetime_and_number_markup($field);
                break;
            case 'table':
                $this->table_markup($field);
        }
    }

    // Show fields in form
    private function show_fields($fields)
    {
        echo '<table class="form-table">';
        foreach ($fields as $field) {
            printf('<tr><th><label for="%s">%s</label></th><td>',
                $field['id'],
                $field['label']
            );
            $this->fields_markup($field);
            echo '</td></tr>';
        }
        echo '</table>';
    }

    public function show_tender_metabox() {
        $tender_meta_fields = $this->tender_meta_fields();

        echo '<input type="hidden" name="custom_meta_box_nonce" value="' .
            wp_create_nonce(basename(__FILE__)) .
            '" />';

        $this->show_fields($tender_meta_fields);
    }

    private function write_meta_to_db($fields, $post_id)
    {
        foreach ($fields as $field) {
            $old = get_post_meta($post_id, $field['id'], true);

            $new = $_POST[$field['id']];
            if($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }

    // Check values from metaboxes and write to database
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
        // Made function
        $this->write_meta_to_db($tender_meta_fields, $post_id);
    }
}