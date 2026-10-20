<?php get_header(); ?>

<?php while (have_posts()) { the_post(); ?>
    <section class="page-head">
        <div class="container narrow">
            <span class="eyebrow"><?php echo esc_html(get_the_date()); ?></span>
            <h1><?php the_title(); ?></h1>
        </div>
    </section>

    <section class="section">
        <div class="container narrow">
            <article <?php post_class('card post'); ?>>
                <div class="entry"><?php the_content(); ?></div>
            </article>
            <?php comments_template(); ?>
        </div>
    </section>
<?php } ?>

<?php get_footer(); ?>
