<?php

public function give_user_subscription( $product, $user_id, $note = '' ) {

	// First make sure all required functions and classes exist
	if( ! function_exists( 'wc_create_order' ) || ! function_exists( 'wcs_create_subscription' ) || ! class_exists( 'WC_Subscriptions_Product' ) ){
		return false;
	}

	$order = wc_create_order( array( 'customer_id' => $user_id ) );

	if( is_wp_error( $order ) ){
		return false;
	}

	$user = get_user_by( 'ID', $user_id );

	$fname     = $user->first_name;
	$lname     = $user->last_name;
	$email     = $user->user_email;
	$address_1 = get_user_meta( $user_id, 'billing_address_1', true );
	$address_2 = get_user_meta( $user_id, 'billing_address_2', true );
	$city      = get_user_meta( $user_id, 'billing_city', true );
	$postcode  = get_user_meta( $user_id, 'billing_postcode', true );
	$country   = get_user_meta( $user_id, 'billing_country', true );
	$state     = get_user_meta( $user_id, 'billing_state', true );

	$address         = array(
		'first_name' => $fname,
		'last_name'  => $lname,
		'email'      => $email,
		'address_1'  => $address_1,
		'address_2'  => $address_2,
		'city'       => $city,
		'state'      => $state,
		'postcode'   => $postcode,
		'country'    => $country,
	);

	$order->set_address( $address, 'billing' );
	$order->set_address( $address, 'shipping' );
	$order->add_product( $product, 1 );

	$sub = wcs_create_subscription(array(
		'order_id' => $order->get_id(),
		'status' => 'pending', // Status should be initially set to pending to match how normal checkout process goes
		'billing_period' => WC_Subscriptions_Product::get_period( $product ),
		'billing_interval' => WC_Subscriptions_Product::get_interval( $product )
	));

	if( is_wp_error( $sub ) ){
		return false;
	}

	// Modeled after WC_Subscriptions_Cart::calculate_subscription_totals()
	$start_date = gmdate( 'Y-m-d H:i:s' );
	// Add product to subscription
	$sub->add_product( $product, 1 );

	$dates = array(
		'trial_end'    => WC_Subscriptions_Product::get_trial_expiration_date( $product, $start_date ),
		'next_payment' => WC_Subscriptions_Product::get_first_renewal_payment_date( $product, $start_date ),
		'end'          => WC_Subscriptions_Product::get_expiration_date( $product, $start_date ),
	);

	$sub->update_dates( $dates );
	$sub->calculate_totals();

	// Update order status with custom note
	$note = ! empty( $note ) ? $note : __( 'Programmatically added order and subscription.' );
	$order->update_status( 'completed', $note, true );
	// Also update subscription status to active from pending (and add note)
	$sub->update_status( 'active', $note, true );

	return $sub;
}