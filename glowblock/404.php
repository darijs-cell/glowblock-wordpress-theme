<?php get_header(); ?>

<section class="section">
    <div class="container narrow center">
        <div class="four-oh-four">
            <?php echo glowblock_lamp('slime', glowblock_palette()['violet']['hex'], 'md'); ?>
        </div>
        <h1>404: the lights are out</h1>
        <p class="lead">We couldn't find that page. Let's get you back to the glow.</p>
        <a class="btn" href="<?php echo esc_url(home_url('/')); ?>">Back to home</a>
    </div>
</section>

<?php get_footer(); ?>
