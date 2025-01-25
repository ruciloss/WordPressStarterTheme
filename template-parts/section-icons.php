<?php if (have_rows('wpde_icons_items', 'option')) { ?>
    <div class="container-fluid px-0 py-6">
        <div class="container">
            <?php
            $group = get_field('wpde_icons', 'option');
            if ($group) {
                $title = $group['title'];
                $subtitle = $group['subtitle'];
                $description = $group['description'];

                WPDE()->the_title($title, $subtitle, $description, ['class' => 'mb-6']);
            }
            ?>
            <div class="row g-3">
                <?php
                while (have_rows('wpde_icons_items', 'option')) {
                    the_row();
                    $item_title = get_sub_field('title');
                    $item_description = get_sub_field('description');
                    $image = get_sub_field('image');
                    if ($image) {
                        $alt = get_post_meta($image['ID'], '_wp_attachment_image_alt', true);
                    }
                    ?>
                    <div class="col-6 col-md-3 col-lg-3">
                        <?php if (!empty($image)) { ?>
                            <img src="<?php echo esc_url($image['url']); ?>" class="img-fluid mb-3" alt="<?php echo esc_attr($alt); ?>" width="48px" height="auto">
                        <?php } ?>
                        <h6 class="mb-0"><?php echo esc_html($item_title); ?></h6>
                        <p class="mb-0"><?php echo esc_html($item_description); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
