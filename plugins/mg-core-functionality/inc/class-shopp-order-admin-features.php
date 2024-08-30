<?php

class MG_ShoppOrderAdmin_Features {
	public function __construct() {
		// Add print and charge buttons
		add_action('shopp_manage_orders_before_actions', array($this, 'add_print_button') );

		if ( is_main_site() ) {
			add_action('shopp_manage_orders_before_actions', array($this, 'add_charge_button') );
		}

		if ( mg_is_leather_site() ) {
			add_action('shopp_manage_orders_before_actions', array($this, 'add_picklist_print_button') );
			add_action('load-toplevel_page_shopp-orders', array($this, 'sort_leather_orders'), 100 );
        }

		// Add action links
		add_action('shopp_manage_orders_column_order_after', array($this, 'add_admin_invoice'), 10, 1 );
		add_action('shopp_manage_orders_column_order_after', array($this, 'add_charge_order'), 10, 1 );

		// Add Order Balance Column
		if ( is_main_site() ) {
			add_filter('manage_toplevel_page_shopp-orders_columns', array($this, 'add_order_balance_column'), 100, 1 );
			add_action('shopp_manage_orders_order_balance_column', array($this, 'order_balance_column'), 10, 2 );
		}

		// Handlers
		add_action('shopp_init', array($this,'handle_print_outs'), 100 );
		add_action('shopp_init', array($this, 'detect_order_charge'), 101);
		add_action('shopp_init', array($this, 'detect_bulk_charge'), 102 );
		add_action('shopp_init', array($this, 'detect_pick_list_print'), 103);
		add_action('admin_footer', array($this, 'detect_bulk_print') );

		// Scripts
		add_action('admin_enqueue_scripts', array($this, 'add_scripts') );
	}

	function add_print_button() {
		?>
		<div class="alignleft actions">
			<button type="submit" id="print-button" name="bulk-print" value="order" class="button-secondary"><?php _e('Print Orders'); ?></button>
		</div>
		<?php
	}

	function add_picklist_print_button() {
	    ?>
		<div class="alignleft actions">
			<button type="submit" id="print-button" name="bulk-pick-list-print" value="order" class="button-secondary"><?php _e('Print Pick List'); ?></button>
        </div>
        <?php
    }

	function add_charge_button() {
		$nonce = wp_create_nonce( "bulk_charge_orders" );
		?>
		<div class="alignleft actions">
			<button type="submit" id="charge-button" onclick="return confirm('Are you sure you want to charge these orders?')" name="charging" value="<?php echo $nonce; ?>" class="button-secondary"><?php _e('Charge Orders'); ?></button>
		</div>
		<?php
	}

	function add_admin_invoice( $Order ) {
		$gift = shopp_meta( $Order->id, 'purchase', 'Gift', 'order-data' ); ?>

		<br/>

		<?php if ( $gift == "Yes" ): ?>
			<a class="modaal-iframe" href="?page=shopp-print-items&type=gift_receipt&order=<?php echo $Order->id; ?>">Printable Invoice (Gift)</a>
		<?php else: ?>
			<a class="modaal-iframe" href="?page=shopp-print-items&amp;type=invoice&amp;order=<?php echo $Order->id; ?>">Printable Invoice</a>
		<?php endif; ?>
		<?php
	}

	function add_charge_order( $Order ) {
		$Purchase = shopp_order ( $Order->id );
		$Gateway = $Purchase->gateway();

		if ( current_user_can('shopp_capture') && ! $Purchase->captured && $Purchase->authorized && $Gateway->captures ): ?>
			| <a href="<?php echo wp_nonce_url( add_query_arg( array('charge' => 'true', 'order' => $Purchase->id ) ), 'charge_order' ); ?>" onclick="return confirm('Are you sure you want to charge order #<?php echo $Purchase->id; ?>?')" >Charge Order</a>
		<?php elseif ( $Purchase->captured ): ?>
			| Charged
		<?php endif;
	}

	function detect_order_charge( $order = false ) {
		if ( ! isset($_GET['order']) ) return;

		if ( isset($_GET['charge']) ) {
			if ( ! isset($_GET['_wpnonce']) || ! wp_verify_nonce($_GET['_wpnonce'], 'charge_order') ) {
				wp_die('You must be using an old link.');
			}

			if ( ! current_user_can('shopp_capture') ) {
				wp_die(__('You do not have sufficient permissions to carry out this action.'));
			}

			$this->charge_order( $_GET['order'] );

			wp_redirect( remove_query_arg( array('charge','order','_wpnonce') ) );
		}
	}

