<?php get_header(); ?>

<section class="showcase text-center">
    <div class="container-fluid">
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php bloginfo('description'); ?></p>
    </div>
</section>

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post() ?>

    <div class="container">
        <div class="card">
            <h5 class="card-header"><?php the_title(); ?></h5>
            <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text"><?php the_excerpt(); ?></p>
                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Детальніше</a>
            </div>
        </div>
    </div>

    <?php endwhile; ?>
<?php else: ?>
    <p><?php __('На данний момент активних тендерів немає'); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
