<?php
/**
 * The template for displaying the header.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/reference/functions/get_header/
 */
?>
<!DOCTYPE html>
<html id="<?php echo esc_attr(WPDE()->_token); ?>" <?php language_attributes(); ?> data-bs-theme="light" class="cc--lightmode">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php get_template_part('template-parts/content', 'preloader'); ?>

<?php
if (!is_404()) {
    get_template_part('template-parts/navbar', 'main');
}

if (is_front_page()) {
    $header = get_field('wpde_header', 'option');

    switch ($header) {
        case 'Simple':
            get_template_part('template-parts/header', 'simple');
            break;
        case 'Reviews':
            get_template_part('template-parts/header', 'reviews');
            break;
        case 'Image':
            get_template_part('template-parts/header', 'image');
            break;
        case 'Carousel Full':
        case 'Carousel Container':
            get_template_part('template-parts/header', 'carousel');
            break;
        case 'Video Full':
        case 'Video Container':
            get_template_part('template-parts/header', 'video');
            break;
        case '':
        default:
            get_template_part('template-parts/header', 'simple');
            break;
    }
}
?>

<main id="app">
