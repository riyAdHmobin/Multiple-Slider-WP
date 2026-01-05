<?php
/**
 * Common Functions.
 *
 * @package Blog_Reader
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if user subscribed to memberships or not.
 *
 * @return bool true | false.
 */
function blp_br_membership_status() {
	$user_eligible        = false;
	$blp_br_settings      = get_option( 'blp_blog_options' );
	$selected_memberships = ! empty( $blp_br_settings['blp_br_allowed_memberships'] )
		? $blp_br_settings['blp_br_allowed_memberships']
		: array();

	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	if ( empty( $selected_memberships ) ) {
		return false;
	}

	if ( class_exists( 'MeprUtils' ) ) {
		$user = MeprUtils::get_currentuserinfo();
		if ( false !== $user && isset( $user->ID ) ) {
			// Returns an array of Membership ID's of current user's active memberships.
			$active_products = $user->active_product_subscriptions( 'ids' );
			if ( ! empty( $active_products ) && ! empty( array_intersect( $active_products, $selected_memberships ) ) ) {
				$user_eligible = true;
			}
		}
	} else {
		$user_eligible = true;
	}
	return $user_eligible;
}
