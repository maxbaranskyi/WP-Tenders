<?php get_header(); ?>

    <section class="showcase text-center">
        <div class="container">
            <h1><?php bloginfo('name'); ?></h1>
            <p><?php bloginfo('description'); ?></p>
        </div>
    </section>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post() ?>
        <?php get_template_part('content') ?>
    <?php endwhile; ?>
        <?php
            the_posts_pagination(array(
                'end_size' => 2,
            ));
        ?>
<?php else: ?>
    <p><?php _e('На данний момент активних тендерів немає', 'maxbstheme'); ?></p>
<?php endif; ?>
<?php get_footer(); ?>