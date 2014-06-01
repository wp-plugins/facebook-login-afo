<?php
/*
Plugin Name: Facebook Login Widget
Plugin URI: http://avifoujdar.wordpress.com/category/my-wp-plugins/
Description: This is a facebook login plugin as widget. This widget also supports default wordpress user login. 
Version: 1.0.1
Author: avimegladon
Author URI: http://avifoujdar.wordpress.com/
*/

/**
	  |||||   
	<(`0_0`)> 	
	()(afo)()
	  ()-()
**/

include_once dirname( __FILE__ ) . '/login_afo_widget.php';


class afo_fb_login {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'facebook_login_widget_afo_menu' ) );
		add_action( 'admin_init',  array( $this, 'facebook_login_widget_afo_save_settings' ) );
	}
	
	function  fb_login_widget_afo_options () {
		global $wpdb;
		$afo_fb_app_id = get_option('afo_fb_app_id');
		$afo_fb_app_secret = get_option('afo_fb_app_secret');
		
		$this->donate_form_facebook_login();
		?>
		
		<form name="f" method="post" action="">
		<input type="hidden" name="option" value="login_widget_afo_save_settings" />
		<table width="100%" border="0"> 
		<tr>
			<td colspan="2"><p>There is also a PRO version of this plugin that supports login with <strong>Facebook</strong>, <strong>Google</strong> And <strong>Twitter</strong>. You can get it <a href="#" target="_blank">here</a> in <strong>USD 1.00</strong> </p></td>
		  </tr>
		  <tr>
			<td width="45%"><h1>Facebook Login Widget</h1></td>
			<td width="55%">&nbsp;</td>
		  </tr>
		  <tr>
			<td><strong>App ID:</strong></td>
			<td><input type="text" name="afo_fb_app_id" value="<?php echo $afo_fb_app_id;?>" /></td>
		  </tr>
		  
		   <tr>
			<td><strong>App Secret:</strong></td>
			 <td><input type="text" name="afo_fb_app_secret" value="<?php echo $afo_fb_app_secret;?>" /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /></td>
		  </tr>
		  <tr>
			<td colspan="2"><?php $this->fb_login_help();?></td>

		  </tr>
		</table>
		</form>
		<?php 
	}
	
	function fb_login_help(){ ?>
		<p><font color="#FF0000"><strong>Note*</strong></font>
			    <br />
		      You need create a new facebook API Applitation to setup this plugin. Please follow the instructions provided below.
			</p>
			  <p>
			  <strong>1.</strong> Go to <a href="https://developers.facebook.com/" target="_blank">https://developers.facebook.com/</a> <br /><br />
			  <strong>2.</strong> Click on Create a new app button. A popup will open.<br /><br />
              <strong>3.</strong> Add the required informations and don't forget to make your app live. This is very important otherwise your app will not work for all users.<br /><br />
			  <strong>4.</strong> Then Click the "Create App" button and follow the instructions, your new app will be created. <br /><br />
			  <strong>5.</strong> Copy and Paste "App ID" and "App Secret" here. <br /><br />
			  <strong>6.</strong> That's All. Have fun :)
			  </p>
			  
	<?php }
	
	function facebook_login_widget_afo_save_settings(){
		if($_POST['option'] == "login_widget_afo_save_settings"){
			update_option( 'afo_fb_app_id', $_POST['afo_fb_app_id'] );
			update_option( 'afo_fb_app_secret', $_POST['afo_fb_app_secret'] );
		}
	}
	
	function facebook_login_widget_afo_menu () {
		add_options_page( 'FB Login Widget', 'FB Login Widget', 1, 'fb_login_widget_afo', array( $this, 'fb_login_widget_afo_options' ));
	}
	
	function donate_form_facebook_login(){?>
		<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55;">
		 <tr>
		 <td align="right"><h3>Even $0.60 Can Make A Difference</h3></td>
			<td><form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				  <input type="hidden" name="cmd" value="_xclick">
				  <input type="hidden" name="business" value="avifoujdar@gmail.com">
				  <input type="hidden" name="item_name" value="Donation for plugins (FB Login)">
				  <input type="hidden" name="currency_code" value="USD">
				  <input type="hidden" name="amount" value="0.60">
				  <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make a donation with PayPal">
				</form></td>
		  </tr>
		</table>
	<?php }
}
new afo_fb_login;