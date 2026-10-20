</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
                <span class="brand-cube" aria-hidden="true"></span>
                <span class="brand-text">Glow<b>Block</b></span>
            </a>
            <p class="muted">Cute blocky night lights in five colours and two light modes.</p>
        </div>

        <div>
            <h4>Shop</h4>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>">All lamps</a></li>
                <?php
                $terms = get_terms(['taxonomy' => 'collection', 'hide_empty' => true]);
                if (!is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        echo '<li><a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>

        <div>
            <h4>Info</h4>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(home_url('/#modes')); ?>">Light modes</a></li>
                <li><a href="<?php echo esc_url(home_url('/#reviews')); ?>">Reviews</a></li>
                <li><a href="<?php echo esc_url(home_url('/#contact')); ?>">Contact</a></li>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <span>&copy; <?php echo esc_html(date('Y')); ?> GlowBlock. Concept store, no real products are sold.</span>
        <span>Custom WordPress theme by Darijs Sivoha</span>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
