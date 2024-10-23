<?php
/**
 * Add the reminder email templates.
 *
 * @since TBD
 *
 * @param $templates array The email templates.
 * @return array The email templates.
 */
function pmproacr_email_templates( $templates ) {
	$templates['pmproacr_reminder_1'] = array(
		'description' => esc_html__( 'Abandoned Cart Recovery - Reminder 1', 'pmpro-abandoned-cart-recovery' ),
		'subject'     => esc_html__( 'Your Membership Is Almost Ready!', 'pmpro-abandoned-cart-recovery' ),
		'body'        => '<p>' . esc_html__( 'You have almost finished purchasing your !!membership_level_name!! membership at !!sitename!!.', 'pmpro-abandoned-cart-recovery' ) . '</p><p><a href="!!checkout_url!!">' . esc_html__( 'Complete Your Purchase', 'pmpro-abandoned-cart-recovery' ) . '</a></p><p><a href="!!opt_out_url!!">' . esc_html__( 'Opt Out of These Emails', 'pmpro-abandoned-cart-recovery' ) . '</a></p>',
		'help_text'   => esc_html__( 'This email is sent as the first reminder to complete a purchase.', 'pmpro-abandoned-cart-recovery' )
	);

	$templates['pmproacr_reminder_2'] = array(
		'description' => esc_html__( 'Abandoned Cart Recovery - Reminder 2', 'pmpro-abandoned-cart-recovery' ),
		'subject'     => esc_html__( 'Reminder: Complete Your Membership Purchase with !!sitename!!', 'pmpro-abandoned-cart-recovery' ),
		'body'        => '<p>' . esc_html__( 'It looks like you may have forgotten to complete your purchase of the !!membership_level_name!! membership at !!sitename!!.', 'pmpro-abandoned-cart-recovery' ) . '</p><p><a href="!!checkout_url!!">' . esc_html__( 'Complete Your Purchase', 'pmpro-abandoned-cart-recovery' ) . '</a></p><p><a href="!!opt_out_url!!">' . esc_html__( 'Opt Out of These Emails', 'pmpro-abandoned-cart-recovery' ) . '</a></p>',
		'help_text'   => esc_html__( 'This email is sent as the second reminder to complete a purchase.', 'pmpro-abandoned-cart-recovery' )
	);

	$templates['pmproacr_reminder_3'] = array(
		'description' => esc_html__( 'Abandoned Cart Recovery - Reminder 3', 'pmpro-abandoned-cart-recovery' ),
		'subject'     => esc_html__( 'Final Reminder: Complete Your Membership Purchase Today', 'pmpro-abandoned-cart-recovery' ),
		'body'        => '<p>' . esc_html__( 'This is your final reminder to complete your purchase of the !!membership_level_name!! membership at !!sitename!!.', 'pmpro-abandoned-cart-recovery' ) . '</p><p><a href="!!checkout_url!!">' . esc_html__( 'Complete Your Purchase', 'pmpro-abandoned-cart-recovery' ) . '</a></p><p><a href="!!opt_out_url!!">' . esc_html__( 'Opt Out of These Emails', 'pmpro-abandoned-cart-recovery' ) . '</a></p>',
		'help_text'   => esc_html__( 'This email is sent as the third reminder to complete a purchase.', 'pmpro-abandoned-cart-recovery' )
	);

	return $templates;
}
add_filter( 'pmproet_templates', 'pmproacr_email_templates' );

/**
 * Send a reminder email.
 *
 * @since TBD
 *
 * @param object $recovery_attempt The recovery attempt.
 * @param int $reminder_number The reminder number.
 */
function pmproacr_send_reminder_email( $recovery_attempt, $reminder_number ) {
	// Get the user.
	$user  = get_userdata( $recovery_attempt->user_id );
	$level = pmpro_getLevel( $recovery_attempt->token_level_id );

	// Send the email.
	$email           = new PMProEmail();
	$email->template = 'pmproacr_reminder_' . $reminder_number;
	$email->email    = $user->user_email;
	$email->data     = array(
		'user_login' => $user->user_login,
		'user_email' => $user->user_email,
		'display_name' => $user->display_name,
		'header_name' => $user->display_name,
		'sitename' => get_option('blogname'),
		'siteemail' => get_option('pmpro_from_email'),
		'login_link' => pmpro_login_url(),
		'login_url' => pmpro_login_url(),
		'membership_id' => $level->id,
		'membership_level_name' => $level->name,
		'checkout_url' => pmpro_login_url( pmpro_url( 'checkout', '?pmpro_level=' . $level->id ) ),
		'levels_url' => pmpro_login_url( pmpro_url( 'levels' ) ),
		'opt_out_url' => add_query_arg( 'pmproacr_opt_out', urlencode( $user->user_email ), home_url() ),
	);
	$email->sendEmail();
}