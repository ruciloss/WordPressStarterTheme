<?php
/**
 * Used any time that get_search_form() is called.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 */
?>

<form action="<?php echo home_url('/'); ?>" role="search" autocomplete="off" class="d-flex justify-content-between align-items-center"> 
    <input class="form-control w-75 py-3 border-0 shadow-none" id="search" name="s" type="text" placeholder="<?php _e('Find anything...', 'wpde'); ?>" aria-label="<?php _e('Find anything...', 'wpde'); ?>" value="<?php echo get_search_query(); ?>">
    <button class="btn flex-1" type="submit">
        <kbd><small>CTRL K</small></kbd>
    </button>
</form>
