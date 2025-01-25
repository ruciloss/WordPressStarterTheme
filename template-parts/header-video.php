<?php
$header = get_field('wpde_header', 'option');
switch ($header) {
    case 'Video Container':
        $container_class = '';
        break;
    case 'Video Full':
    case '':
    default:
        $container_class = '-fluid';
        break;
}

$video = get_field('wpde_header_video', 'option');

$metadata = get_field('wpde_header_metadata', 'option');
if ($metadata) {
    $title = $metadata['title'];
    $description = $metadata['description'];
    $link = $metadata['link'];
    $link_2 = $metadata['link_2'];
}

if($metadata || $video) {
?>
<header class="overflow-hidden">
    <div class="container<?php echo $container_class; ?> position-relative px-0">
        <?php
        if( $video ) { ?>
            <video autoplay loop muted playsinline class="position-relative z-n1 w-100">
                <source src="<?php echo $video['url']; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php } ?>
        <div class="row justify-content-center align-items-center text-center position-absolute start-0 end-0 top-0 bottom-0">
            <div class="col-md-8">
                <?php if (!empty($title)) { ?>
                    <h1 class="display-3 fw-bold text-white mb-2"><?php echo esc_html($title); ?></h1>
                <?php } ?>
                <?php if (!empty($description)) { ?>
                    <p class="mb-5 text-white"><?php echo esc_html($description); ?></p>
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
                    <a class="btn text-white border-0" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
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
