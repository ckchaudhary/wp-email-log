<?php
/**
 * Plugin Name: RB Email Logger
 * Plugin URI: https://github.com/ckchaudhary/wp-email-log
 * Description: Logs all emails, sent using wp_mail function. Logs all emails from buddypress as well.
 * Version: 1.0.0
 * Author: ckchaudhary
 * Author URI: https://www.recycleb.in/u/chandan/
 * Licence: GPLv2
 *
 * @package RB Email Logger
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) ? '' : exit();

\add_action( 'wp_mail_succeeded', 'rbel_log_success_email' );

/**
 * Logs emails sent successfuly!
 *
 * @param array $mail_data {
 *     An array containing the email recipient(s), subject, message, headers, and attachments.
 *
 *     @type string[] $to          Email addresses to send message.
 *     @type string   $subject     Email subject.
 *     @type string   $message     Message contents.
 *     @type string[] $headers     Additional headers.
 *     @type string[] $attachments Paths to files to attach.
 * }
 * @return bool
 */
function rbel_log_success_email( $mail_data ) {
	if ( ! class_exists( '\RBDebugLog' ) ) {
		return false;
	}

	$to = is_array( $mail_data['to'] ) ? implode( ', ', $mail_data['to'] ) : $mail_data['to'];

	$log_text  = 'TO: ' . $to . PHP_EOL;
	$log_text .= 'SUBJECT: ' . $mail_data['subject'] . PHP_EOL;
	$log_text .= '----------------------------' . PHP_EOL;
	$log_text .= 'MESSAGE: ' . $mail_data['message'];
	\RBDebugLog::log( $log_text, 'emails-success' );
}

\add_action( 'wp_mail_failed', 'rbel_log_failed_email' );

/**
 * Logs emails that didn't go through.
 *
 * @param \WP_Error $error A WP_Error object with the PHPMailer\PHPMailer\Exception message, and an array
 *                         containing the mail recipient, subject, message, headers, and attachments.
 * @return bool
 */
function rbel_log_failed_email( $error ) {
	if ( ! class_exists( '\RBDebugLog' ) ) {
		return false;
	}

	$mail_data = $error->get_all_error_data();
	$log_text  = 'TO: ' . $mail_data['to'] . PHP_EOL;
	$log_text .= 'SUBJECT: ' . $mail_data['subject'] . PHP_EOL;
	$log_text .= '----------------------------' . PHP_EOL;
	$log_text .= 'MESSAGE: ' . $mail_data['message'];
	\RBDebugLog::log( $log_text, 'emails-failed' );
}

\add_action( 'bp_send_email_failure', 'rbel_log_bp_email', 10, 2 );
\add_action( 'bp_send_email_success', 'rbel_log_bp_email', 10, 2 );

/**
 * Log all emails sent from buddypress.
 * Since buddypress, by default, doesnt' use wp_mail and uses its own implementation.
 *
 * @param bool|\WP_Error $status True if email successed.
 *                               If failed, a WP_Error object describing why the email failed to send.
 *                               The contents will vary based on the email delivery class you are using.
 * @param \BP_Email      $bp_email  The email we tried to send.
 * @return bool
 */
function rbel_log_bp_email( $status, $bp_email ) {
	if ( ! class_exists( '\RBDebugLog' ) ) {
		return false;
	}

	$recepients_email_ids = '';
	$recepients = $bp_email->get_to();
	foreach ( $recepients as $recepient ) {
		$recepients_email_ids .= $recepient->get_address() . ' ';
	}
	$log_text  = 'TO: ' . $recepients_email_ids . PHP_EOL;
	$log_text .= 'SUBJECT: ' . $bp_email->get_subject( 'replace-tokens' ) . PHP_EOL;
	$log_text .= '----------------------------' . PHP_EOL;
	$log_text .= 'MESSAGE: ' . $bp_email->get_content_html( 'replace-tokens' );

	if ( is_wp_error( $status ) ) {
		\RBDebugLog::log( $log_text, 'emails-failed' );
	} else {
		\RBDebugLog::log( $log_text, 'emails-success' );
	}
}
