<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:19
 */

class Tender_Posts
{
    public function __construct()
    {
        add_action('init', array($this, 'tender_post_types'));
        add_action('init', array($this, 'tender_taxonomy'));
    }

    // Making a custom type of posts
    public function tender_post_types()
    {
        register_post_type('tenders', [
            'labels' => [
                'name'               => __('Тендери', 'maxbstheme'),
                'singular_name'      => __('Тендер', 'maxbstheme'),
                'add_new'            => __('Створити тендер', 'maxbstheme'),
                'add_new_item'       => __('Створення тендеру', 'maxbstheme'),
                'edit_item'          => __('Редагування тендеру', 'maxbstheme'),
                'new_item'           => __('Новий тендер', 'maxbstheme'),
                'view_item'          => __('Дивитися тендер', 'maxbstheme'),
                'search_items'       => __('Шукати тендер', 'maxbstheme'),
                'not_found'          => __('Не знайдено', 'maxbstheme'),
                'not_found_in_trash' => __('Не знайдено у кошику', 'maxbstheme'),
                'menu_name'          => __('Тендери', 'maxbstheme'),
            ],
            'public'        => true,
            'menu_position' => 25,
            'menu_icon'     => 'dashicons-format-quote',
            'hierarchical'  => false,
            'support'       => array('title', 'editor', 'thumbnail'),
            'has_archive'   => true,
        ]);

    }

    // Making a taxonomy for custom type of posts
    public function tender_taxonomy()
    {
        register_taxonomy('tendertype', array('tenders'), array(
            'labels' => array(
                'name'          => __('Категорії', 'maxbstheme'),
                'singular_name' => __('Категорія', 'maxbstheme'),
                'search_items'  => __('Знайти категорію', 'maxbstheme'),
                'all_items'     => __('Всі категорії', 'maxbstheme'),
                'view_item'     => __('Подивитися категорію', 'maxbstheme'),
                'edit_item'     => __('Редагувати категорію', 'maxbstheme'),
                'update_item'   => __('Оновити категорію', 'maxbstheme'),
                'add_new_item'  => __('Додати нову категорію', 'maxbstheme'),
                'new_item_name' => __('Нова категорія', 'maxbstheme'),
                'menu_name'     => __('Категорії', 'maxbstheme'),
            ),
            'description'  => '',
            'public'       => true,
            'hierarchical' => false
        ));
    }
}