<?php get_header(); ?>

    <section class="showcase text-center">
        <div class="container-fluid">
            <h1><?php bloginfo('name'); ?></h1>
            <p><?php bloginfo('description'); ?></p>
            <a class="btn btn-primary btn-lg">Далі</a>
        </div>
    </section>
    <div class="container-fluid" id="term-link">
        <div class="wrapper text-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <?php
                $taxon = get_terms();
                foreach ($taxon as $tax) {
                    if ($tax->taxonomy == 'tendertype') { ?>
                        <a href="<?php echo get_term_link($tax->term_id); ?>" class="btn btn-secondary"><?php echo $tax->name; ?></a>
                    <?php }
                };
                ?>
            </div>
        </div>
    </div>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post() ?>

        <div class="container">
            <div class="card">
                <h5 class="card-header"><?php the_title(); ?></h5>
                <div class="card-body">
                    <?php $tendertypes = get_the_terms($post->ID, 'tendertype'); ?>
                    <strong>Вид: </strong>
                    <?php foreach ($tendertypes as $type) {?>
                        <a href="<?php echo get_term_link($type->term_id) ?>"><?php echo $type->name ?></a>
                    <?php } ?>
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