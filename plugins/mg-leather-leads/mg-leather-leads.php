<?php
/*
Plugin Name: MG Leather Leads
Plugin URI: https://cgd.io
Description: Send notification emails to representatives about new leads.
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2016 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class MG_Leather_Leads {
	public function __construct() {} // silence is golden

	public function start() {
		// Catch Order Shipped
		add_action( 'shopp_shipped_order_event', array( $this, 'order_shipped' ) );

		// Handle Rep Lead Contacted
		add_action( 'wp_ajax_lead_update_contacted', array( $this, 'update_contacted' ) );
		add_action( 'wp_ajax_nopriv_lead_update_contacted', array( $this, 'update_contacted' ) );

		// Handle Reminder Cron
		add_action( 'mg_lead_send_reminder', array( $this, 'send_reminder' ), 10, 1 );
	}

	function order_shipped( $event ) {
		$order           = $event->order();
		$carrier         = $event->message['carrier'];
		$tracking        = $event->message['tracking'];
		$is_osr          = isset( $order->data['_intended_recipient'] );
		$is_bag_customer = isset( $order->data['Origin Product'] );
		$is_us           = 'US' === $order->shipcountry;

		if ( $is_osr || $is_bag_customer ) {
			return;
		}

		$leads_rep_emails = shopp_meta( $order->id, 'purchase', 'leads_rep_emails' );

		/**
		 * If order is shipped twice, we don't want to schedule another lead reminder
		 */
		if ( ! empty( $leads_rep_emails ) ) {
			return;
		}

		$reps = get_posts(
			array(
				'showposts'        => -1,
				'post_type'        => 'representative',
				'suppress_filters' => false,
				'tax_query'        => array(
					'relation' => 'AND',
					array(
						'taxonomy' => $is_us ? 'domestic_territory' : 'international_territory',
						'field'    => 'slug',
						'terms'    => array( strtolower( $is_us ? $order->shipstate : $order->shipcountry ) ),
					),
					array(
						'taxonomy' => 'representative_industry',
						'field'    => 'name',
						'terms'    => array( $order->data['Industry'] ),
					),
				),
			)
		);

		if ( $reps ) {

			// More Than One Rep
			if ( count( $reps ) > 1 ) {

				// Look up By Zips
				foreach ( $reps as $r ) {
					$zips = wp_get_post_terms( $r->ID, 'representative_zip' );

					if ( ! empty( $zips ) ) {
						foreach ( $zips as $t ) {

							if ( substr( $order->shippostcode, 0, 5 ) === $t->name ) {
								$matched = $r;
								break;
							}
						}
					}
				}

				// Random fall back
				if ( ! isset( $matched ) ) {
					$matched = $reps[ array_rand( $reps ) ];
				}
			} else { // Just One Rep
				$matched = $reps[0];
			}

			$email = get_post_meta( $matched->ID, 'rep_email', true );

			// Final Check
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				error_log( 'No emails found!' );
				return;
			}

			// Track Lead Status
			shopp_set_meta( $order->id, 'purchase', 'leads_assigned_rep', $matched->ID );
			shopp_set_meta( $order->id, 'purchase', 'leads_rep_emails', array( $email ) );
			shopp_set_meta( $order->id, 'purchase', 'leads_rep_contacted', 'false' );
			shopp_set_meta( $order->id, 'purchase', 'leads_reminders', 0 );

			// Add Note To Order
			$note                 = new ShoppMetaObject();
			$note->parent         = $order->id;
			$note->context        = 'purchase';
			$note->type           = 'order_note';
			$note->name           = 'note';
			$note->value          = new stdClass();
			$note->value->author  = 0;
			$note->value->message = "Lead assigned to {$matched->post_title}.";
			$note->value->sent    = false;
			$note->save();

			// Schedule Reminder (1 week)
			wp_schedule_single_event( time() + 604800, 'mg_lead_send_reminder', array( $order->id ) );
		} else {
			error_log( 'No reps found!' );
		}
	}

	function send_reminder( $order_id ) {
		$order         = ShoppPurchase( shopp_order( $order_id ) );
		$rep_id        = shopp_meta( $order_id, 'purchase', 'leads_assigned_rep' );
		$emails        = shopp_meta( $order_id, 'purchase', 'leads_rep_emails' );
		$rep_responded = shopp_meta( $order_id, 'purchase', 'leads_rep_contacted' );
		$reminders     = shopp_meta( $order_id, 'purchase', 'leads_reminders' );
		$name          = shopp( 'purchase.get-firstname' ) . ' ' . shopp( 'purchase.get-lastname' );

		if ( $reminders > 2 || count( $emails ) > 1 ) {
			return;
		}

		if ( 'true' !== $rep_responded && 'willnotcontact' !== $rep_responded && ! empty( $emails ) ) {
			// Send An Email
			foreach ( $emails as $e ) {
				// Add Note To Order
				$note                 = new ShoppMetaObject();
				$note->parent         = $order->id;
				$note->context        = 'purchase';
				$note->type           = 'order_note';
				$note->name           = 'note';
				$note->value          = new stdClass();
				$note->value->author  = 0;
				$note->value->message = "Lead notification sent to {$e}.";
				$note->value->sent    = false;
				$note->save();

				$order->email(
					'',
					$e,
					0 === $reminders ? 'New Lead - ' . $name : 'Reminder: New Lead - ' . $name,
					array( 'email-new-lead.php' )
				);
			}

			// Increment Reminders
			$reminders = $reminders + 1;
			shopp_set_meta( $order_id, 'purchase', 'leads_reminders', $reminders );

			// Limit Emails To 3
			if ( $reminders < 3 ) {
				// Schedule One More Reminder (1 week)
				wp_schedule_single_event( time() + 604800, 'mg_lead_send_reminder', array( $order_id ), true );
			}
		}
	}

	function update_contacted() {
		if ( isset( $_GET['value'] ) && ! empty( $_GET['value'] ) && ! empty( $_GET['order'] ) ) {
			shopp_set_meta( $_GET['order'], 'purchase', 'leads_rep_contacted', $_GET['value'] );

			echo "Thank you! We've registered your response.";
			die;
		}
	}
}

$mg_leather_leads = new MG_Leather_Leads();
$mg_leather_leads->start();