	function charge_order( $order = false ) {
		if ( ! $order ) return;

		$Purchase = shopp_order( $order );
		$Gateway = $Purchase->gateway();

		if ( $Gateway && $Gateway->captures && current_user_can('shopp_capture') ) {
			$user = wp_get_current_user();

			shopp_add_order_event($Purchase->id, 'capture', array(
				'txnid'   => $Purchase->txnid,
				'gateway' => $Purchase->gateway,
				'amount'  => $Purchase->capturable(),
				'user'    => $user->ID
			));

			$Purchase->load_events();
		}
	}

	function handle_print_outs() {
		if ( ! isset($_GET['order']) || ! isset($_GET['type']) || ! isset($_GET['page']) ) return;
		if ( $_GET['page'] != 'shopp-print-items' ) return;

		$order = $_REQUEST['order'];

		$this->print_out( $order, $show_print = true );

		die;
	}

	function print_out( $order, $show_print = false ) {
		$Purchase = ShoppPurchase( shopp_order($order) );
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php shopp('purchase','email-subject'); ?></title>
		</head>

		<body itemscope itemtype="http://schema.org/EmailMessage">
		<style type="text/css">
			<?php
			$cssfile = Shopp::locate_template(array('email.css'));
			echo file_get_contents($cssfile);
			?>
            .body-wrap .content {
                background: #fff;
            }
			.main {
			  background-color: #fff;
			  border: none;
			  border-radius: 3px;
			}

			.content-wrap {
			  padding: 0px;
			}
			#print_section {
				text-align: center;
			}
			@media print {
				#print_section {
					display: none;
				}

