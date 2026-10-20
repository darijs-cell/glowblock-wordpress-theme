<?php
/**
 * GlowBlock theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GLOWBLOCK_VERSION', '1.0.0');

/* -------------------------------------------------------------------------
 * Theme setup & assets
 * ---------------------------------------------------------------------- */

function glowblock_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => 'Primary menu',
        'footer'  => 'Footer menu',
    ]);
}
add_action('after_setup_theme', 'glowblock_setup');

function glowblock_assets(): void
{
    wp_enqueue_style(
        'glowblock-font',
        'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap',
        [],
        null
    );
    wp_enqueue_style('glowblock-style', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_script(
        'glowblock-main',
        get_theme_file_uri('assets/main.js'),
        [],
        filemtime(get_theme_file_path('assets/main.js')),
        true
    );
    wp_localize_script('glowblock-main', 'GlowBlock', ['palette' => glowblock_palette()]);
}
add_action('wp_enqueue_scripts', 'glowblock_assets');

/* -------------------------------------------------------------------------
 * Lamp data
 * ---------------------------------------------------------------------- */

function glowblock_palette(): array
{
    return [
        'pink'   => ['label' => 'Bubblegum', 'hex' => '#ff6fae'],
        'amber'  => ['label' => 'Amber',     'hex' => '#ffb347'],
        'lime'   => ['label' => 'Lime',      'hex' => '#7bed6a'],
        'cyan'   => ['label' => 'Ice',       'hex' => '#4fd8ff'],
        'violet' => ['label' => 'Violet',    'hex' => '#a07bff'],
    ];
}

function glowblock_styles(): array
{
    return [
        'cat'   => 'Cat',
        'fox'   => 'Fox',
        'bunny' => 'Bunny',
        'slime' => 'Slime',
        'bot'   => 'Robot',
        'dino'  => 'Dino',
    ];
}

function glowblock_lamp_data(int $post_id): array
{
    $palette = glowblock_palette();

    $color = get_post_meta($post_id, '_lamp_color', true);
    if (!isset($palette[$color])) {
        $color = 'pink';
    }

    $style = get_post_meta($post_id, '_lamp_style', true);
    if (!isset(glowblock_styles()[$style])) {
        $style = 'cat';
    }

    $price = get_post_meta($post_id, '_lamp_price', true);
    $size  = get_post_meta($post_id, '_lamp_size', true);

    return [
        'color' => $color,
        'hex'   => $palette[$color]['hex'],
        'style' => $style,
        'price' => $price !== '' ? (float) $price : 29.99,
        'size'  => $size !== '' ? (int) $size : 21,
    ];
}

function glowblock_price(float $price): string
{
    return '$' . number_format($price, 2);
}

/* -------------------------------------------------------------------------
 * Custom post type "Lamp" + taxonomy "Collection"
 * ---------------------------------------------------------------------- */

function glowblock_register_content_types(): void
{
    register_post_type('lamp', [
        'labels' => [
            'name'          => 'Lamps',
            'singular_name' => 'Lamp',
            'add_new_item'  => 'Add new lamp',
            'edit_item'     => 'Edit lamp',
            'all_items'     => 'All lamps',
        ],
        'public'       => true,
        'has_archive'  => 'lamps',
        'rewrite'      => ['slug' => 'lamp'],
        'menu_icon'    => 'dashicons-lightbulb',
        'menu_position' => 5,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);

    register_taxonomy('collection', 'lamp', [
        'labels' => [
            'name'          => 'Collections',
            'singular_name' => 'Collection',
        ],
        'hierarchical' => true,
        'public'       => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'collection'],
    ]);
}
add_action('init', 'glowblock_register_content_types');

/* Meta box ----------------------------------------------------------------- */

function glowblock_add_meta_box(): void
{
    add_meta_box('glowblock_lamp_details', 'Lamp details', 'glowblock_meta_box_html', 'lamp', 'side', 'high');
}
add_action('add_meta_boxes', 'glowblock_add_meta_box');

function glowblock_meta_box_html(WP_Post $post): void
{
    $data = glowblock_lamp_data($post->ID);
    wp_nonce_field('glowblock_save_lamp', 'glowblock_lamp_nonce');
    ?>
    <p>
        <label for="lamp_price"><strong>Price ($)</strong></label><br>
        <input type="number" step="0.01" min="0" id="lamp_price" name="lamp_price" value="<?php echo esc_attr($data['price']); ?>" style="width:100%">
    </p>
    <p>
        <label for="lamp_style"><strong>Character</strong></label><br>
        <select id="lamp_style" name="lamp_style" style="width:100%">
            <?php foreach (glowblock_styles() as $key => $label) { ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($data['style'], $key); ?>><?php echo esc_html($label); ?></option>
            <?php } ?>
        </select>
    </p>
    <p>
        <label for="lamp_color"><strong>Default glow colour</strong></label><br>
        <select id="lamp_color" name="lamp_color" style="width:100%">
            <?php foreach (glowblock_palette() as $key => $c) { ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($data['color'], $key); ?>><?php echo esc_html($c['label']); ?></option>
            <?php } ?>
        </select>
    </p>
    <p>
        <label for="lamp_size"><strong>Length (cm)</strong></label><br>
        <input type="number" min="5" max="80" id="lamp_size" name="lamp_size" value="<?php echo esc_attr($data['size']); ?>" style="width:100%">
    </p>
    <?php
}

function glowblock_save_lamp(int $post_id): void
{
    if (!isset($_POST['glowblock_lamp_nonce']) || !wp_verify_nonce($_POST['glowblock_lamp_nonce'], 'glowblock_save_lamp')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $palette = glowblock_palette();
    $styles  = glowblock_styles();

    $color = sanitize_key($_POST['lamp_color'] ?? 'pink');
    $style = sanitize_key($_POST['lamp_style'] ?? 'cat');

    update_post_meta($post_id, '_lamp_price', max(0, (float) ($_POST['lamp_price'] ?? 29.99)));
    update_post_meta($post_id, '_lamp_size', min(80, max(5, (int) ($_POST['lamp_size'] ?? 21))));
    update_post_meta($post_id, '_lamp_color', isset($palette[$color]) ? $color : 'pink');
    update_post_meta($post_id, '_lamp_style', isset($styles[$style]) ? $style : 'cat');
}
add_action('save_post_lamp', 'glowblock_save_lamp');

/* -------------------------------------------------------------------------
 * 3D lamp renderer (pure CSS cuboids, no images needed)
 * ---------------------------------------------------------------------- */

function glowblock_part(float $w, float $h, float $d, float $x, float $y, float $z, string $class = ''): array
{
    return compact('w', 'h', 'd', 'x', 'y', 'z', 'class');
}

function glowblock_lamp_parts(string $style): array
{
    $bw = 120; $bh = 104; $bd = 120;
    if ($style === 'slime') { $bw = 140; $bh = 110; $bd = 140; }
    if ($style === 'bot')   { $bw = 124; $bh = 100; $bd = 110; }

    $fz = $bd / 2 + 1.5;          // eyes & mouth sit just in front of the face
    $top = -$bh / 2;
    $parts = [glowblock_part($bw, $bh, $bd, 0, 0, 0, 'body')];

    // Feet
    $feetY = $bh / 2 + 9;
    $parts[] = glowblock_part(34, 18, 44, -$bw * 0.28, $feetY, 14, 'shade');
    $parts[] = glowblock_part(34, 18, 44,  $bw * 0.28, $feetY, 14, 'shade');

    switch ($style) {
        case 'fox':
            $parts[] = glowblock_part(26, 42, 18, -38, $top - 21, -8);
            $parts[] = glowblock_part(26, 42, 18,  38, $top - 21, -8);
            $parts[] = glowblock_part(52, 30, 34, 0, 16, $bd / 2 + 17, 'shade');
            $parts[] = glowblock_part(12, 10, 3, 0, 8, $bd / 2 + 34.5, 'ink');
            $parts[] = glowblock_part(28, 28, 70, 0, 22, -$bd / 2 - 35);
            $parts[] = glowblock_part(14, 22, 3, -26, -6, $fz, 'ink');
            $parts[] = glowblock_part(14, 22, 3,  26, -6, $fz, 'ink');
            break;

        case 'bunny':
            $parts[] = glowblock_part(22, 74, 14, -30, $top - 37, -6);
            $parts[] = glowblock_part(22, 74, 14,  30, $top - 37, -6);
            $parts[] = glowblock_part(14, 22, 3, -26, -4, $fz, 'ink');
            $parts[] = glowblock_part(14, 22, 3,  26, -4, $fz, 'ink');
            $parts[] = glowblock_part(20, 6, 3, 0, 24, $fz, 'ink');
            break;

        case 'slime':
            $parts[] = glowblock_part(50, 22, 50, 8, $top - 11, 0);
            $parts[] = glowblock_part(16, 22, 3, -32, -6, $fz, 'ink');
            $parts[] = glowblock_part(16, 22, 3,  32, -6, $fz, 'ink');
            $parts[] = glowblock_part(36, 8, 3, 0, 26, $fz, 'ink');
            break;

        case 'bot':
            $parts[] = glowblock_part(8, 28, 8, 0, $top - 14, 0, 'shade');
            $parts[] = glowblock_part(20, 20, 20, 0, $top - 38, 0);
            $parts[] = glowblock_part(18, 44, 44, -$bw / 2 - 9, 0, 0, 'shade');
            $parts[] = glowblock_part(18, 44, 44,  $bw / 2 + 9, 0, 0, 'shade');
            $parts[] = glowblock_part(22, 16, 3, -28, -8, $fz, 'ink');
            $parts[] = glowblock_part(22, 16, 3,  28, -8, $fz, 'ink');
            $parts[] = glowblock_part(40, 6, 3, 0, 24, $fz, 'ink');
            break;

        case 'dino':
            foreach ([-36, 0, 36] as $zz) {
                $parts[] = glowblock_part(16, 26, 22, 0, $top - 13, $zz, 'shade');
            }
            $parts[] = glowblock_part(30, 30, 60, 0, 26, -$bd / 2 - 30);
            $parts[] = glowblock_part(56, 34, 30, 0, 16, $bd / 2 + 15);
            $parts[] = glowblock_part(14, 22, 3, -26, -10, $fz, 'ink');
            $parts[] = glowblock_part(14, 22, 3,  26, -10, $fz, 'ink');
            break;

        case 'cat':
        default:
            $parts[] = glowblock_part(30, 28, 22, -36, $top - 14, -10);
            $parts[] = glowblock_part(30, 28, 22,  36, $top - 14, -10);
            $parts[] = glowblock_part(14, 22, 3, -26, -4, $fz, 'ink');
            $parts[] = glowblock_part(14, 22, 3,  26, -4, $fz, 'ink');
            $parts[] = glowblock_part(20, 6, 3, 0, 24, $fz, 'ink');
            break;
    }

    return $parts;
}

function glowblock_lamp(string $style, string $hex, string $size = 'md'): string
{
    $sizes = ['sm', 'md', 'lg'];
    $size  = in_array($size, $sizes, true) ? $size : 'md';

    ob_start();
    ?>
    <div class="lamp lamp--<?php echo esc_attr($size); ?>" data-lamp data-base="<?php echo esc_attr($hex); ?>" data-current="<?php echo esc_attr($hex); ?>" style="--glow: <?php echo esc_attr($hex); ?>;" aria-hidden="true">
        <div class="lamp__floor"></div>
        <div class="lamp__stage">
            <?php foreach (glowblock_lamp_parts($style) as $p) { ?>
                <div class="cuboid <?php echo esc_attr($p['class']); ?>" style="--w:<?php echo round($p['w'], 1); ?>;--h:<?php echo round($p['h'], 1); ?>;--d:<?php echo round($p['d'], 1); ?>;--x:<?php echo round($p['x'], 1); ?>;--y:<?php echo round($p['y'], 1); ?>;--z:<?php echo round($p['z'], 1); ?>">
                    <i class="f"></i><i class="b"></i><i class="r"></i><i class="l"></i><i class="t"></i><i class="u"></i>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/** Swatch + mode controls for a lamp scope. */
function glowblock_lamp_controls(string $current_color): void
{
    ?>
    <div class="lamp-ui">
        <div class="swatches" role="group" aria-label="Glow colour">
            <?php foreach (glowblock_palette() as $key => $c) { ?>
                <button type="button" class="swatch<?php echo $key === $current_color ? ' is-active' : ''; ?>" data-color="<?php echo esc_attr($c['hex']); ?>" style="--c: <?php echo esc_attr($c['hex']); ?>" aria-label="<?php echo esc_attr($c['label']); ?>" title="<?php echo esc_attr($c['label']); ?>"></button>
            <?php } ?>
        </div>
        <div class="modes" role="group" aria-label="Light mode">
            <button type="button" class="mode is-active" data-mode="select">Colour select</button>
            <button type="button" class="mode" data-mode="phase">Colour phasing</button>
        </div>
    </div>
    <?php
}

/* -------------------------------------------------------------------------
 * Navigation fallback
 * ---------------------------------------------------------------------- */

function glowblock_fallback_menu(): void
{
    $home = home_url('/');
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url(get_post_type_archive_link('lamp')) . '">Shop</a></li>';
    echo '<li><a href="' . esc_url($home . '#modes') . '">Light modes</a></li>';
    echo '<li><a href="' . esc_url($home . '#reviews') . '">Reviews</a></li>';
    echo '<li><a href="' . esc_url($home . '#contact') . '">Contact</a></li>';
    echo '</ul>';
}

/* -------------------------------------------------------------------------
 * Demo content (created once, so the theme looks complete after activation)
 * ---------------------------------------------------------------------- */

function glowblock_seed_demo_content(): void
{
    if (get_option('glowblock_seeded')) {
        return;
    }
    if ((int) wp_count_posts('lamp')->publish > 0) {
        update_option('glowblock_seeded', 1);
        return;
    }

    $lamps = [
        ['Blocky Cat Light',       'cat',   'pink',   29.99, 21, 'Critters', 'Our best seller: a cuddly cube cat that glows in five colours. Perfect for bedside tables, desks and shelves.'],
        ['Fox Cube Lamp',          'fox',   'amber',  32.99, 22, 'Critters', 'A cheeky blocky fox with a long tail and a warm amber glow. Switch to phasing mode for a sunset that never ends.'],
        ['Bunny Block Light',      'bunny', 'violet', 29.99, 24, 'Critters', 'Tall ears, tiny feet, soft violet light. The calmest night light in the gang.'],
        ['Slime Cube Lamp',        'slime', 'lime',   24.99, 18, 'Blocks',   'A squishy-looking cube that lights up the room in bright lime. Cool to the touch and totally mess-free.'],
        ['Bot Buddy Lamp',         'bot',   'cyan',   34.99, 22, 'Gadgets',  'A retro robot with an antenna and an icy cyan glow. Great for gamers and tinkerers.'],
        ['Dino Block Light',       'dino',  'lime',   31.99, 25, 'Critters', 'Spikes, a stubby tail and a big friendly grin. A roaring good time in the dark.'],
        ['Mini Slime Night Light', 'slime', 'pink',   17.99, 12, 'Blocks',   'Pocket-sized and plug-free. Takes a bubblegum glow anywhere you go.'],
        ['Pocket Bot',             'bot',   'violet', 22.99, 14, 'Gadgets',  'The little brother of Bot Buddy. Same personality, smaller footprint, violet vibes.'],
    ];

    foreach ($lamps as [$title, $style, $color, $price, $size, $collection, $text]) {
        $id = wp_insert_post([
            'post_type'    => 'lamp',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_excerpt' => $text,
            'post_content' => "<p>{$text}</p><p>Five glow colours, two light modes (colour select and colour phasing), USB-C powered, cool-touch and kid-safe. Each GlowBlock lamp is about {$size} cm long.</p>",
        ]);
        if (is_wp_error($id) || !$id) {
            continue;
        }
        update_post_meta($id, '_lamp_style', $style);
        update_post_meta($id, '_lamp_color', $color);
        update_post_meta($id, '_lamp_price', $price);
        update_post_meta($id, '_lamp_size', $size);
        wp_set_object_terms($id, $collection, 'collection');
    }

    update_option('glowblock_seeded', 1);
    flush_rewrite_rules(false);
}
add_action('init', 'glowblock_seed_demo_content', 20);

function glowblock_flush_on_switch(): void
{
    glowblock_register_content_types();
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'glowblock_flush_on_switch');
