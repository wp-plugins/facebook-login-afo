<?php
class fb_login_wid extends WP_Widget {
	private $appId,$appSecret;
	public function __construct() {
		include_once dirname( __FILE__ ) . '/facebook/facebook.php';
		$this->appId = get_option('afo_fb_app_id');
		$this->appSecret = get_option('afo_fb_app_secret');
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		parent::__construct(
	 		'fb_login_wid',
			'FB Login Widget AFO',
			array( 'description' => __( 'This is a simple login form in the widget.', 'text_domain' ), )
		);
	 }

	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->loginForm();
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = $instance[ 'wid_title' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<?php 
	}
	
	public function loginForm(){
		global $post;
		$this->error_message();
		$this->LoadScript();
		if(!is_user_logged_in()){
		?>
		<form name="login" id="login" method="post" action="">
		<input type="hidden" name="option" value="afo_user_login" />
		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
			<ul style="list-style-type:none;">
			<li>Username</li>
			<li><input type="text" name="user_username" required="required"/></li>
			<li>Password</li>
			<li><input type="password" name="user_password" required="required"/></li>
			<li>&nbsp;</li>
			<li><input name="login" type="submit" value="Login" /></li>
			<li>&nbsp;</li>
			<li><a href="javascript:void(0)" onClick="FBLogin();"><img src="<?php echo plugins_url( 'facebook.png' , __FILE__ );?>" alt="Fb Connect" title="Login with facebook" /></a></li>
			</ul>
		</form>
		<?php 
		} else {
		global $current_user;
     	get_currentuserinfo();
		$link_with_username = 'Howdy, '.$current_user->display_name;
		?>
		<ul style="list-style-type:none;">
			<li><?php echo $link_with_username;?> | <a href="<?php echo wp_logout_url(site_url()); ?>" title="Logout">Logout</a></li>
		</ul>
		<?php 
		}
	}
	
	private function LoadScript(){
	?>
	<script type="text/javascript">
window.fbAsyncInit = function() {
	FB.init({
	appId      : "<?php echo $this->appId?>", // replace your app id here
	status     : true, 
	cookie     : true, 
	xfbml      : true  
	});
};
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

function FBLogin(){
	FB.login(function(response){
		if(response.authResponse){
			window.location.href = "<?php echo site_url();?>?option=fblogin";
		}
	}, {scope: 'email,user_likes'});
}
</script>
	<?php
	}
	
	public function error_message(){
		if($_SESSION['msg']){
			echo '<div class="'.$_SESSION['msg_class'].'">'.$_SESSION['msg'].'</div>';
			unset($_SESSION['msg']);
			unset($_SESSION['msg_class']);
		}
	}
	
	public function register_plugin_styles() {
		wp_enqueue_style( 'style_login_widget', plugins_url( 'fb-login-widget/style_login_widget.css' ) );
	}
	
} 

function fb_login_validate(){
	if($_POST['option'] == "afo_user_login"){
		global $post;
		if($_POST['user_username'] != "" and $_POST['user_password'] != ""){
			$creds = array();
			$creds['user_login'] = $_POST['user_username'];
			$creds['user_password'] = $_POST['user_password'];
			$creds['remember'] = true;
		
			$user = wp_signon( $creds, true );
			if($user->ID == ""){
				$_SESSION['msg_class'] = 'error_wid_login';
				$_SESSION['msg'] = 'Error in login!';
			} else{
				wp_redirect( site_url() );
				exit;
			}
		} else {
			$_SESSION['msg_class'] = 'error_wid_login';
			$_SESSION['msg'] = 'Username or password is empty!';
		}
		
	}
	
	
	if($_REQUEST['option'] == "fblogin"){
		global $wpdb;
		$appid 		= get_option('afo_fb_app_id');
		$appsecret  = get_option('afo_fb_app_secret');
		$facebook   = new Facebook(array(
			'appId' => $appid,
			'secret' => $appsecret,
			'cookie' => TRUE,
		));
		$fbuser = $facebook->getUser();
		if ($fbuser) {
			try {
				$user_profile = $facebook->api('/me');
			}
			catch (Exception $e) {
				echo $e->getMessage();
				exit();
			}
			$user_fbid	= $fbuser;
			$user_email = $user_profile["email"];
			$user_fnmae = $user_profile["first_name"];
  
		  if( email_exists( $user_email )) { // user is a member 
			  $user = get_user_by('login', $user_email );
			  $user_id = $user->ID;
			  wp_set_auth_cookie( $user_id, true );
		   } else { // this user is a guest
			  $random_password = wp_generate_password( 10, false );
			  $user_id = wp_create_user( $user_email, $random_password, $user_email );
			  wp_set_auth_cookie( $user_id, true );
		   }
		   
   			wp_redirect( site_url() );
			exit;
   
		}		
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget( "fb_login_wid" );' ) );
add_action( 'init', 'fb_login_validate' );
?>