<div class="container tender-card">
    <div class="card">
        <h5 class="card-header"><?php the_title(); ?></h5>
        <div class="card-body">
            <?php $tendertypes = get_the_terms($post->ID, 'tendertype'); ?>
            <p><strong><?php _e('Вид', 'maxbstheme'); ?> :</strong>
            <?php foreach ($tendertypes as $type): ?>
                <?php echo $type->name ?></p>
            <?php endforeach; ?>
            <p class="card-text"><?php the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                <?php _e('Детальніше', 'maxbstheme'); ?>
            </a>
        </div>
    </div>
</div>
