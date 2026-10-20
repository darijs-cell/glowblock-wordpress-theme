<?php
get_header();

$current = is_tax('collection') ? get_queried_object() : null;
$terms   = get_terms(['taxonomy' => 'collection', 'hide_empty' => true]);
?>

<section class="page-head">
    <div class="container">
        <span class="eyebrow">Shop</span>
        <h1><?php echo $current ? esc_html($current->name) : 'All lamps'; ?></h1>
        <p class="lead">Five glow colours, two light modes and a whole lot of personality.</p>

        <?php if (!is_wp_error($terms) && $terms) { ?>
            <div class="pills">
                <a class="pill<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>">All</a>
                <?php foreach ($terms as $term) { ?>
                    <a class="pill<?php echo ($current && $current->term_id === $term->term_id) ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (have_posts()) { ?>
            <div class="lamp-grid">
                <?php
                while (have_posts()) {
                    the_post();
                    get_template_part('template-parts/lamp-card');
                }
                ?>
            </div>

            <div class="pagination"><?php the_posts_pagination(['mid_size' => 1]); ?></div>
        <?php } else { ?>
            <p class="center muted">No lamps here yet.</p>
        <?php } ?>
    </div>
</section>

<?php get_footer(); ?>
