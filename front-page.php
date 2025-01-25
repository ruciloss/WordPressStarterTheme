<?php
/**
 * The template for displaying front page.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
*/
?>

<?php
get_header();

get_template_part('template-parts/section', 'icons');
get_template_part('template-parts/section', 'gallery');
get_template_part('template-parts/section', 'logos');
get_template_part('template-parts/section', 'posts');

get_footer();
