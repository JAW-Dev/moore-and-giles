<?php
/**
 * General Text Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php if ( ! empty( $email_content ) ) : ?>
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
													<?php echo $email_content; ?>
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
