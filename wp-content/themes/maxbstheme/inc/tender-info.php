<div class="row">
    <div class="col-md-6" id="content">
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </div>
    <div class="col-md-6" id="info">
        <?php $tendertypes = get_the_terms($post->ID, 'tendertype'); ?>
        <strong><?php _e('Вид', 'maxbstheme'); ?> :</strong>
        <?php foreach ($tendertypes as $type): ?>
            <a href="<?php echo get_term_link($type->term_id) ?>"><?php echo $type->name ?></a>
        <?php endforeach; ?>
        <p><?php echo __('Дата початку: ', 'maxbstheme') . do_shortcode('[date_start]')  ?></p>
        <p><?php echo __('Дата закінчення: ', 'maxbstheme') . do_shortcode('[date_end]')  ?></p>
        <p><?php echo __('Стартова ставка: ', 'maxbstheme') . get_post_meta($post->ID, 'start_rate', true)  ?></p>
        <p><?php echo __('Крок ставки: ', 'maxbstheme') . get_post_meta($post->ID, 'step', true)  ?></p>
        <p><?php echo __('Найменша поточна ставка: ', 'maxbstheme') . do_shortcode('[min_rate]')  ?></p>
    </div>
</div>

