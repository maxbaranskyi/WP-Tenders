<form>
    <div class="form-group">
        <label for="rate"><?php _e('Ваша ставка', 'maxbstheme'); ?></label>
        <input type="hidden" name="id_post" value="<?php echo $post->ID; ?>">
        <input type="hidden" name="id_user" value="<?php echo get_current_user_id(); ?>">
        <input class="form-control col-md-2" type="number" maxlength="8" name="price" id="rate" placeholder="0000.00">
    </div>
    <button type="submit" id="btn-rate" class="btn btn-primary"><?php _e('Прийняти', 'maxbstheme'); ?></button>
</form>