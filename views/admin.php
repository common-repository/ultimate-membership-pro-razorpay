<?php
do_action( 'ump_admin_after_top_menu_add_ons' );
$pluginSlug = $data['plugin_slug'];

?>
<form action="" method="post">

	<div class="ihc-stuffbox">
		<?php if ( defined( 'UMP_RAZORPAY_PRO' ) && UMP_RAZORPAY_PRO ) :?>
			<h3 class="ihc-h3"><?php esc_html_e('RazorPay Pro Payment Service', 'ultimate-membership-pro-razorpay');?></h3>
		<?php else: ?>
			<h3 class="ihc-h3"><?php esc_html_e('RazorPay Payment Service', 'ultimate-membership-pro-razorpay');?></h3>
		<?php endif; ?>

		<div class="inside">
				<div class="iump-form-line" >
					<h4><?php esc_html_e('Activate ', 'ultimate-membership-pro-razorpay');?> RazorPay <?php esc_html_e(' Payment Service', 'ultimate-membership-pro-razorpay');?></h4>
		      <label class="iump_label_shiwtch ihc-switch-button-margin">
							<?php $checked = ($data[ $pluginSlug . '-enabled']) ? 'checked' : '';?>
							<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ump_rzr-enabled');" <?php esc_html_e("$checked", "ump_rzr");?> />

							<div class="switch ihc-display-inline"></div>

					</label>
						<input type="hidden" name="ump_rzr-enabled" value="<?php esc_html_e($data['ump_rzr-enabled']);?>" id="ump_rzr-enabled" />

							<p><?php esc_html_e('Once all Settings are properly done, Activate the Payment Service for further use.', 'ultimate-membership-pro-razorpay');?> </p>

					<?php	if ( !defined( 'UMP_RAZORPAY_PRO' ) || !UMP_RAZORPAY_PRO ) :?>
						<div class="ihc-alert-warning"><?php _e('To handle recurring Subscriptions management and charge recurring Payments, you must install the <b>RazorPay Pro</b> version, which is available ', 'ultimate-membership-pro-razorpay');?><a href="https://store.wpindeed.com/addon/razorpay-pro-payment-gateway/" target="_blank"><?php esc_html_e(' here', 'ultimate-membership-pro-razorpay');?>.</a></div>

									<?php endif;?>

					<?php if ( defined( 'UMP_RAZORPAY_PRO' ) && UMP_RAZORPAY_PRO ) :?>
							<?php // description for razorpay pro here ?>
					<?php else:?>
							<div class="iump-form-line">
								<h4>RazorPay <?php esc_html_e(' Capabilities', 'ultimate-membership-pro-razorpay');?></h4>
								<ul class="ihc-payment-capabilities-list">
									<li><?php esc_html_e('RazorPay support only single payments.', 'ultimate-membership-pro-razorpay');?> </li>
									<li><?php esc_html_e('RazorPay support coupons.', 'ultimate-membership-pro-razorpay');?> </li>
								</ul>
							</div>
					<?php endif;?>

				</div>

				<div class="ihc-wrapp-submit-bttn iump-submit-form">
						<input id="ihc_submit_bttn" type="submit" value="<?php _e('Save Changes', 'ultimate-membership-pro-razorpay' );?>" name="ihc_save" class="button button-primary button-large" />
				</div>

			</div>
		</div>


		<div class="ihc-stuffbox">
				<h3 class="ihc-h3"><?php esc_html_e('RazorPay Settings', 'ultimate-membership-pro-razorpay');?></h3>
				<div class="inside">

						<div class="iump-form-line">
							<div class="row ihc-row-no-margin">
										<div class="col-xs-5 ihc-col-no-padding">
												<div class="input-group">
								<span class="input-group-addon"><?php esc_html_e( 'Key Id', 'ultimate-membership-pro-razorpay' );?></span>
								<input class="form-control" type="text" name="ump_rzr-key" value="<?php esc_html_e( $data['ump_rzr-key'] );?>" />
							</div>
						</div>
					</div>
					<div class="row ihc-row-no-margin">
						<div class="col-xs-5 ihc-col-no-padding">
							<div class="input-group">
								<span class="input-group-addon"><?php esc_html_e( 'Secret Key', 'ultimate-membership-pro-razorpay');?></span>
								<input class="form-control" type="text" name="ump_rzr-secret" value="<?php esc_html_e( $data['ump_rzr-secret'] );?>" />
						</div>
					</div>
						</div>
						</div>
						<div class="iump-form-line">
								<?php
										$siteUrl = site_url();
										$siteUrl = trailingslashit( $siteUrl );
										$notifyUrl = add_query_arg( 'ihc_action', 'ump_rzr', $siteUrl );
								?>
								<ul class="ihc-payment-capabilities-list">
									<li><?php esc_html_e('Go to ', 'ultimate-membership-pro-razorpay');?> <a href="https://razorpay.com/" target="_blank">Razorpay</a> <?php esc_html_e(' and log in.', 'ultimate-membership-pro-razorpay');?></li>
										<li>
												<?php  esc_html_e( 'Once logged in you may find your API credentials here: ', 'ultimate-membership-pro-razorpay' );?>
												<a href="https://dashboard.razorpay.com/app/keys" target="_blank">https://dashboard.razorpay.com/app/keys</a>
										</li>

									<li><?php  _e( 'In Webhooks tab from <b>Settings</b> you may add new Webhook with ', 'ultimate-membership-pro-razorpay' );?> <b><?php esc_html_e("$notifyUrl", "ultimate-membership-pro-razorpay");?></b>
										<?php if ( defined( 'UMP_RAZORPAY_PRO' ) && UMP_RAZORPAY_PRO ) :?>
												<?php // events for razorpay pro
												_e(" and choose the following events from Active Events: <code>payment_link.paid</code>, <code>payment_link.cancelled</code>, <code>payment.failed</code>, <code>subscription.charged</code>, <code>subscription.cancelled</code>.", "ump_rzr");?>

										<?php else:?>
												<?php _e(" and choose the following events from Active Events: <code>payment_link.paid</code>, <code>payment_link.cancelled</code>, <code>payment.failed</code>.", "ump_rzr");?>
										<?php endif;?>
										</b></li>
										<li><?php _e('To see the currencies supported by Razorpay, check ', 'ultimate-membership-pro-razorpay');?><a target="_blank" href="https://razorpay.com/docs/payments/payments/accept-international-payments-from-india/#3-do-you-support-multiple-settlement-currencies"><?php _e(' List of Currencies currently supported', 'ultimate-membership-pro-razorpay');?></a></li>
								</ul>

						</div>

						<div class="iump-form-line">
								<h2><?php esc_html_e('Test Credentials (only on Test Mode)', 'ultimate-membership-pro-razorpay');?></h2>
								<p><?php esc_html_e('For Test/Sandbox mode use the next credentials available:', 'ultimate-membership-pro-razorpay');?></p>
								<a href="https://razorpay.com/docs/payments/payments/test-card-upi-details/" target="_blank">https://razorpay.com/docs/payments/payments/test-card-upi-details/</a>
							<div class="ihc-admin-register-margin-bottom-space"></div>

								<table class="ihc-test-crd">
									<tr>
										<th><?php esc_html_e('Description', 'ultimate-membership-pro-razorpay');?></th>
										<th><?php esc_html_e('Number', 'ultimate-membership-pro-razorpay');?></th>
									</tr>
									<tr>
										<td><?php esc_html_e('Credit Card: ', 'ultimate-membership-pro-razorpay');?></td>
										<td><code>5267318187975449</code></td>
									</tr>
									<tr>
										<td><?php esc_html_e('Expire Time: ', 'ultimate-membership-pro-razorpay');?></td>
										<td><code><?php esc_html_e(date('m/y', strtotime('+1 year') ));?></code></td>
									</tr>
									<tr>
										<td><?php esc_html_e('CVV', 'ultimate-membership-pro-razorpay');?></td>
										<td><code>123</code></td>
									</tr>

								</table>
						</div>

						<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input id="ihc_submit_bttn" type="submit" value="<?php _e('Save Changes', 'ultimate-membership-pro-razorpay' );?>" name="ihc_save" class="button button-primary button-large" />
						</div>
					</div>
			</div>

			<div class="ihc-stuffbox">
					<h3 class="ihc-h3"><?php esc_html_e('Extra Settings', 'ultimate-membership-pro-razorpay');?></h3>
					<div class="inside">
						<div class="row ihc-row-no-margin">
								<div class="col-xs-4">
										<div class="iump-form-line iump-no-border input-group">
											<span class="input-group-addon"><?php esc_html_e('Label:', 'ultimate-membership-pro-razorpay');?></span>
											<input type="text" name="ihc_ump_rzr_label" value="<?php esc_html_e($data['ihc_ump_rzr_label']);?>"  class="form-control" />
										</div>

										<div class="iump-form-line iump-no-border input-group">
											<span class="input-group-addon"><?php esc_html_e('Order:', 'ultimate-membership-pro-razorpay');?></span>
											<input type="number" min="1" name="ihc_ump_rzr_select_order" value="<?php esc_html_e($data['ihc_ump_rzr_select_order']);?>" class="form-control" />
										</div>
								</div>
						</div>
						<!-- developer -->
						<div class="row ihc-row-no-margin">
								<div class="col-xs-4">
									 <div class="input-group">
											<h4><?php esc_html_e('Short Description', 'ultimate-membership-pro-razorpay');?></h4>
											<textarea name="ihc_ump_rzr_short_description" class="form-control" rows="2" cols="125" placeholder="<?php esc_html_e('write a short description', 'ultimate-membership-pro-razorpay');?>"><?php esc_html_e( isset( $data['ihc_ump_rzr_short_description'] ) ? stripslashes($data['ihc_ump_rzr_short_description']) : '');?></textarea>
									 </div>
								</div>
						</div>
						 <!-- end developer -->
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ultimate-membership-pro-razorpay');?>" name="ihc_save" class="button button-primary button-large" />
					 </div>
					</div>
			</div>

</form>
<?php
