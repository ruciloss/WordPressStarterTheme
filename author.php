<?php
/**
 * The template for displaying author archive pages.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#author-display
 */
?>

<?php get_header(); ?>

<?php
$user_id = get_query_var('author');
$user_info = get_userdata($user_id);

if ($user_info) {
    $first_name = !empty($user_info->first_name) ? $user_info->first_name : '';
    $last_name = !empty($user_info->last_name) ? $user_info->last_name : '';
    $description = !empty($user_info->description) ? $user_info->description : '';
    
    if (empty($first_name) && empty($last_name)) {
        $title = esc_html($user_info->display_name);
    } else {
        $title = esc_html(trim($first_name . ' ' . $last_name));
    }
}
?>
<div class="container-fluid px-0 py-6">
    <div class="container">
        <?php
        WPDE()->the_title($title, __('Author', 'wpde'), $description);
        get_template_part('template-parts/content', 'breadcrumbs');
        ?>
        <div class="row mt-6 gy-4">
            <?php if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    $grid = get_field('wpde_grid', 'option');
                    if ($grid) {
                        $grid_size = intval($grid['author']);
                        $card = $grid['author_card'];
                    } else {
                        $grid_size = 3;
                        $card = 'post';
                    }
                    echo '<div class="col-md-' . esc_attr($grid_size) . '">';
                        get_template_part('template-parts/content', $card);
                    echo '</div>';
                }
                get_template_part('template-parts/content', 'pagination');
                wp_reset_postdata();
            } else {
                $html = '<div class="col-lg-12">';
                    $html .= '<p class="text-danger mb-0">' . esc_html(__('Sorry, no data of the author was found.', 'wpde')) . '</p>';
                $html .= '</div>';

                echo $html;
            } ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
