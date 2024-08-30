<?php
/**
 * Top Hero Section with Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! empty( $title ) || ! empty( $sub_title ) || ! empty( $image_url ) || ! empty( $email_content ) || ! empty( $button ) ) : ?>
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
													<?php if ( ! empty( $image_url ) ) : ?>
													<div class="base-padding-top-4 base-padding-bottom-4" style="padding-bottom:64px;padding-top:64px;">
														<img class="text-center" style="max-width:548px;width:100%;height:auto;" src="<?php echo $image_url; ?>"/>
													</div>
													<?php endif; ?>
													<?php if ( ! empty( $email_content ) ) : ?>
													<div class="base-padding-top-4 base-margin-bottom-2" style="padding-bottom:64px;padding-top:64px;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;text-align:left">
														<?php echo $email_content; ?>
													</div>
													<?php endif; ?>

													<?php if ( ! empty( $button ) && is_array( $button ) ) : ?>
														<table class="button centered" style="Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;margin:0 0 16px 0;margin-bottom:0;margin-left:auto;margin-right:auto;padding:0;text-align:left;vertical-align:top;width:auto">
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
<?php endif; ?>