				table.body-wrap {page-break-after: always;}
			}
		</style>

		<table class="body-wrap">
			<tr>
				<td class="container" width="600">
					<?php if ( $show_print ): ?>
					<div id="print_section">
						<button id="print_link" onclick="window.print();">Print Order (Not Visible On Print Out)</button>
					</div>
					<?php endif; ?>

					<div class="content">

						<?php include( get_stylesheet_directory() . '/shopp/partials/email-header.php'); ?>

						<table class="main" width="100%" cellpadding="0" cellspacing="0">

							<tr>
								<td class="content-wrap aligncenter">

									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td class="content-block alignleft">
												<h1 class="alignleft">Order <?php shopp('purchase.id'); ?></h1>
											</td>
										</tr>
									</table>

									<?php if ( $_GET['type'] == "gift_receipt" ): ?>
										<?php shopp('purchase','receipt','template=receipt-gifts.php'); ?>
									<?php else: ?>
                                        <?php if ( mg_is_leather_site() ) shopp('purchase', 'sort-items'); ?>

										<?php shopp('purchase','receipt','template=receipt-merchant.php'); ?>
									<?php endif; ?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		</body>
		</html>
		<?php
	}

	function add_order_balance_column( $columns ) {
		$columns['order_balance'] = "Balance";

		return $columns;
	}

	function order_balance_column( $column, $Order ) {
		$Purchase = shopp_order ( $Order->id );

		$format = '%s';

		if ( $Purchase->balance > 0 ) {
			$format = '<span style="color:red">%s</span>';
		}

		echo sprintf($format, money($Purchase->balance) );
	}

	function add_scripts() {
		wp_register_style( 'modaal-css', MG_CF_URL . '/lib/modaal/css/modaal.min.css', false, MG_CF_VERSION );
		wp_register_script( 'modaal', MG_CF_URL . '/lib/modaal/js/modaal.min.js', false, MG_CF_VERSION );
		wp_register_script( 'mg-cf-admin', MG_CF_URL . '/js/admin.js', array('jquery', 'modaal'), MG_CF_VERSION );

		wp_enqueue_style( 'modaal-css' );
		wp_enqueue_script( 'modaal' );
		wp_enqueue_script( 'mg-cf-admin' );
	}

	function detect_bulk_print() {
		if ( ! empty($_REQUEST['bulk-print']) && ! empty($_REQUEST['selected']) ) {

			$output = '';

			$orders = $_REQUEST['selected'];

			foreach( $orders as $o ) {
				ob_start();
				$this->print_out( $o );

				$output .= ob_get_clean();
			}

			$output = esc_attr($output);
			echo "<iframe style='visibility: hidden; height: 0; width: 0;' id='bulk-print' srcdoc='$output'></iframe>";
		}
	}

	function detect_pick_list_print() {
        if ( ! empty($_REQUEST['bulk-pick-list-print']) && ! empty($_REQUEST['selected']) ) {

	        $output = '';
            $combined_purchased = [];
            $orders = $_REQUEST['selected'];

	        foreach( $orders as $o ) {
		        $Purchase = shopp_order($o);
		        $combined_purchased = array_merge($combined_purchased, $Purchase->purchased);
	        }

	        usort($combined_purchased, array($this,'cmp') );

            ob_start();
	        $this->pick_list_print_out( $combined_purchased );
	        $output = ob_get_clean();

	        $output = esc_attr($output);

	        echo "<iframe style='visibility: hidden; height: 0; width: 0;' id='bulk-print' srcdoc='$output'></iframe>";
        }
    }

	function cmp($a, $b) {
		return strcmp($a->name . ' ' . $a->optionlabel, $b->name . ' ' . $b->optionlabel);
	}

	function pick_list_print_out( $combined_purchased ) {
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>Pick List</title>
        </head>

        <body itemscope itemtype="http://schema.org/EmailMessage">
        <style type="text/css">
            <?php
			$cssfile = Shopp::locate_template(array('email.css'));
			echo file_get_contents($cssfile);
			?>
            .body-wrap .content {
                background: #fff;
            }
            .main {
                background-color: #fff;
                border: none;
                border-radius: 3px;
            }

            .content-wrap {
                padding: 0px;
            }
            #print_section {
                text-align: center;
            }
            @media print {
                #print_section {
                    display: none;
                }

                table.body-wrap {page-break-after: always;}
            }
        </style>

        <table class="body-wrap">
            <tr>
                <td class="container" width="600">

                    <div class="content">

						<?php include( get_stylesheet_directory() . '/shopp/partials/email-header.php'); ?>

                        <table class="main" width="100%" cellpadding="0" cellspacing="0">

                            <tr>
                                <td class="content-wrap aligncenter">

                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="content-block alignleft">
                                                <h1 class="alignleft">Pick List</h1>
                                            </td>
                                        </tr>
                                    </table>

                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="content-block alignleft">
                                                <table class="invoice">
					                                <?php if ( count($combined_purchased) > 0 ): ?>
                                                        <tr>
                                                            <td>
                                                                <table class="invoice-items" cellpadding="0" cellspacing="0">
									                                <?php foreach ( $combined_purchased as $purchased ): ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo $purchased->name; ?> – <?php echo $purchased->optionlabel; ?> x <?php echo $purchased->quantity; ?>
	                                                                            <?php echo $this->item_input_list( $purchased ); ?>
                                                                            </td>
                                                                            <td>Order: <?php echo $purchased->purchase; ?></td>
                                                                        </tr>
									                                <?php endforeach; ?>
                                                                </table>
                                                            </td>
                                                        </tr>
					                                <?php endif; ?>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        </body>
        </html>
		<?php
	}

	function item_input_list( $purchased ) {
		$item = $purchased;
		if ( empty($item->data) ) return false;
		$before = ''; $after = ''; $classes = ''; $excludes = array();
		if ( ! empty($options['class']) )   $classes  = ' class="' . $options['class'] . '"';
		if ( ! empty($options['exclude']) ) $excludes = explode(',', $options['exclude']);
		if ( ! empty($options['before']) )  $before   = $options['before'];
		if ( ! empty($options['after']) )   $after    = $options['after'];

		$result .= $before . '<ul' . $classes . '>';

		foreach ( $item->data as $name => $data ) {
			if ( in_array($name, $excludes) ) continue;
			$result .= '<li><strong>' . apply_filters('shopp_purchase_item_input_name', $name) . '</strong>: ' . apply_filters('shopp_purchase_item_input_data', $data, $name) . '</li>';
		}
		$result .= '</ul>' . $after;
		return $result;
    }

	function detect_bulk_charge() {
		if ( current_user_can('shopp_capture') && ! empty($_REQUEST['charging']) && ! empty($_REQUEST['selected']) ) {

			if ( ! wp_verify_nonce($_REQUEST['charging'], 'bulk_charge_orders') ) {
				wp_die("You don't have permission to bulk charge orders.");
			}

			$orders = $_REQUEST['selected'];

			foreach( $orders as $o ) {
				$this->charge_order( $o );
			}

			wp_redirect( remove_query_arg( array('charging','selected') ) );
		}
	}

	function sort_leather_orders() {
	    $Purchase = ShoppPurchase();

	    usort($Purchase->purchased, array($this,'cmp') );
    }
}
