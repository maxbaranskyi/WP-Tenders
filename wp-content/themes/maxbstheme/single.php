<?php get_header(); ?>
<div class="container col-md-8 col-md-offset-2 post-place">
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post() ?>

        <h1><?php the_title(); ?></h1>
        <p><?php the_content(); ?></p>

    <?php endwhile; ?>
<?php else: ?>
    <p><?php _e('На данний момент активних тендерів немає', 'maxbstheme'); ?></p>
<?php endif; ?>
</div>
<?php get_footer(); ?>
