<?php
$options = get_field('wpde_options', 'option');
if ($options) {
    $preloader = $options['preloader'];
}

$logo = get_field('wpde_logo', 'option');
if ($logo) {
    $logo_image = $logo['image'];
    $logo_width = $logo['width'] ? $logo['width'] : 256;
    $logo_text = $logo['text'];
}
?>

<?php if (!empty($preloader)) { ?>
    <div id="preloader" class="vh-100 w-100 position-absolute start-0 top-0 bg-body z-3 d-flex align-items-center justify-content-center">
        <?php if (!empty($logo_image)) { ?>
                <img class="img-fluid blink" src="<?php echo esc_url($logo_image['url']); ?>" alt="Logo" width="<?php echo esc_attr($logo_width); ?>" height="auto"/>
            <?php
            } elseif(!empty($logo_text)) {
                echo esc_html($logo_text);
            } else {
                echo esc_html(get_bloginfo('name'));
            }
        ?>
    </div>
<?php } ?>

