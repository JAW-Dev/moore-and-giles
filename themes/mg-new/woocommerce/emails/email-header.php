<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo get_bloginfo( 'name', 'display' ); ?> - <?php echo $email_heading; ?></title>
	<style>
		@media only screen {
			html {
				min-height: 100%;
				background: #f3f3f3
			}
		}
		
		@media only screen and (max-width:596px) {
			table.body img {
				width: auto;
				height: auto
			}
			table.body center {
				min-width: 0!important
			}
			table.body .container {
				width: 95%!important
			}
			table.body .columns {
				height: auto!important;
				-moz-box-sizing: border-box;
				-webkit-box-sizing: border-box;
				box-sizing: border-box;
				padding-left: 16px!important;
				padding-right: 16px!important
			}
			table.body .collapse .columns {
				padding-left: 0!important;
				padding-right: 0!important
			}
			th.small-3 {
				display: inline-block!important;
				width: 25%!important
			}
			th.small-4 {
				display: inline-block!important;
				width: 33.33333%!important
			}
			th.small-5 {
				display: inline-block!important;
				width: 41.66667%!important
			}
			th.small-6 {
				display: inline-block!important;
				width: 50%!important
			}
			th.small-7 {
				display: inline-block!important;
				width: 58.33333%!important
			}
			th.small-12 {
				display: inline-block!important;
				width: 100%!important
			}
		}
		
		@media (min-width:596px) {
			.table-separator-line {
				margin-left: 16px;
				margin-right: 16px
			}
		}
		
		@media (min-width:596px) {
			.table-separator-line.wider {
				margin-left: 16px;
				margin-right: 16px;
				max-width: 100%
			}
		}
		
		@media (min-width:596px) {
			th.header-right p {
				padding-bottom: 32px
			}
		}
		
		@media (min-width:596px) {
			.share-ig .share-ig-inner .adventure-text {
				margin-bottom: 0;
				text-align: right
			}
		}
		
		@media (min-width:596px) {
			.share-ig .share-ig-inner .button.centered {
				margin-left: 0
			}
		}
	</style>
</head>

<body style="-moz-box-sizing:border-box;-ms-text-size-adjust:100%;-webkit-box-sizing:border-box;-webkit-text-size-adjust:100%;Margin:0;box-sizing:border-box;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;min-width:100%;padding:0;text-align:left;width:100%!important"><span class="preheader" style="color:#f3f3f3;display:none!important;font-size:1px;line-height:1px;max-height:0;max-width:0;mso-hide:all!important;opacity:0;overflow:hidden;visibility:hidden"></span>
	<table class="body" style="Margin:0;background:#ececec;border-collapse:collapse;border-spacing:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%">
		<tr style="padding:0;text-align:left;vertical-align:top">
			<td class="center" align="center" valign="top" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
				<center data-parsed style="min-width:580px;width:100%">
					<table align="center" class="wrapper header float-center" style="Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%">
						<tr style="padding:0;text-align:left;vertical-align:top">
							<td class="wrapper-inner" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
								<table align="center" class="container" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;margin:0 auto;padding:0;text-align:inherit;vertical-align:top;width:580px">
									<tbody>
										<tr style="padding:0;text-align:left;vertical-align:top">
											<td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
												<table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
													<tbody>
														<tr style="padding:0;text-align:left;vertical-align:top">
															<th class="header-left small-12 large-3 columns first" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:8px;text-align:left;width:129px">
																<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																	<tr style="padding:0;text-align:left;vertical-align:top">
																		<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
																			<a target="_blank" href="<?php echo get_home_url(); ?>" title="Shop Moore & Giles" style="Margin:0;color:#EEB211;font-family:'Noto Serif SC',serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none"><img src="<?php echo get_stylesheet_directory_uri(); ?>/src/images/emails/mg-logo.png" alt="Shop Moore & Giles" width="124" height="114" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;margin-left:auto;margin-right:auto;max-width:124px;outline:0;text-decoration:none;width:auto"></a>
																		</th>
																	</tr>
																</table>
															</th>
															<th class="header-right small-12 large-9 columns last" valign="bottom" style="Margin:0 auto;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:8px;padding-right:16px;text-align:left;width:419px">
																<table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
																	<tr style="padding:0;text-align:left;vertical-align:top">
																		<th style="Margin:0;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
																			<p class="text-center base-border-bottom" style="Margin:0;Margin-bottom:10px;border-bottom:1px solid #ccc;color:#232e32;font-family:'Noto Serif SC',serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:16px;padding-top:32px;text-align:center">
																				<a class="sans-serif" target="_blank" href="<?php echo get_home_url(); ?>/shop" title="Shop Moore & Giles" style="Margin:0;color:#6e706f;font-family:Lato,sans-serif;font-weight:400;line-height:1.3;margin:0;margin-left:0;padding:0;text-align:left;text-decoration:none">Shop</a> 
																				<a class="sans-serif" target="_blank" href="<?php echo get_home_url(); ?>/bag-frequently-asked-questions/" title="Frequently Asked Questions" style="Margin:0;color:#6e706f;font-family:Lato,sans-serif;font-weight:400;line-height:1.3;margin:0;margin-left:16px;padding:0;text-align:left;text-decoration:none">FAQs</a> 
																				<a class="sans-serif" target="_blank" href="<?php echo get_home_url(); ?>/my-account/" title="My Moore & Giles Account" style="Margin:0;color:#6e706f;font-family:Lato,sans-serif;font-weight:400;line-height:1.3;margin:0;margin-left:16px;padding:0;text-align:left;text-decoration:none">My Account</a>
																			</p>
																		</th>
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
							</td>
						</tr>
					</table>
					<!-- End of header -->
