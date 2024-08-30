<?php
/**
 * Simple CTA Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $alternative ) : ?>
	<table align="center" class="wrapper questions-shipping simple-cta larger float-center" style="Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%">
		<tr style="padding:0;text-align:left;vertical-align:top">
			<td class="wrapper-inner" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
				<table align="center" class="container" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
					<tbody>
						<tr style="padding:0;text-align:left;vertical-align:top">
							<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
								<div class="simple-cta-inner" style="background:#f6f6f6;padding-bottom:64px;padding-top:64px">
									<table align="center" class="container" style="Margin:0 auto;background:#f6f6f6;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
										<tbody>
											<tr style="padding:0;text-align:left;vertical-align:top">
												<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
													<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
														<tbody>
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th class="text-center small-12 large-12 columns first last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:center;width:564px">
																	<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																		<tr style="padding:0;text-align:left;vertical-align:top">
																			<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
																				<?php if ( ! empty( $title ) ) : ?>
																					<h6 style="Margin:0;Margin-bottom:10px;color:#232e32;font-family:Lato,sans-serif;font-size:48px;font-weight:700;letter-spacing:0;line-height:1;margin:0;margin-bottom:8px;padding:0;text-align:center;text-transform:none;word-wrap:normal"><?php echo $title; ?></h6>
																				<?php endif; ?>
																				<?php if ( ! empty( $sub_title ) ) : ?>
																					<h5 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:'Noto Serif SC',serif;font-size:20px;font-weight:lighter;line-height:1;margin:0;margin-bottom:32px;padding:0;text-align:center;word-wrap:normal"><?php echo $sub_title; ?></h5>
																				<?php endif; ?>
																				<?php if ( ! empty( $button ) && is_array( $button ) ) : ?>
																					<table class="button centered" style="Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;margin:0 0 16px 0;margin-bottom:0;margin-left:auto;margin-right:auto;padding:0;text-align:left;vertical-align:top;width:auto">
																						<tr style="padding:0;text-align:left;vertical-align:top">
																							<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
																								<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																									<tr style="padding:0;text-align:left;vertical-align:top">
																										<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;background:#EEB211;border:none;border-collapse:collapse!important;border-radius:3px;color:#fefefe;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;transition:all ease-in-out .1s!important;vertical-align:top;word-wrap:break-word"><a target="_blank" href="<?php echo $button['url']; ?>" style="Margin:0;border:0 solid #EEB211;border-radius:3px;color:#fefefe;display:inline-block;font-family:Lato,sans-serif;font-size:16px;font-weight:700;letter-spacing:2px;line-height:1.3;margin:0;padding:16px 36px;text-align:center;text-decoration:none;text-transform:uppercase"><?php echo $button['title']; ?></a></td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																				<?php endif; ?>
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
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
<?php else : ?>
	<table align="center" class="wrapper questions-have-problem simple-cta float-center" style="Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%">
		<tr style="padding:0;text-align:left;vertical-align:top">
			<td class="wrapper-inner" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
				<table align="center" class="container" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
					<tbody>
						<tr style="padding:0;text-align:left;vertical-align:top">
							<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
								<div class="simple-cta-inner" style="padding-bottom:64px;padding-top:64px">
									<table align="center" class="container" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
										<tbody>
											<tr style="padding:0;text-align:left;vertical-align:top">
												<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
													<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
														<tbody>
															<tr style="padding:0;text-align:left;vertical-align:top">
																<th class="text-center small-12 large-12 columns first last" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:center;width:564px">
																	<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																		<tr style="padding:0;text-align:left;vertical-align:top">
																			<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
																				<?php if ( ! empty( $title ) ) : ?>
																					<h6 style="Margin:0;Margin-bottom:10px;color:#EEB211;font-family:Lato,sans-serif;font-size:18px;font-weight:400;letter-spacing:1.5px;line-height:1.3;margin:0;margin-bottom:16px;padding:0;text-align:center;text-transform:uppercase;word-wrap:normal"><?php echo $title; ?></h6>
																				<?php endif; ?>
																				<?php if ( ! empty( $sub_title ) ) : ?>
																					<h5 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:'Noto Serif SC',serif;font-size:26px;font-weight:lighter;line-height:1.3;margin:0;margin-bottom:32px;padding:0;text-align:center;word-wrap:normal"><?php echo $sub_title; ?></h5>
																				<?php endif; ?>
																				<?php if ( ! empty( ! empty( $button ) && is_array( $button ) ) ) : ?>
																					<table class="button centered outline" style="Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;margin:0 0 16px 0;margin-bottom:0;margin-left:auto;margin-right:auto;padding:0;text-align:left;vertical-align:top;width:auto">
																						<tr style="padding:0;text-align:left;vertical-align:top">
																							<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
																								<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																									<tr style="padding:0;text-align:left;vertical-align:top">
																										<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;background:#fff;border:none;border-collapse:collapse!important;border-radius:3px;color:#fefefe;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;transition:all ease-in-out .1s!important;vertical-align:top;word-wrap:break-word"><a target="_blank" href="<?php echo $button['url']; ?>" style="Margin:0;border:1px solid #232e32;border-radius:3px;color:#232e32;display:inline-block;font-family:Lato,sans-serif;font-size:16px;font-weight:700;letter-spacing:2px;line-height:1.3;margin:0;padding:16px 36px;text-align:center;text-decoration:none;text-transform:uppercase"><?php echo $button['title']; ?></a></td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																				<?php endif; ?>
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
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
<?php endif; ?>
