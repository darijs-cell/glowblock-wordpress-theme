<?php
$data  = glowblock_lamp_data(get_the_ID());
$terms = get_the_terms(get_the_ID(), 'collection');
?>
<article <?php post_class('card lamp-card'); ?>>
    <a class="lamp-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
        <div class="lamp-card__stage">
            <?php echo glowblock_lamp($data['style'], $data['hex'], 'sm'); ?>
        </div>
    </a>

    <div class="lamp-card__body">
        <?php if ($terms && !is_wp_error($terms)) { ?>
            <span class="tag"><?php echo esc_html($terms[0]->name); ?></span>
        <?php } ?>

        <h3 class="lamp-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

        <div class="lamp-card__meta">
            <span class="price"><?php echo esc_html(glowblock_price($data['price'])); ?></span>
            <button type="button" class="btn btn-small" data-add-to-cart data-name="<?php echo esc_attr(get_the_title()); ?>">Add to cart</button>
        </div>
    </div>
</article>
