<?php
	/**
	 * This script sync wordpress user with an active role.
	 * (except the woocommerce roles!)
	 *
	 * @version 2.0.0 Free
	 */
	global $wpdb;
	require_once(dirname(__FILE__) . '/../vendor/klicktipp.api.inc.php');

	$klicktipUsername = get_option('wptkt_klicktipp_username');
	$klicktipPassword = get_option('wptkt_klicktipp_password');

	$apiValue = get_option('wptkt_klicktipp_api');
	if ($apiValue == 'br') {
		$apiUrl = 'http://api.klickmail.com.br';
	} else {
		$apiUrl = 'http://api.klick-tipp.com';
	}
	$connector = new KlicktippConnector($apiUrl);
	$isConnected = $connector->login($klicktipUsername, $klicktipPassword);
	
	function getTagId($connector, $tagName, $tags) {
	    $tagId = array_search($tagName, $tags);
	    if (is_null($tagId) || $tagId === false) $tagId = $connector->tag_create($tagName, '');
	    return $tagId;
    }

	function hasCustomerCompletedOrder($customerId) {
		global $wpdb;
		$orders = $wpdb->get_row("SELECT COUNT(*) AS count FROM wp_posts WHERE post_status = 'wc-completed' AND ID IN (SELECT post_id FROM wp_postmeta WHERE meta_key = '_customer_user' AND meta_value = '" . $customerId . "')");
		if (!is_null($orders) && ($orders->count > 0)) {
			return true;
		} else {
			return false;
		}
	}

	if ($isConnected) {
		$tags = $connector->tag_index();
		$user = get_user_by('id',$iUserID);
		$roles = array(
			'subscriber' => 'Subscriber',
			'customer' => 'Customer'
		);

		foreach ($roles AS $slug => $role) {
			if (in_array($slug, $user->roles)) {
			    $emailAddress = $user->data->user_email;
			    $userData = get_userdata($user->data->ID);
			    $fields = array(
				    'fieldFirstName' => $userData->first_name,
				    'fieldLastName' => $userData->last_name
			    );
				$subscriberId = $connector->subscriber_search($emailAddress);

				if ($subscriberId) {
					if ($slug == 'customer') {
						$fields = array();
					}
					$connector->subscriber_update($subscriberId, $fields);
					$subscriber = $connector->subscriber_get($subscriberId);
					$subscriberTags = $subscriber->tags;
					$tagId = getTagId($connector, $role, $tags);
					if (!in_array($tagId, $subscriberTags)) $connector->tag($emailAddress, $tagId);
				} else {
				    $doubleOptinProcessId = get_option('wptkt_role_' . $slug . '_id');
				    if (!isset($doubleOptinProcessId) || empty($doubleOptinProcessId)) {
					    $doubleOptinProcessId = 0;
				    }
				    $tagId = getTagId($connector, $role, $tags);
				    if ($slug == 'customer') {
					    if (hasCustomerCompletedOrder($user->data->ID))
						    $subscriber = $connector->subscribe($emailAddress, $doubleOptinProcessId, $tagId, $fields);
				    } else {
					    $subscriber = $connector->subscribe($emailAddress, $doubleOptinProcessId, $tagId, $fields);
				    }
			    }
			}
		}
		$connector->logout();
	}
?>