<?php
/**
 * Order Details Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shipping_address       = $order->get_formatted_shipping_address();
$shipping_phone         = get_post_meta( $order->get_id(), '_shipping_phone' ) ? '<br/>' . get_post_meta( $order->get_id(), '_shipping_phone', true ) : '';
$billing_address        = $order->get_formatted_billing_address();
$billing_name           = $order->get_formatted_billing_full_name();
$billing_email          = $order->get_billing_email() ? '<br/>' . $order->get_billing_email() : '';
$billing_phone          = $order->get_billing_phone() ? '<br/>' . $order->get_billing_phone() : '';
$order_phone            = $shipping_phone ? $shipping_phone : $billing_phone;
$order_number           = $order->get_order_number();
$order_items            = $order->get_items();
$order_item_totals      = $order->get_order_item_totals();
$order_pay_method_title = $order->get_payment_method_title();
$card_type              = $order_pay_method_title === 'Credit Card' ? ' (' . $order->get_meta( '_mes_card_type' ) . ')' : '';
$order_id               = $order->get_id();
$gift_note              = get_post_meta( $order_id, 'gift_note', true );
$waive_signature        = get_post_meta( $order_id, 'waive_signature', true ) ? 'Yes' : 'No';
$last4                  = $order->get_meta( '_mes_card_number' );

// Remove payement type from order totals.
unset( $order_item_totals['payment_method'] );

?>

<table align="center" class="container float-center" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px">
<tbody>
	<tr style="padding:0;text-align:left;vertical-align:top">
		<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
			<div class="base-padding-top-2 base-padding-bottom-2 bg-gray-200" style="background:#f6f6f6;padding-bottom:32px;padding-top:32px">
				<table align="center" class="container collapse bg-gray-200" style="Margin:0 auto;background:#f6f6f6;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
					<tbody>
						<tr style="padding:0;text-align:left;vertical-align:top">
							<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
								<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
									<tbody>
										<tr style="padding:0;text-align:left;vertical-align:top">
											<th class="small-4 large-4 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;text-align:left;width:201.33px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
															<div class="order-detail-title" style="font-family:Lato,sans-serif;font-size:16px;font-weight:700;padding-bottom:16px">Shipping Address:</div>
															<div class="order-detail-detail" style="font-size:15px"><?php echo $shipping_address; ?></div>
														</th>
													</tr>
												</table>
											</th>
											<th class="small-4 large-4 columns" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:8px;text-align:left;width:177.33px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
															<div class="order-detail-title" style="font-family:Lato,sans-serif;font-size:16px;font-weight:700;padding-bottom:16px">Billing Address:</div>
															<div class="order-detail-detail" style="font-size:15px"><?php echo $billing_address . $billing_email . $order_phone; ?></div>
														</th>
													</tr>
												</table>
											</th>
											<th class="small-4 large-4 columns last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:16px;text-align:left;width:201.33px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
															<div class="order-detail-title" style="font-family:Lato,sans-serif;font-size:16px;font-weight:700;padding-bottom:16px">Order Number:</div>
															<div class="order-detail-detail" style="font-size:15px"><?php echo $order_number; ?></div>
															<div class="order-detail-title" style="font-family:Lato,sans-serif;font-size:16px;font-weight:700;padding-bottom:16px;padding-top:16px">Payment Method:</div>
															<div class="order-detail-detail" style="font-size:15px"><?php echo $order_pay_method_title . $card_type; ?></div>
															<?php if ( ! empty( $last4 ) ) : ?>
																<div class="order-detail-detail" style="font-size:15px"><?php echo $last4; ?></div>
															<?php endif; ?>
														</th>
													</tr>
												</table>
											</th>
										</tr>
									</tbody>
								</table>
								<?php if ( $gift_note ) : ?>
								<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
									<tbody>
										<tr style="padding:0;text-align:left;vertical-align:top">
											<th class="small-4 large-4 columns last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:left;width:201.33px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
															<div class="order-detail-title" style="margin-top: 16px;font-family:Lato,sans-serif;font-size:16px;font-weight:700;">Gift Note: <span class="order-detail-detail" style="font-weight:normal;font-size:15px"><?php echo $gift_note; ?></span></div>
														</th>
													</tr>
												</table>
											</th>
										</tr>
									</tbody>
								</table>
								<?php endif; ?>


								<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
									<tbody>
										<tr style="padding:0;text-align:left;vertical-align:top">
											<th class="small-4 large-4 columns last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:left;width:201.33px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
															<div class="order-detail-title" style="margin-top: 16px;font-family:Lato,sans-serif;font-size:16px;font-weight:700;">Wave Signature: <span class="order-detail-detail" style="font-weight:normal;font-size:15px"><?php echo $waive_signature; ?></span></div>
														</th>
													</tr>
												</table>
											</th>
										</tr>
									</tbody>
								</table>


								<div class="table-separator-line black-border base-margin-top-2" style="border-color:#232e32;border-top:1px solid #ccc;margin-top:32px"></div>
								<table class="row item-details-column-titles" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
									<tbody>
										<tr style="padding:0;text-align:left;vertical-align:top">
											<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;text-transform:uppercase;width:298px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0;padding:0;padding-bottom:8px;padding-top:8px;text-align:left;text-transform:uppercase">Item Details</th>
													</tr>
												</table>
											</th>
											<th class="small-3 large-3 columns" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:8px;padding-top:0;text-align:left;text-transform:uppercase;width:129px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0;padding:0;padding-bottom:8px;padding-top:8px;text-align:left;text-transform:uppercase">QTY</th>
													</tr>
												</table>
											</th>
											<th class="small-3 large-3 columns last" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:16px;padding-top:0;text-align:left;text-transform:uppercase;width:153px">
												<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
													<tr style="padding:0;text-align:left;vertical-align:top">
														<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:15px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0;padding:0;padding-bottom:8px;padding-top:8px;text-align:left;text-transform:uppercase">Price</th>
													</tr>
												</table>
											</th>
										</tr>
									</tbody>
								</table>
								<div class="table-separator-line black-border" style="border-color:#232e32;border-top:1px solid #ccc"></div>
								<!-- Begin loop through items -->
								<?php if ( ! empty( $order_items ) && is_array( $order_items ) ) : ?>
									<?php
									foreach ( $order_items as $order_item ) :
										$item_data       = $order_item->get_data();
										$personalization = wc_get_order_item_meta( $item_data['id'], 'Personalization', true );
										$gift_wrapping   = wc_get_order_item_meta( $item_data['id'], 'Gift Wrapping', true );
										$item_attributes = get_order_item_attributes( $order_item );

										foreach ( $item_attributes as $key => $value ) :
											if ( 'pa_color-family' === $key ) {
												unset( $item_attributes[ $key ] );
											}
										endforeach;
										?>
										<table class="row item-details-detail" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
											<tbody>
												<tr style="padding:0;text-align:left;vertical-align:top">
													<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;width:298px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-top:16px;text-align:left">
																	<?php
																	echo $order_item->get_name();
																	?>
																</th>
															</tr>
														</table>
													</th>
													<th class="small-3 large-3 columns" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:8px;padding-top:0;text-align:left;width:129px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-top:16px;text-align:left"><?php echo $order_item->get_quantity(); ?></th>
															</tr>
														</table>
													</th>
													<th class="small-3 large-3 columns last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:16px;padding-top:0;text-align:left;width:153px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-top:16px;text-align:left">$<?php echo $order_item->get_subtotal(); ?></th>
															</tr>
														</table>
													</th>
												</tr>
											</tbody>
										</table>
										<?php
										foreach ( $item_attributes as $key => $value ) :
											if ( 'pa_color' === $key ) :
												?>
												<table class="row item-details-detail" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
													<tbody>
														<tr style="padding:0;text-align:left;vertical-align:top">
															<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;width:298px">
																<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																	<tr style="padding:0;text-align:left;vertical-align:top">
																		<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-left:8px;text-align:left">
																			<div><?php echo $value['name']; ?>: <?php echo $value['value']; ?></div>
																		</th>
																	</tr>
																</table>
															</th>
														</tr>
													</tbody>
												</table>
												<?php
											endif;
										endforeach;
										?>
										<?php if ( $personalization ) : ?>
										<table class="row item-details-detail" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
											<tbody>
												<tr style="padding:0;text-align:left;vertical-align:top">
													<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;width:298px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-left:8px;text-align:left">
																	<div>Personalization: <?php echo $personalization; ?></div>
																</th>
															</tr>
														</table>
													</th>
												</tr>
											</tbody>
										</table>
										<?php endif; ?>
										<?php if ( $gift_wrapping ) : ?>
										<table class="row item-details-detail" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
											<tbody>
												<tr style="padding:0;text-align:left;vertical-align:top">
													<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;width:298px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;padding-left:8px;text-align:left">
																	<div>Gift Wrapping</div>
																</th>
															</tr>
														</table>
													</th>
												</tr>
											</tbody>
										</table>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
								<?php if ( ! empty( $order_item_totals ) && is_array( $order_item_totals ) ) : ?>
									<div class="table-separator-line black-border base-margin-top base-margin-bottom" style="border-color:#232e32;border-top:1px solid #ccc;margin-bottom:16px;margin-top:16px"></div>
									<?php foreach ( $order_item_totals as $order_item_total_detail ) : ?>
										<table class="row item-details-summary-row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
											<tbody>
												<tr style="padding:0;text-align:left;vertical-align:top">
													<th class="small-6 large-6 columns first" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;padding-top:0;text-align:left;width:298px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0;padding:0;padding-top:6px;text-align:left"><?php echo $order_item_total_detail['label']; ?></th>
															</tr>
														</table>
													</th>
													<th class="small-3 large-3 columns" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:8px;padding-top:0;text-align:left;width:129px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0;padding:0;padding-top:6px;text-align:left"></th>
															</tr>
														</table>
													</th>
													<th class="small-3 large-3 columns last" style="Margin:0 auto;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:16px;padding-top:0;text-align:left;width:153px">
														<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th style="Margin:0;color:#232e32;font-family:Lato,sans-serif;font-size:18px;font-weight:700;line-height:1.3;margin:0;padding:0;padding-top:6px;text-align:left"><?php echo $order_item_total_detail['value']; ?></th>
															</tr>
														</table>
													</th>
												</tr>
											</tbody>
										</table>
									<?php endforeach; ?>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</td>
	</tr>
</tbody>
</table>
