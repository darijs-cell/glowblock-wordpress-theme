<?php get_header(); ?>

<section class="page-head">
    <div class="container">
        <span class="eyebrow"><?php echo is_search() ? 'Search' : 'Journal'; ?></span>
        <h1><?php echo is_search() ? 'Results for "' . esc_html(get_search_query()) . '"' : 'Latest news'; ?></h1>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <?php if (have_posts()) {
            while (have_posts()) {
                the_post(); ?>
                <article <?php post_class('card post'); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="muted"><?php echo esc_html(get_the_date()); ?></p>
                    <div class="entry"><?php the_excerpt(); ?></div>
                </article>
            <?php }
            the_posts_pagination();
        } else { ?>
            <p class="center muted">Nothing found.</p>
        <?php } ?>
    </div>
</section>

<?php get_footer(); ?>
