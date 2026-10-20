<?php get_header(); ?>

<!-- HERO -->
<section class="hero" data-lamp-scope>
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Night lights, levelled up</span>
            <h1>Light up your room,<br><span class="grad">one block at a time.</span></h1>
            <p class="lead">Cute blocky lamps that glow in five colours and switch between two light modes. Cool to the touch, USB-C powered and impossible not to smile at.</p>

            <div class="hero-actions">
                <a class="btn" href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>">Shop the gang</a>
                <a class="btn btn-ghost" href="#modes">See the light modes</a>
            </div>

            <ul class="hero-facts">
                <li><strong>5</strong><span>glow colours</span></li>
                <li><strong>2</strong><span>light modes</span></li>
                <li><strong>USB-C</strong><span>powered</span></li>
            </ul>
        </div>

        <div class="hero-stage">
            <?php echo glowblock_lamp('cat', glowblock_palette()['pink']['hex'], 'lg'); ?>
            <?php glowblock_lamp_controls('pink'); ?>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section">
    <div class="container features">
        <article class="card feature">
            <span class="feature-icon">🌈</span>
            <h3>5 glow colours</h3>
            <p>Bubblegum, amber, lime, ice and violet. Pick the mood, change it any time.</p>
        </article>
        <article class="card feature">
            <span class="feature-icon">✨</span>
            <h3>2 light modes</h3>
            <p>Hold one colour or let the lamp drift softly through the whole palette.</p>
        </article>
        <article class="card feature">
            <span class="feature-icon">🔌</span>
            <h3>USB-C powered</h3>
            <p>Plug into any charger or power bank. A cable is included in the box.</p>
        </article>
        <article class="card feature">
            <span class="feature-icon">🧸</span>
            <h3>Cool &amp; kid-safe</h3>
            <p>LED lighting stays cool to the touch, with a sturdy, rounded block build.</p>
        </article>
    </div>
</section>

<!-- SHOP -->
<section class="section" id="shop">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">The collection</span>
            <h2>Meet the glow gang</h2>
        </div>

        <div class="lamp-grid">
            <?php
            $lamps = new WP_Query(['post_type' => 'lamp', 'posts_per_page' => 6, 'orderby' => 'date', 'order' => 'ASC']);
            while ($lamps->have_posts()) {
                $lamps->the_post();
                get_template_part('template-parts/lamp-card');
            }
            wp_reset_postdata();
            ?>
        </div>

        <p class="center"><a class="btn btn-ghost" href="<?php echo esc_url(get_post_type_archive_link('lamp')); ?>">View all lamps</a></p>
    </div>
</section>

<!-- LIGHT MODES -->
<section class="section" id="modes" data-lamp-scope>
    <div class="container modes-grid">
        <div class="modes-stage card">
            <?php echo glowblock_lamp('fox', glowblock_palette()['amber']['hex'], 'md'); ?>
        </div>

        <div>
            <span class="eyebrow">Light modes</span>
            <h2>Two ways to glow</h2>
            <p class="lead">Try it right here. Tap a colour for a steady glow, or switch to phasing and watch the lamp drift through the rainbow.</p>

            <?php glowblock_lamp_controls('amber'); ?>

            <ul class="mode-list">
                <li><b>Colour select.</b> Choose your favourite of five colours and keep it.</li>
                <li><b>Colour phasing.</b> The light fades from one colour to the next, slow and calm.</li>
            </ul>
        </div>
    </div>
</section>

<!-- COLLECTIONS -->
<?php
$collections = get_terms(['taxonomy' => 'collection', 'hide_empty' => true]);
if (!is_wp_error($collections) && $collections) { ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Browse by type</span>
            <h2>Pick a collection</h2>
        </div>
        <div class="collections">
            <?php
            $i = 0;
            $hexes = array_column(glowblock_palette(), 'hex');
            foreach ($collections as $term) { ?>
                <a class="card collection" href="<?php echo esc_url(get_term_link($term)); ?>" style="--c: <?php echo esc_attr($hexes[$i % count($hexes)]); ?>">
                    <span class="collection-dot"></span>
                    <h3><?php echo esc_html($term->name); ?></h3>
                    <p class="muted"><?php echo (int) $term->count; ?> lamps</p>
                </a>
            <?php $i++; } ?>
        </div>
    </div>
</section>
<?php } ?>

<!-- REVIEWS -->
<section class="section" id="reviews">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Happy glowers</span>
            <h2>What people say</h2>
        </div>
        <div class="reviews">
            <figure class="card review">
                <div class="stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>My daughter refuses to sleep without the bunny now. The violet glow is so soft.</blockquote>
                <figcaption>Elina K.</figcaption>
            </figure>
            <figure class="card review">
                <div class="stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>Phasing mode is hypnotic. It looks amazing on my desk next to the monitor.</blockquote>
                <figcaption>Marcis T.</figcaption>
            </figure>
            <figure class="card review">
                <div class="stars" aria-label="5 out of 5">★★★★★</div>
                <blockquote>Bought the robot as a gift and ended up ordering another one for myself.</blockquote>
                <figcaption>Sofia R.</figcaption>
            </figure>
        </div>
    </div>
</section>

<!-- CONTACT / NEWSLETTER -->
<section class="section" id="contact">
    <div class="container">
        <div class="card cta">
            <h2>Join the glow club</h2>
            <p class="lead">New characters, colours and offers. No spam, only good vibes. (Demo form: nothing is sent.)</p>
            <form class="signup" data-demo-form novalidate>
                <input type="email" placeholder="you@example.com" aria-label="Email address" required>
                <button class="btn" type="submit">Subscribe</button>
            </form>
            <p class="form-msg" role="status"></p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
