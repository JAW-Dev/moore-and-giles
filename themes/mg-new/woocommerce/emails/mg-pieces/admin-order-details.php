<?php
/**
 * Order Details Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$coupons_used   = $order->get_used_coupons();
$total_discount = $order->get_discount_total();
$order_meta     = $order->get_meta_data();
$meta_blockers  = array(
	'_billing_wc_avatax_vat_id',
	'_mes_authorization_code',
	'_order_number',
	'_order_number_formatted',
	'_order_number_meta',
	'_recorded_variation_sales',
	'_wc_avatax_tax_calculated',
	'_wc_avatax_tax_date',
);
$meta_displayed = false;

?>
<table align="center" class="container float-center" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px">
	<tbody>
		<tr style="padding:0;text-align:left;vertical-align:top">
			<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
				<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
					<tbody>
						<tr style="padding:0;text-align:left;vertical-align:top">
							<th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:left;width:564px">
								<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
									<tr style="padding:0;text-align:left;vertical-align:top">
										<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
											<div class="base-padding-top-4 base-padding-bottom-4" style="padding-bottom:64px;padding-top:64px;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;text-align:left">
												<?php if ( ! empty( $coupons_used ) && is_array( $coupons_used ) ) : ?>
													<h2 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Lato,sans-serif;font-size:40px;font-weight:700;line-height:1;margin:0;margin-bottom:10px;padding:0;text-align:center;word-wrap:normal" >Admin Details</h2>
													<h3>Coupon Code(s) Used - Totalling $<?php echo $total_discount; ?></h3>
													<ul>
														<?php foreach ( $coupons_used as $coupon ) : ?>
															<li><?php echo $coupon; ?></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
												<?php if ( ! empty( $order_meta ) && is_array( $order_meta ) ) : ?>
												<h3>Order Meta</h3>
													<ul>
														<?php
														foreach ( $order_meta as $single_meta ) :
															?>
															<?php
																$data  = $single_meta->get_data();
																$key   = $data['key'];
																$value = $data['value'];

															if ( ! in_array( $key, $meta_blockers ) && ! empty( $value ) ) {
																echo "<li class=''>$key - $value</li>";
																$meta_displayed = true;
															}
															?>
														<?php endforeach; ?>
														<?php if( ! $meta_displayed ) : ?>
															<li>Nothing to display.</li>
														<?php endif; ?>
													</ul>
												<?php endif; ?>
											</div>
										</th>
										<th class="expander" style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
									</tr>
								</table>
							</th>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
