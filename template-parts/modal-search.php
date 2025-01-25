<?php
$options = get_field('wpde_options', 'option');
if ($options) {
    $search_form = $options['search_form'];
}

if (empty($search_form)) {
?>
<div class="modal bg-blur fade" id="modal-searchform" tabindex="-1" aria-labelledby="modal-searchform-label" aria-hidden="true">
	<div class="modal-dialog mt-6">
		<div class="modal-content border shadow-lg">
			<div class="modal-body p-0">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
