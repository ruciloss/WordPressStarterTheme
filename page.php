<?php
/**
 * The template for displaying single page.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-page
 */
?>

<?php get_header(); ?>
<div class="container-fluid px-0 py-6">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    get_template_part('template-parts/content', 'page');
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
