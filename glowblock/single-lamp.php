<?php get_header(); ?>

<?php
while (have_posts()) {
    the_post();
    $data  = glowblock_lamp_data(get_the_ID());
    $terms = get_the_terms(get_the_ID(), 'collection');
    ?>
    <section class="section product" data-lamp-scope>
        <div class="container product-grid">
            <div class="product-stage card">
                <?php echo glowblock_lamp($data['style'], $data['hex'], 'lg'); ?>
                <?php glowblock_lamp_controls($data['color']); ?>
            </div>

            <div class="product-info">
                <nav class="crumbs" aria-label="Breadcrumb">
                    <a href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>">Shop</a>
                    <?php if ($terms && !is_wp_error($terms)) { ?>
                        <span>/</span>
                        <a href="<?php echo esc_url(get_term_link($terms[0])); ?>"><?php echo esc_html($terms[0]->name); ?></a>
                    <?php } ?>
                </nav>

                <h1><?php the_title(); ?></h1>
                <p class="product-price"><?php echo esc_html(glowblock_price($data['price'])); ?></p>

                <div class="entry"><?php the_content(); ?></div>

                <button type="button" class="btn btn-large" data-add-to-cart data-name="<?php echo esc_attr(get_the_title()); ?>">Add to cart</button>

                <dl class="specs">
                    <div><dt>Glow colours</dt><dd>5</dd></div>
                    <div><dt>Light modes</dt><dd>Colour select, colour phasing</dd></div>
                    <div><dt>Length</dt><dd><?php echo (int) $data['size']; ?> cm</dd></div>
                    <div><dt>Power</dt><dd>USB-C (cable included)</dd></div>
                </dl>
            </div>
        </div>
    </section>

    <?php
    $related_args = [
        'post_type'      => 'lamp',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'rand',
    ];
    if ($terms && !is_wp_error($terms)) {
        $related_args['tax_query'] = [['taxonomy' => 'collection', 'field' => 'term_id', 'terms' => $terms[0]->term_id]];
    }
    $related = new WP_Query($related_args);
    if ($related->have_posts()) { ?>
        <section class="section">
            <div class="container">
                <div class="section-head"><h2>You might also like</h2></div>
                <div class="lamp-grid">
                    <?php
                    while ($related->have_posts()) {
                        $related->the_post();
                        get_template_part('template-parts/lamp-card');
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </section>
    <?php } ?>
<?php } ?>

<?php get_footer(); ?>
