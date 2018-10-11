<?php
    get_header();
    wp_cron();

    function time_post_format($date){
        $new = new DateTime($date);
        return $new->format('H:i d.m.Y');
    }
?>
<div class="container col-md-8 col-md-offset-2 post-place">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post() ?>

            <h1><?php the_title(); ?></h1>

            <?php $tendertypes = get_the_terms($post->ID, 'tendertype'); ?>
            <strong>Вид: </strong>
            <?php foreach ($tendertypes as $type) {?>
                <a href="<?php echo get_term_link($type->term_id) ?>"><?php echo $type->name ?></a>
            <?php } ?>
            <p><?php the_content(); ?></p>
            <p><?php echo 'Дата початку: ' . time_post_format(get_post_meta($post->ID, 'date_start', true))?></p>
            <p><?php echo 'Дата закінчення: ' . time_post_format(get_post_meta($post->ID, 'date_end', true))?></p>
            <p><?php echo 'Статус: ' . get_post_meta($post->ID, 'status', true)?></p>
            <p><?php echo 'Стартова ставка: ' . get_post_meta($post->ID, 'start_rate', true)?></p>
            <p><?php echo 'Крок ставки: ' . get_post_meta($post->ID, 'step', true)?></p>
            <p><?php echo 'Найменша поточна ставка: ' . do_shortcode('[min_rate]') ?></p>
        <?php endwhile; ?>
        <?php
            $status = get_post_meta($post->ID, 'status', true);
            if($current_user->user_login != '' && $status !== 'Закритий'):?>
            <form>
                <div class="form-group">
                    <label for="rate">Ваша ставка</label>
                    <input type="hidden" name="id_post" value="<?php echo $post->ID; ?>">
                    <input type="hidden" name="id_user" value="<?php echo get_current_user_id(); ?>">
                    <input class="form-control col-md-2" type="number" maxlength="8" name="price" id="rate" placeholder="0000.00">
                </div>
                <button type="submit" id="btn-rate" class="btn btn-primary">Прийняти</button>
            </form>
        <?php elseif($status === 'Закритий'): ?>
            <p>Вибачте, але даний тендер вже закритий</p>
        <?php else: ?>
            <p>Для здійснення ставки, Вам потрібно увійти, або зареєструватися</p>
        <?php endif; ?>
        <?php ?>
    <?php else: ?>
        <p><?php __('На данний момент активних тендерів немає'); ?></p>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
