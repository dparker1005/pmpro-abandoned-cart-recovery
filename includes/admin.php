<?php

/**
 * Add the Abandoned Cart Recovery settings page.
 *
 * @since TBD
 */
function pmproacr_admin_add_page() {
	add_submenu_page(
		'pmpro-dashboard',
		__( 'Abandoned Cart Recovery', 'pmpro-abandoned-cart-recovery' ),
		__( 'Abandoned Cart Recovery', 'pmpro-abandoned-cart-recovery' ),
		'manage_options',
		'pmproacr',
		'pmproacr_admin_page'
	);
}
add_action( 'admin_menu', 'pmproacr_admin_add_page' );

/**
 * Display the Abandoned Cart Recovery settings page.
 *
 * @since TBD
 */
function pmproacr_admin_page() {
	$recovery_attempts_list_table = new PMProACR_Recovery_Attempts_List_Table();
	$recovery_attempts_list_table->prepare_items();

	require_once PMPRO_DIR . '/adminpages/admin_header.php';

	?>
	<hr class="wp-header-end">
	<h1><?php esc_html_e( 'Abandoned Cart Recovery', 'pmpro-abandoned-cart-recovery' ); ?></h1>
	<?php
	$recovery_attempts_list_table->display();
	require_once PMPRO_DIR . '/adminpages/admin_footer.php';
}