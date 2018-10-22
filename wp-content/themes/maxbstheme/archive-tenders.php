<?php get_header(); ?>
    <section class="showcase text-center">
        <div class="container">
            <h1><?php bloginfo('name'); ?></h1>
            <p><?php bloginfo('description'); ?></p>
        </div>
    </section>
    <div id="content">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post() ?>
            <?php get_template_part('content') ?>
        <?php endwhile; ?>
    <?php else: ?>
        <p><?php _e('На данний момент активних тендерів немає', 'maxbstheme'); ?></p>
    <?php endif; ?>
    </div>
    <nav id="pagination">
    <?php
        the_posts_pagination(array(
            'end_size' => 2,
            'prev_next' => false
        ))
    ?>
    </nav>
<?php get_footer(); ?>
<?php wp_footer(); ?>
