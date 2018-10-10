<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php bloginfo('name'); ?> <?php is_front_page() ? bloginfo('description') : wp_title(); ?></title>
        <link href="<?php bloginfo('template_url')?>/css/bootstrap.css" rel="stylesheet">
        <link href="<?php bloginfo('stylesheet_url')?>" rel="stylesheet">
        <?php wp_head(); ?>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container">
                <a class="navbar-brand" href="<?php echo get_home_url(); ?>"><?php bloginfo('name'); ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <?php wp_nav_menu(array(
                    'menu' => 'primary',
                    'theme_location' => 'primary',
                    'depth' => 2,
                    'container' => 'div',
                    'container_class' => 'сollapse navbar-collapse',
                    'menu_class' => 'nav navbar-nav',
                    'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
                    'walker' => new wp_bootstrap_navwalker()
                ));?>

                <?php
                    global $current_user;
                    get_currentuserinfo();
                    if ($current_user->user_login == '') {
                        echo '<a class="btn btn-light rigth-elements" href="' .  wp_registration_url() . '">' . __('Реєстрація', 'maxbstheme') . '</a>';
                        echo '<a class="btn btn-primary rigth-elements" href="' . wp_login_url(get_permalink()). '">' . __('Увійти', 'maxbstheme') . '</a>';
                    } else {
                        echo '<span class="navbar-text rigth-elements">' . __('Вітаю, ', 'maxbstheme') . $current_user->user_login . '</span>';
                        echo '<a class="btn btn-info rigth-elements" href="' . wp_logout_url() .'">' . __('Вийти', 'maxbstheme') . '</a>';
                    }
                ?>
                </div>
            </nav>
        </header>
        <main role="main" id="main">

