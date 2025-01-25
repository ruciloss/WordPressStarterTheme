<?php
$header = get_field('wpde_header', 'option');
switch ($header) {
    case 'Carousel Container':
        $container_class = '';
        break;
    case 'Carousel Full':
    case '':
    default:
        $container_class = '-fluid';
        break;
}
?>
<header>
    <div class="container<?php echo $container_class; ?> px-0 border-bottom">
        <?php if (have_rows('wpde_header_carousel', 'option')) { ?>
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <?php
            $slide_count = count(get_field('wpde_header_carousel', 'option'));
            if ($slide_count > 1) {
                echo '<div class="carousel-indicators">';
                $slide_index = 0;
                while (have_rows('wpde_header_carousel', 'option')) {
                    the_row(); ?>
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?php echo $slide_index; ?>" class="<?php echo $slide_index == 0 ? 'active' : ''; ?>" aria-current="<?php echo $slide_index == 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $slide_index + 1; ?>"></button>
                    <?php
                    $slide_index++;
                }
                echo '</div>';
            }
            ?>
            <div class="carousel-inner">
                <?php
                $slide_index = 0;
            while (have_rows('wpde_header_carousel', 'option')) {
                the_row();
                $image = get_sub_field('image');
                if($image) {
                    $image_size_custom = wp_is_mobile() ? 'thumb-rectangle' : 'large-lg';
                    $image_size = wp_get_attachment_image_url($image['ID'], $image_size_custom);
                    $alt = get_post_meta($image['ID'], '_wp_attachment_image_alt', true);
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
                    $link = get_sub_field('link');
                    if( $link ) {
                        $link_url = $link['url'];
                        $link_target = $link['target'] ? $link['target'] : '_self';
                    }
                    ?>
                        <div class="carousel-item <?php echo $slide_index == 0 ? 'active' : ''; ?>">
                            <a class="carousel-link" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
                                <img src="<?php echo esc_url($image_size); ?>" class="img-fluid" alt="<?php echo esc_attr($alt); ?>">
                            </a>
                            <div class="carousel-caption d-none d-md-block">
                                <h1 class="text-white"><?php echo esc_html($title); ?></h1>
                                <small class="text-white"><?php echo esc_html($description); ?></small>
                            </div>
                        </div>
                    <?php
                }
                $slide_index++;
            }
            ?>
            </div>
            <?php if ($slide_index > 1) { ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</header>
