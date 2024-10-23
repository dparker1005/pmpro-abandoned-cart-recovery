<?php

/**
 * Process opt-out requests.
 *
 * @since TBD
 */
function pmproacr_process_opt_out() {
	global $wpdb;

	if ( ! isset( $_REQUEST['pmproacr_opt_out'] ) ) {
		return;
	}

	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	// We need to get the user ID from the email address.
	$user = get_user_by( 'email', sanitize_email( $_REQUEST['pmproacr_opt_out'] ) );
	if ( ! $user ) {
		// Show a banner that the opt-out has failed.
		add_action( 'wp_footer', 'pmproacr_show_opt_out_failed_banner' );
		return;
	}

	// Update the user meta to opt out.
	update_user_meta( $user->ID, 'pmproacr_opt_out', 11 );

	// Mark all in-progress recovery attempts as lost.
	$wpdb->update(
		$wpdb->pmproacr_recovery_attempts,
		array( 'status' => 'lost' ),
		array( 'user_id' => $user->ID, 'status' => 'in_progress' )
	);

	// Show a banner confirming the opt-out request.
	add_action( 'wp_footer', 'pmproacr_show_opt_out_banner', 11 );
}
add_action( 'wp', 'pmproacr_process_opt_out' );

/**
 * Show a banner confirming the opt-out request.
 *
 * @since TBD
 */
function pmproacr_show_opt_out_banner() {
	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	$email = sanitize_email( $_REQUEST['pmproacr_opt_out'] );

	// Show the banner.
	?>
	<div class="pmproacr-opt-out-banner pmproacr-opt-out-banner-success">
		<p><?php echo esc_html( sprintf( __( 'You have successfully opted out of abandoned cart recovery emails for the email address %s.', 'pmpro-abandoned-cart-recovery' ), $email ) ); ?></p>
	</div>
	<?php
}

/**
 * Show a banner that the opt-out has failed.
 *
 * @since TBD
 */
function pmproacr_show_opt_out_failed_banner() {
	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	$email = sanitize_email( $_REQUEST['pmproacr_opt_out'] );

	// Show the banner.
	?>
	<div class="pmproacr-opt-out-banner pmproacr-opt-out-banner-failed">
		<p><?php echo esc_html( sprintf( __( 'There was an error processing your opt-out request. The email address %s is not a user on this site.', 'pmpro-abandoned-cart-recovery' ), $email ) ); ?></p>
	<?php
}