<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:22
 */

class Theme_Settings
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'maxbstheme_setup')); //Do not replace
        add_action('show_admin_bar', ('__return_false')); //Do not replace
        //AJAX
        add_action('wp_enqueue_scripts', array($this, 'ajax_connect')); //Do not replace
        add_action('wp_ajax_nopriv_ajax_pagination', array($this, 'my_ajax_pagination')); //Do not replace
        add_action('wp_ajax_ajax_pagination', array($this, 'my_ajax_pagination')); //Do not replace
        //Taking away h1-tag of pagination
        add_filter('navigation_markup_template', array($this, 'markup_pagination'), 10, 2 ); //Do not replace
    }

    // Setting menu
    public function maxbstheme_setup() {
        add_theme_support('post-thumbnails');

        //Navigation
        register_nav_menus(array(
            'primary' => __('Primary Menu')
        ));
    }

    public function ajax_connect()
    {
        global $wp_query;
        wp_enqueue_script('tender-ajax-request', 'https://code.jquery.com/jquery-3.3.1.min.js');
        wp_localize_script('tender-ajax-request', 'TendAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'query_vars' => json_encode( $wp_query->query )
        ));
    }

    // Show other posts by click in pagination buttons
    public function my_ajax_pagination() {
        $query_vars = json_decode( stripslashes( $_POST['query_vars'] ), true );
        $query_vars['paged'] = $_POST['page'];

        $posts = new WP_Query( $query_vars );
        $GLOBALS['wp_query'] = $posts;

        if( ! $posts->have_posts() ) {
            get_template_part( 'content', 'none' );
        } else {
            while ( $posts->have_posts()) {
                $posts->the_post();
                get_template_part('tenders', get_post_format() );
                get_template_part('content');
            }
        }

        the_posts_pagination(array(
            'end_size' => 2,
        ));

        wp_die();
    }

    // Return pagination without H1 tag
    public function markup_pagination($template, $class) {
        return '<nav class="navigation %1$s" role="navigation">
		<div class="nav-links text-center">%3$s</div>
	</nav>';
    }
}
