<?php get_header(); ?>
<div class="container col-md-8 col-md-offset-2 post-place">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) :
              the_post();
              get_template_part('inc/tender', 'info');
        endwhile; ?>
        <?php
            $status = get_post_meta($post->ID, 'status', true);
            if($current_user->user_login != '' && $status !== 'close'):?>
            <?php get_template_part('inc/tender', 'rate'); ?>
        <?php elseif($status === 'close'): ?>
            <p><?php _e('Вибачте, але даний тендер вже закритий', 'maxbstheme'); ?></p>
        <?php else: ?>
            <p><?php _e('Для здійснення ставки, Вам потрібно увійти, або зареєструватися', 'maxbstheme'); ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p><?php _e('На данний момент активних тендерів немає', 'maxbstheme'); ?></p>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
