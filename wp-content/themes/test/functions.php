<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.09.18
 * Time: 14:12
 */

require_once('inc/wp_bootstrap_navwalker.php');
require_once('inc/tenders.php');

add_action('after_setup_theme', 'test_theme_setup'); //Do not replace
add_action('show_admin_bar', '__return_false'); //Do not replace
//AJAX
add_action('wp_enqueue_scripts', 'ajax_connect'); //Do not replace
add_action('wp_ajax_nopriv_ajax_pagination', 'my_ajax_pagination' ); //Do not replace
add_action('wp_ajax_ajax_pagination', 'my_ajax_pagination' ); //Do not replace
//Taking away h1-tag of pagination
add_filter('navigation_markup_template', 'test_pagination', 10, 2 ); //Do not replace


function test_theme_setup() {
    add_theme_support('post-thumbnails');

    //Navigation
    register_nav_menus(array(
        'primary' => __('Primary Menu')
    ));
}

function ajax_connect()
{
    global $wp_query;
    wp_enqueue_script('tender-ajax-request', 'https://code.jquery.com/jquery-3.3.1.min.js');
    wp_enqueue_script('tender-ajax-request', get_template_directory_uri() . '/js/testajax.js');
    wp_enqueue_script('ajax-pagination', get_template_directory_uri() . '/js/ajaxpagination.js', array( 'jquery' ), '1.0', true );
    wp_localize_script('tender-ajax-request', 'TendAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'query_vars' => json_encode( $wp_query->query )
    ));
}

// PAGINATION AJAX
function my_ajax_pagination() {
    $query_vars = json_decode( stripslashes( $_POST['query_vars'] ), true );

    $query_vars['paged'] = $_POST['page'];


    $posts = new WP_Query( $query_vars );
    $GLOBALS['wp_query'] = $posts;

    add_filter( 'editor_max_image_size', 'my_image_size_override' );

    if( ! $posts->have_posts() ) {
        get_template_part( 'content', 'none' );
    }
    else {
        while ( $posts->have_posts()) {
            $posts->the_post();
            get_template_part( 'tenders', get_post_format() );
            echo '<div class="container tender-card"><div class="card"><h5 class="card-header">';
            the_title();
            echo '</h5><div class="card-body">';
            $tendertypes = get_the_terms($posts->post->ID, 'tendertype');
            echo '<strong>Вид: </strong>';
            foreach ($tendertypes as $type) {
                echo '<a href="';
                echo get_term_link($type->term_id) .'">';
                echo $type->name . '</a>';
            }
            echo '<p class="card-text">';
            the_excerpt();
            echo '</p><a href="';
            the_permalink();
            echo '" class="btn btn-primary">Детальніше</a></div></div></div>';
        }
    }

    the_posts_pagination(array(
        'end_size' => 2,
    ));

    wp_die();
}

function test_pagination($template, $class) {
    return '<nav class="navigation %1$s" role="navigation">
		<div class="nav-links text-center">%3$s</div>
	</nav>';
}


$tenders = new Tenders;