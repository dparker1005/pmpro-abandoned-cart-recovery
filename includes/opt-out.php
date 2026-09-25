<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process opt-out requests.
 *
 * @since 0.1
 */
function pmproacr_process_opt_out() {
	global $wpdb;

	if ( ! isset( $_REQUEST['pmproacr_opt_out'] ) ) {
		return;
	}

	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	// We need to get the user ID from the email address.
	$user = get_user_by( 'email', sanitize_email( wp_unslash( $_REQUEST['pmproacr_opt_out'] ) ) );
	if ( ! $user ) {
		// Show a banner that the opt-out has failed.
		add_action( 'wp_footer', 'pmproacr_show_opt_out_failed_banner' );
		return;
	}

	// Update the user meta to opt out.
	update_user_meta( $user->ID, 'pmproacr_opt_out', 11 );

	// Mark all in-progress recovery attempts as lost.
	$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table.
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
 * @since 0.1
 */
function pmproacr_show_opt_out_banner() {
	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	$email = sanitize_email( wp_unslash( $_REQUEST['pmproacr_opt_out'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Display only; this callback is only hooked after pmproacr_process_opt_out() confirmed the value is set.

	// Show the banner.
	?>
	<div class="pmproacr-opt-out-banner pmproacr-opt-out-banner-success">
		<p><?php echo esc_html( sprintf(
			/* translators: %s is the email address */
			__( 'You have successfully opted out of abandoned cart recovery emails for the email address %s.', 'pmpro-abandoned-cart-recovery' ),
			$email
		) ); ?></p>
	</div>
	<?php
}

/**
 * Show a banner that the opt-out has failed.
 *
 * @since 0.1
 */
function pmproacr_show_opt_out_failed_banner() {
	// $_REQUEST['pmproacr_opt_out'] is the email address to opt out.
	$email = sanitize_email( wp_unslash( $_REQUEST['pmproacr_opt_out'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Display only; this callback is only hooked after pmproacr_process_opt_out() confirmed the value is set.

	// Show the banner.
	?>
	<div class="pmproacr-opt-out-banner pmproacr-opt-out-banner-failed">
		<p><?php echo esc_html( sprintf(
			/* translators: %s is the email address */
			__( 'There was an error processing your opt-out request. The email address %s is not a user on this site.', 'pmpro-abandoned-cart-recovery' ),
			$email
		) ); ?></p>
	<?php
}