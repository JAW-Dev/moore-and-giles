<?php $email_data = $GLOBALS['u_reminder_email_data']; ?>
Content-type: text/html; charset=utf-8
From: <?php echo $email_data['from'] . PHP_EOL; ?>
To: <?php echo $email_data['to'] . PHP_EOL; ?>
Subject: <?php echo $email_data['subject'] . PHP_EOL; ?>

<html>
<style type="text/css">
	body {
		height:100%;
		margin:0px;
		padding:0px;
		background-color:#f4f4f4;
		font-family: "Helvetica Neue", Arial, Helvetica, sans-serif;
	}

	h1 {
		color: #999;
		font-size: 24px;
		margin-bottom: 10px;
	}

	h2 {
		margin-top: 0;
		color: #999;
		font-size: 15px;
		font-weight: normal;
	}

	address { font-style: normal; }
	fieldset { border: none; border-top: 1px solid #dadada; margin: 20px 40px 20px 0; }
	fieldset legend { display: block; font-weight: bold; color: #999; }
	fieldset span { display: block; }
	table { clear: both; }
	table.transaction th { text-align: left; }
	.labels { width: 100%; }
	table.labels td { vertical-align: top; }
	h1 { margin-bottom: 0; }
	p { margin-bottom: 24px; }

	.order { width: 100%; overflow: hidden; border: none; }
	.order td { border: none; }
	.order th { font-weight: bold; text-align: left; }
	.order .item { width: 50%; }
	.order td.qty { text-align: center; }
	.order .money,
	.order .total, .order .buttons td { text-align: right; }
	.order .remove { font-size: 12px; }
	.order tr.totals th,.order tr.totals td { padding: 10px 0 0 0; }

	div.content {
		padding: 40px;
		margin: 20px;
		border-color: #f6f6f6;
		border-width: 1px 1px 1px 1px;
		border-style: solid;
		background-color: #ffffff;
	}

	div.spacer {
		height: 20px;
		width: 100%;
		text-align: center;
		color: #999;
		padding-bottom: 10px;
	}

	img {
		border: none;
	}

	table {
		width: 100%;
	}
	a.primary-button {
		background: #EEB111;
		border: 1px solid #D29A07;
		border-radius: 4px;
		color: white;
		display: inline;
		float: right;
		font-family: Arial;
		font-size: 12px;
		font-weight: lighter;
		padding: 6px 0;
		padding-left: 8px;
		padding-right: 8px;
		text-decoration: none;
	}

	a.primary-button:hover {
		background: #D29A07;
	}
</style>
<body>
	<table>
		<tr>
			<td align="center">
			<a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/mandg-logo.png" /></a>
			</td>
		</tr>
	</table>
	<div class="content">
		<?php echo $email_data['message']; ?>
	</div>
	<div class="spacer">This is an automatic e-mail. For any questions, please call 1-800-737-0168 or you may reply to this e-mail.</div>
</body>
</html>