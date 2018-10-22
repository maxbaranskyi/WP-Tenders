<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.10.18
 * Time: 14:22
 */

class ThemeSettings
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'maxbstheme_setup'));
        add_action('login_redirect', array($this, 'user_login_redirect'));
        add_action('show_admin_bar', ('__return_false'));
        //AJAX
        add_action('wp_enqueue_scripts', array($this, 'script_and_style_connect'));
        add_action('wp_ajax_nopriv_ajax_pagination', array($this, 'maxbstheme_ajax_pagination'));
        add_action('wp_ajax_ajax_pagination', array($this, 'maxbstheme_ajax_pagination'));
        //Taking away h1-tag of pagination
        add_filter('navigation_markup_template', array($this, 'markup_pagination'), 10, 2);
        //Template
        add_action('wp_footer', array($this, 'content_template'));
    }

    // Setting menu
    public function maxbstheme_setup()
    {
        add_theme_support('post-thumbnails');

        //Navigation
        register_nav_menus(array(
            'primary' => __('Primary Menu')
        ));
    }

    // After log in redirect to 'Tenders' page
    public function user_login_redirect()
    {
        return '/tenders/';
    }

    public function script_and_style_connect()
    {
        global $wp_query;
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-util');
        wp_enqueue_style('tender-bootstrap-styles', get_template_directory_uri() . '/css/bootstrap.css');
        wp_enqueue_style('maxbstheme-main', get_stylesheet_uri());
        wp_enqueue_script('tender-bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js');
        wp_enqueue_script('tender-ajax-request', get_template_directory_uri() . '/js/mainajax.js', array('jquery', 'wp-util'));

        wp_localize_script('tender-ajax-request', 'TendAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'query_vars' => json_encode($wp_query->query)
        ));
    }

    // Show other posts by click in pagination buttons
    public function maxbstheme_ajax_pagination()
    {
        $query_vars = json_decode(stripslashes($_POST['query_vars']), true);
        $query_vars['paged'] = $_POST['page'];

        $posts = ( new WP_Query($query_vars) )->posts;

        foreach($posts as &$post) {
            $post->permalink = get_the_permalink($post->ID);
            $post->temrs = get_the_terms($post->ID, 'tendertype');
        }

        $GLOBALS['wp_query'] = $posts;
        $response = [
            'success' => true,
            'data' => $posts
        ];
        wp_send_json($response);
    }

    /**
     * Return pagination without H1 tag
     *
     * @param $template
     * @param $class
     *
     * @return string
     */
    public function markup_pagination($template, $class)
    {
        $pagination = '<nav class="navigation %1$s" role="navigation">
		                    <div class="nav-links text-center">%3$s</div>
	                   </nav>';
        return $pagination;
    }

    public function content_template()
    {
        require_once(get_template_directory() . '/js-templates/content-templates.php');
    }
}

