<?php get_header(); ?>

<section class="showcase text-center">
    <div class="container-fluid">
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php bloginfo('description'); ?></p>
    </div>
</section>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post() ?>
        <div class="text-center">
            <h1><?php the_title(); ?></h1>
            <p><?php the_content(); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p><?php __('На данний момент активних тендерів немає'); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
