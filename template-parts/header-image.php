<?php
$metadata = get_field('wpde_header_metadata', 'option');
if ($metadata) {
    $title = $metadata['title'];
    $description = $metadata['description'];
    $link = $metadata['link'];
    $link_2 = $metadata['link_2'];
?>

<header class="border-bottom py-6">
    <div class="container py-md-6">
        <div class="row align-items-center justify-content-center flex-column-reverse flex-md-row">
            <div class="col-md-4">
                <?php if (!empty($subtitle)) { ?>
                    <small class="text-primary"><strong><?php echo esc_html($subtitle); ?></strong></small>
                <?php } ?>
                <?php if (!empty($title)) { ?>
                    <h1 class="display-5 fw-bold mb-2"><?php echo esc_html($title); ?></h1>
                <?php } ?>
                <?php if (!empty($description)) { ?>
                    <p class="mb-5"><?php echo esc_html($description); ?></p>
                <?php } ?>

                <?php if (!empty($link) || !empty($link_2)) { ?>
                    <div class="d-flex align-items-center">
                        <?php
                        if ($link) {
                            $link_url = $link['url'];
                            $link_title = $link['title'];
                            $link_target = $link['target'] ? $link['target'] : '_self';
                            ?>
                            <a class="btn btn-primary me-2" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                        <?php } ?>

                        <?php
                        if ($link_2) {
                            $link_url = $link_2['url'];
                            $link_title = $link_2['title'];
                            $link_target = $link_2['target'] ? $link_2['target'] : '_self';
                            ?>
                            <a class="btn border-0" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <div class="col-md-4 mb-5 mb-md-0">
            <?php
            $image = get_field('wpde_header_image', 'option');
            if($image) {
                $image_size_custom = wp_is_mobile() ? 'thumb-rectangle' : 'medium-md';
                $image_size = wp_get_attachment_image_url($image['ID'], $image_size_custom);
                $alt = get_post_meta($image['ID'], '_wp_attachment_image_alt', true);
            ?>
                <img src="<?php echo esc_url($image_size); ?>" class="img-fluid" alt="<?php echo esc_attr($alt); ?>">
            <?php } ?>
            </div>
        </div>
    </div>
</header>

<?php
}
