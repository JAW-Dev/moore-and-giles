<div id="sample_requests">
	<form action="<?php shopp( 'checkout.url' ); ?>" method="post" class="shopp validate validation-alerts" id="checkout">
		<?php shopp( 'checkout.function' ); ?>
		<input type="hidden" name="<?php echo MG_SampleRequests_Process::$action_field_name; ?>" value="<?php echo MG_SampleRequests_Process::$action_field_value; ?>" />

        <div class="sample-request-form section">
            <h3>Articles<br/>
                <small>Sizes over 20x20 require pre-approval. To request a ring set, pick any color of the desired leather collection, <br/>select "Other" and write "Ring Set" for the size.</small>
            </h3>

            <h4>Quick Add: Quickly search for and add multiple articles</h4>
            <div class="sample-selector-wrap" style="margin-bottom: 2em;">
				<select class="sample-selector" multiple="multiple"></select>
			</div>

            <div class="row headings">
                <div class="one-third first">
                    <h4>Article Name / Color</h4>
                </div>
                <div class="one-third">
                    <h4>Size</h4>
                </div>
                <div class="one-sixth">
                    <h4>Quantity</h4>
                </div>
                <div class="one-sixth">&nbsp;</div>
            </div>

			<?php if ( shopp('cart.has-items') ): ?>
				<?php while( shopp('cart.items') ): ?>
					<?php MG_SampleRequests_Frontend::row( shopp('cartitem.get-id'), shopp('cartitem.get-product','priceline=true') . "|" . shopp('cartitem.get-product'), shopp('cartitem.get-name') . " - " . shopp('cartitem.get-optionlabel'), "3x5", shopp( 'cartitem.get-quantity' ) ); ?>
				<?php endwhile; ?>
			<?php else: ?>
				<?php MG_SampleRequests_Frontend::row(1); ?>
			<?php endif; ?>



            <div class="clear"></div>
            <div class="add-row-container">
                <a class="add-row" href="#"><i class="fa fa-plus-square"></i> Add Article</a>
            </div>
        </div>

        <div class="sample-request-form-custom section">
            <h3>Additional Articles<br/><small>Articles not listed in our online catalog. Click <i>Add Article</i> below to begin.</small></h3>
            <div class="custom-row headings">
                <div class="one-sixth first">
                    <h4>Article Name</h4>
                </div>
                <div class="one-sixth">
                    <h4>Color</h4>
                </div>
                <div class="one-third">
                    <h4>Size</h4>
                </div>
                <div class="one-sixth">
                    <h4>Quantity</h4>
                </div>
                <div class="one-sixth">&nbsp;</div>
            </div>

            <div class="clear"></div>
            <div class="add-row-container">
                <a class="add-custom-row" href="#"><i class="fa fa-plus-square"></i> Add Article</a>
            </div>

        </div>

        <div class="sample-request-form-custom-order section">
            <h3>Custom Samples<br/><small>All Custom Samples are paid for by client and purchase will be applied to final order. Click <i>Add Article</i> below to begin.</small></h3>
            <div class="custom-order-row headings" style="display:none">
                <div class="one-sixth first">
                    <h4>Pattern</h4>
                </div>
                <div class="one-third">
                    <h4>Base Leather</h4>
                </div>
                <div class="one-sixth">
                    <h4>Tipping</h4>
                </div>
                <div class="one-sixth">
                    <h4>Quantity</h4>
                </div>
                <div class="one-sixth">&nbsp;</div>
            </div>

            <div class="clear"></div>
            <div class="add-row-container">
                <a class="add-custom-order-row" href="#"><i class="fa fa-plus-square"></i> Add Article</a>
            </div>

        </div>

        <div class="sample-request-form-boxes section" style="display:none">
            <h3>Boxes</h3>
            <div class="box-row headings">
                <div class="one-sixth first">
                    <h4>Box Type</h4>
                </div>
                <div class="one-sixth">
                    <h4>Quantity</h4>
                </div>
                <div class="one-sixth">&nbsp;</div>
            </div>

            <div class="clear"></div>
            <div class="add-row-container">
                <a class="add-custom-box" href="#"><i class="fa fa-plus-square"></i> Add a Box</a>
            </div>

            <p>Please allow up to 3 weeks for box orders. All box orders ship ground.</p>
        </div>

		<div class="contact-information section">
			<h3 style="margin-top: 1em;"><?php _e( 'Your Contact Information', 'Shopp' ); ?></h3>

			<div>
				<div class="one-half first">
						<span><label for="firstname">First</label><?php shopp('checkout','firstname','required=true&minlength=2&size=8&title=First Name'); ?><span class="required-notification">* Required</span></span>
				</div>
				<div class="one-half">
					<span><label for="lastname">Last</label><?php shopp('checkout','lastname','required=true&minlength=2&size=14&title=Last Name'); ?><span class="required-notification">* Required</span></span>
				</div>
			</div>

			<div class="one-half first">
				<span><label for="company">Company/Organization</label><?php shopp('checkout','company','size=22&title=Company/Organization'); ?></span>
			</div>
			<div class="one-half">
				<span><label for="phone">Phone</label><?php shopp('checkout','phone','format=phone&size=15&title=Phone'); ?></span>
			</div>

			<div class="one-half first">
				<span><label for="email">Email</label><?php shopp('checkout','email','required=true&format=email&size=30&title=Email'); ?><span class="required-notification">* Required</span></span>
			</div>

			<div class="one-half">
				<span><label for="order-data-representative">For a Moore and Giles Representative:</label><?php shopp('checkout','order-data','name=Representative&title=Representative&type=text'); ?></span>
			</div>
			<div class="clear"></div>
		</div>

		<div class="section">
			<h3>Side Mark / Reference No / Project Name<br/><small>i.e., New York Hilton</small></h3>

			<div class="one-half first">
				<div>
					<span>
					<?php shopp('checkout','order-data','name=Side Mark&type=text&required=true'); ?><span class="required-notification">* Required</span></span>
				</div>
				<div class="division">
					<span><label for="division">Division:</label></span>
					<select id="division" name="data[Division]" class="required">
						<option value="">Select...</option>
						<option>00 Applies to all cost centers</option>
						<option>01 Res (Residential)</option>
						<option>02 Hos (Hospitality)</option>
						<option>03 Acc (Accessories)</option>
						<option>04 Avi (Aviation)</option>
						<option>05 RDes (Residential Design)</option>
						<option>07 Life (Lifestyle)</option>
						<option>08 (Footwear)</option>
                        <option>10 (Automotive)</option>
					</select>
					<span class="required-notification select">* Required</span>
				</div>

			</div>
			<div class="clear"></div>
		</div>

		<div class="shipping-address section">
			<h3><?php _e( 'Recipient Information', 'Shopp' ); ?></h3>

			<div class="two-thirds first section">
				<h4>Who are you requesting samples for?</h4>

				<?php
				$recipient_options = array(
					'Yourself' => true,
					'Existing Customer' => false,
					'New Customer' => false,
				);
				?>

				<?php foreach ( $recipient_options as $name => $op ): ?>
					<?php
					$checked = "false";
					if ( ShoppOrder()->data['_intended_recipient'] == $name || ( ! isset(ShoppOrder()->data['_intended_recipient']) && $op == true ) )
						$checked = "true";
					?>

					<label class="radio"><?php shopp( 'checkout.order-data', "name=_intended_recipient&type=radio&value=$name&class=order-data-intended-recipient&checked=$checked" ); ?> <?php echo $name; ?></label>
				<?php endforeach; ?>

				<?php
				if ( isset(ShoppOrder()->data['_selected_customer_address']) ) {
					$value = ShoppOrder()->data['_selected_customer_address'];
				} else {
					$value = ShoppOrder()->Customer->id;
				}
				$loaded_customer = shopp_customer($value);
				?>
				<div class="selected-customer-address-container">
					<select name="data[_selected_customer_address]" id="order-data-_selected_customer_address">
						<option value="<?php echo $value; ?>"><?php echo $loaded_customer->firstname . " " . $loaded_customer->lastname; ?></option>
					</select>
				</div>
			</div>
			<div class="clear"></div>

			<h4>Shipping Address</h4>
			<div>
				<div class="one-half first">
					<label for="shipping-address"><?php _e( 'Name', 'Shopp' ); ?></label>
					<?php shopp( 'checkout.shipping-name', 'required=true&title=' . __( 'Ship to', 'Shopp' ) ); ?>
					<span class="required-notification">* Required</span>
				</div>

				<div class="one-half">
					<label for="order-data-shipping-phone">Phone</label>
					<?php  shopp('checkout','order-data', "name=Shipping Phone&type=text&format=phone&size=20&required=true&minlength=10"); ?>
					<span class="required-notification">* Required</span>
				</div>

				<div class="one-half first">
					<label for="order-data-recipient-company">Company</label>
					<?php  shopp('checkout','order-data', "name=Recipient Company&type=text&size=20&minlength=3"); ?>
				</div>

				<div class="one-half">
					<label for="order-data-recipient-email">Email&nbsp;&nbsp;<small>Separate multiple email addresses with commas.</small></label>
					<?php  shopp('checkout','order-data', "name=Recipient Email&type=text&format=text&size=20&minlength=6&required=true"); ?>
					<span class="required-notification">* Required</span>
				</div>
			</div>

			<div>
				<label for="shipping-address"><?php _e( 'Street Address', 'Shopp' ); ?></label>
				<?php shopp( 'checkout.shipping-address', 'required=true&title=' . __( 'Shipping street address', 'Shopp' ) ); ?>
				<span class="required-notification">* Required</span>
			</div>
			<div class="address-line-2">
				<label for="shipping-xaddress"><?php _e( 'Address Line 2', 'Shopp' ); ?></label>
				<?php shopp( 'checkout.shipping-xaddress', 'title=' . __( 'Shipping address line 2', 'Shopp' ) ); ?>
			</div>

			<div class="one-half first">
				<label for="shipping-city"><?php _e( 'City', 'Shopp' ); ?></label>
				<?php shopp( 'checkout.shipping-city', 'required=true&title=' . __( 'City shipping address', 'Shopp' ) ); ?>
				<span class="required-notification">* Required</span>
			</div>
			<div class="one-half">
				<label for="shipping-state"><?php _e( 'State / Province', 'Shopp' ); ?></label>
				<?php shopp('checkout.shipping-state'); ?>
				<span class="required-notification">* Required</span>
			</div>
			<div class="one-half first">
				<label for="shipping-postcode"><?php _e( 'Zip / Postal Code', 'Shopp' ); ?></label>
				<?php shopp( 'checkout.shipping-postcode', 'required=true&title=' . __( 'Postal/Zip Code shipping address', 'Shopp' ) ); ?>
				<span class="required-notification">* Required</span>
			</div>
			<div class="one-half">
				<label for="shipping-country"><?php _e( 'Country', 'Shopp' ); ?></label>
				<?php shopp( 'checkout.shipping-country', 'required=true&title=' . __( 'Country shipping address', 'Shopp' ) ); ?>
				<span class="required-notification select">* Required</span>
			</div>
			<div class="clear"></div>
			<input type="hidden" name="sameaddress" value="billing" id="same-address-billing" class="sameaddress billing">
		</div>

		<div class="verify-address section">
			<p class='continue-button'>
				<a href="#" id="validate_shipping_address" class="primary-button">Verify Shipping Address to Continue...</a>
			</p>
			<div class="verify-address-message">
				<div class="message"></div>

				<div class="continue-button exception-message">
					<p>If you are confident the shipping address provided above is correct, you can click the following link to skip address verification:</p>
					<a href="#" id="force_address_validate">I certify that provided shipping address is correct.</a>
				</div>
			</div>
		</div>

		<div class="lockable-area locked-area">

			<div class="section">
				<div class="clear"></div>
				<h3>Special Requests</h3>

				<?php shopp('checkout','order-data','name=Special Requests&type=textarea'); ?>
			</div>

			<div class="additional-info section">
				<h3>Shipping Method</h3>
				<div class="one-half first">
					<h4>Requested Shipping Method</h4>
					<label class="radio"><?php shopp( 'checkout.order-data', 'name=Shipping Method&type=radio&value=Ground&class=order-data-shipping-method&checked=true' ); ?> Ground</label>
					<label class="radio"><?php shopp( 'checkout.order-data', 'name=Shipping Method&type=radio&value=2 Day&class=order-data-shipping-method' ); ?> 2 Day</label>
					<label class="radio"><?php shopp( 'checkout.order-data', 'name=Shipping Method&type=radio&value=Overnight&class=order-data-shipping-method' ); ?> Overnight</label>

					<p>
						<ul>
							<li>Ground: Processes in 72 hours and ships ground.</li>
							<li>2 Day: Processes same day and ships 2-day.</li>
							<li>Overnight: Processes same day and ships overnight.</li>
						</ul>
					</p>
					<p>Overnight orders placed after 4pm EST will be processed the next business day</p>
				</div>

				<div class="one-half">
					<span><label for="order-data-special-shipping-instructions">Special Shipping Instructions</label>
					<?php shopp('checkout','order-data','name=Special Shipping Instructions&type=textarea'); ?></span>

					<div id="shipping_number_container" style="margin-top: 20px;">
						<span><label>Customer UPS / FedEx / DHL Number</label>
						<?php shopp( 'checkout.order-data', 'name=Customer UPS / FedEx / DHL Number&type=text' ); ?></span>
					</div>
				</div>

				<div class="clear"></div>
			</div>

			<div class="clear"></div>

			<div>
				<input type="submit" value="Submit Request" class="right primary-button" />
			</div>

		</div>
	</form>
</div>
