<?php
$metadata = get_field('wpde_header_metadata', 'option');
if ($metadata) {
    $title = $metadata['title'];
    $description = $metadata['description'];
    $link = $metadata['link'];
    $link_2 = $metadata['link_2'];
?>

<header class="py-6 border-bottom">
    <div class="container py-6">
        <div class="row justify-content-center align-items-center text-center">
            <div class="col-md-8">
                <?php if (!empty($title)) { ?>
                    <h1 class="display-3 fw-bold mb-2"><?php echo esc_html($title); ?></h1>
                <?php } ?>
                <?php if (!empty($description)) { ?>
                    <p class="mb-5"><?php echo esc_html($description); ?></p>
                <?php } ?>

                <?php if (!empty($link) || !empty($link_2)) { ?>
                <div class="d-flex justify-content-center align-items-center">
                <?php
                if ($link) {
                    $link_url = $link['url'];
                    $link_title = $link['title'];
                    $link_target = $link['target'] ? $link['target'] : '_self';
                    ?>
                    <a class="btn btn-primary text-uppercase me-2" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                        <small><?php echo esc_html($link_title); ?></small>
                    </a>
                <?php } ?>

                <?php
                if ($link_2) {
                    $link_url = $link_2['url'];
                    $link_title = $link_2['title'];
                    $link_target = $link_2['target'] ? $link_2['target'] : '_self';
                ?>
                    <a class="btn border-0" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                        <?php echo esc_html($link_title); ?> &rarr;
                    </a>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</header>

<?php
}
