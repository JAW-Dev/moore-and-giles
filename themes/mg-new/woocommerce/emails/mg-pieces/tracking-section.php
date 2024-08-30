<?php

use Objectiv\Site\Woo\WismoLabs;

/**
 * Tracking Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tracking_items = false;

if ( class_exists( 'WC_Shipment_Tracking_Actions' ) ) {
	$tracking_actions = new WC_Shipment_Tracking_Actions();
	$tracking_items   = $tracking_actions->get_tracking_items( $order->id );
}


if ( $tracking_items ) {
	$title     = "Your Order Is On Its Way";
	$sub_title = "Don't Like Surprises? Track Your Package.";
	$wismo     = new WismoLabs();
	
	/**
	 * Output the separator line
	 */
	do_action( 'mg_woo_email_separator_line' );

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
												<div class="base-padding-top-4 base-padding-bottom-4" style="padding-bottom:64px;padding-top:64px">
													<?php if ( ! empty( $title ) ) : ?>
														<h1 class="text-center sans-serif f40 font-bold" style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Lato,sans-serif;font-size:40px;font-weight:700;line-height:1;margin:0;margin-bottom:10px;padding:0;text-align:center;word-wrap:normal"><?php echo $title; ?></h1>
													<?php endif; ?>
													<?php if ( ! empty( $sub_title ) ) : ?>
														<p class="text-center text-primary f20 base-margin-bottom-2 font-lighter" style="Margin:0;Margin-bottom:10px;color:#EEB211;font-family:'Noto Serif SC',serif;font-size:20px;font-weight:lighter;line-height:1.3;margin:0;margin-bottom:32px;padding:0;text-align:center"><?php echo $sub_title; ?></p>
													<?php endif; ?>
													<?php
													$package_number = 1;
													foreach ( $tracking_items as $tracking_item ) :
														$wismo_url    = $wismo->get_tracking_url( $order, $tracking_item, $tracking_actions );
														$button_title = 'Track Package';
														$margin_top   = 'margin-top: 0px;';
														if ( $package_number > 1 ) {
															$button_title = 'Track Package ' . $package_number;
															$margin_top   = 'margin-top: 10px;';
														}
														$button = array(
															'title' => $button_title,
															'url' => $wismo_url,
														);
														if ( ! empty( $button ) && is_array( $button ) && ! empty( $wismo_url ) ) :
														?>
															<table class="button centered" style="Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;margin:0 0 16px 0;margin-bottom:0;margin-left:auto;margin-right:auto;padding:0;text-align:left;vertical-align:top;width:auto;<?php echo $margin_top; ?>">
																<tr style="padding:0;text-align:left;vertical-align:top">
																	<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
																		<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																			<tr style="padding:0;text-align:left;vertical-align:top">
																				<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;background:#EEB211;border:none;border-collapse:collapse!important;border-radius:3px;color:#fefefe;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;transition:all ease-in-out .1s!important;vertical-align:top;word-wrap:break-word"><a href="<?php echo $button['url']; ?>" target="_blank" style="Margin:0;border:0 solid #EEB211;border-radius:3px;color:#fefefe;display:inline-block;font-family:Lato,sans-serif;font-size:16px;font-weight:700;letter-spacing:2px;line-height:1.3;margin:0;padding:16px 36px;text-align:center;text-decoration:none;text-transform:uppercase"><?php echo $button['title']; ?></a></td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														<?php
														endif;
														$package_number++;
													endforeach;
													?>
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
	<?php
}
