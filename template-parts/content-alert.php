<?php
$alert = get_field('wpde_alert', 'option');
if ($alert) {
    $text = $alert['text'];
    $link = $alert['link'];
	if( $link ) {
		$link_url = $link['url'];
		$link_title = $link['title'];
		$link_target = $link['target'] ? $link['target'] : '_self';
	}
}

if(!empty($alert)) {
?>

<div id="alert" class="position-fixed bottom-0 start-0 end-0 mb-3 mx-auto d-flex align-items-center bg-blur rounded-3 border shadow-lg d-none" style="width: max-content;">
	<small class="px-3"><?php echo $text; ?></small>  <a class="btn btn-primary text-uppercase rounded-start-0 py-3" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><small><?php echo esc_html( $link_title ); ?></small></a>
</div>

<?php } ?>