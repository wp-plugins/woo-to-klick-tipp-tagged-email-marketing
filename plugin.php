<?php 	 
$error 		= '';
$message 	= '';

$site_url	=	site_url();

function requestServer($datastring)
{
	$ch		=	curl_init('http://www.woocommerce-klick-tipp.com/klicktip-capi/check_license.php');
	curl_setopt($ch,CURLOPT_POST,true);				curl_setopt($ch,CURLOPT_POSTFIELDS,$datastring);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);	
	$output	=	curl_exec($ch);		
	curl_error($ch);
	return $output;	
}

if(isset($_GET['reset_sync']) && $_GET['reset_sync'] == '1'){
	global $wpdb;
	update_option('klicktippbridge_last_export','');
	$message = 'Time is reset, next cron will update all orders to Klick-Tipp';
}

	if(isset($_POST['license_submit'])){
	
			$license_email	=	trim($_POST['license_email']);
			$license_key	=	trim($_POST['license_key']);	
			if($license_email==''){					
				$error = "Enter Email";		
			}		
			else if($license_key==''){	
				$error	= 'Enter License Key';
			}			
			else{		
				
				$datastring = 'license_email='.$license_email.'&license_key='.$license_key.'&site_url='.$site_url;
				$yes		= requestServer($datastring);
			
				if($yes>0){		
					update_option('klicktip_license_email',$license_email);		
					update_option('klicktip_license_key',$license_key);			
					$message =  "Saved Successfully";			
				}		
				else{		
					update_option('klicktip_license_email',"");		
					update_option('klicktip_license_key',"");		
					$error =  "Wrong API Credentials";			
				}		
			}	
		}	
		else{	
			$license_email	=	get_option('klicktip_license_email');	
			$license_key	=	get_option('klicktip_license_key');		
		}
	
	/* klicktip account */
	if(isset($_POST['klicktip_submit'])) {
		$klicktip_username	=	trim($_POST['klicktip_username']);	
		$klicktip_password	=	trim($_POST['klicktip_password']);
		if($klicktip_username==''){				
			$error = "Enter Klick-Tipp Username";			
		}	
		else if($klicktip_password==''){	
			$error	= 'Enter Klick-Tipp Password';	
		}			
		else{		
			require ('klicktipp.api.php');
			$klicktip_username = $klicktip_username;
			$klicktip_password = $klicktip_password;			

			$connector = new KlicktippConnector();
			$correct_creds	=	$connector->login($klicktip_username, $klicktip_password);
			if($correct_creds){
				update_option('klicktip_username',$klicktip_username);	
				update_option('klicktip_password',$klicktip_password);	
				$message	=	'Klick-Tipp Account Saved Successfully';	
			}
			else{
				$error	= 'Wrong Klick-Tipp Credentials';	
			}
			
		}	
	}	
	else{
		$klicktip_username = get_option('klicktip_username');	
		$klicktip_password = get_option('klicktip_password');
	}	
	$datastring = 'license_email='.$license_email.'&license_key='.$license_key.'&site_url='.$site_url;
	$yes		= requestServer($datastring);
	
if(isset($_POST['klicktip_wordpress_cron_submit']))	{
	if(isset($_POST['klicktip_wordpress_cron'])){
		update_option('klicktip_wordpress_cron',1);
	}
	else{
		update_option('klicktip_wordpress_cron',0);
	}
	$message	=	'Cron Saved Successfully';
}
?>



<body>
<div id="hangout_main">

    <!-- Start Header -->
    <div class="gh_header">
		<div class="row-fluid">
			<div class="span6">
     			<div class="block_left">
                	WooCommerce to<span> Klick-Tipp Bridge</span>
                </div>
    		</div>
    		<div class="span6">
            	<div class="block_right">
					<?php 
					include_once 'klicktipp.api.php';
					$klicktip_username = $klicktip_username;
					$klicktip_password = $klicktip_password;			

					$connector = new KlicktippConnector();
					$correct_creds	=	$connector->login($klicktip_username, $klicktip_password);
					if($correct_creds){
					?>
     				<img src="<?php echo plugin_dir_url(__FILE__); ?>/img/activated.png" alt="" align="top" /> Sync active
					<?php 
					}
					else{
					?>
					<img src="<?php echo plugin_dir_url(__FILE__); ?>/img/deactivated.png" alt="" align="top" /> Sync deactivated
					<?php 
					}
					?>
                </div>
    		</div>
    	</div>
    </div>
    <!-- End Header -->
    <div class="hangout_activated">
		<?php 
				
		if ( !function_exists( 'woocommerce_get_page_id' ) )
			echo '<div class="error"><p>Please activate WooCommerce plugin for use of this plugin.</p></div>';
		else{		
		?>
    	<!-- Start Tabs -->
    	<div class="gh_tabs">
		<div class="row-fluid">
			<div class="span12">
     		<ul class="gh_tabs_list">
				<li class="span4"><a href="#hangouts_settings_panel"><span><i class="icon-time"></i></span>License </a></li>
				<li class="span4"><a href="#hangouts_panel"><span><i class="icon-envelope"></i></span>Klick-Tipp Account </a></li>
				<li class="span4"><a href="#email_settings_panel"><span><i class="icon-share"></i></span>Cron </a></li>      
			</ul>
    		</div>
    	</div>
    	</div>
    	<!-- End Tabs -->
    <!-- Start Container -->
    <div class="gh_container">		
		<div class="row-fluid">		
			<div class="span12">
   <?php 
   if($error)
   {
   	echo '<div class="error"><p>'.$error.'</p></div>';	
   	}
   	if($message)
   	{
		echo '<div class="updated"><p>'.$message.'</p></div>';	
	}
		?>
				<!-- Start Hangouts Settings Panel -->
     			<div id="hangouts_settings_panel" class="gh_tabs_div">
                	<div class="gh_container_header">				
					<?php 
					 $version = '';
						if($yes>0){
							$version	= 'Premium Version active';
						}else{	
							$version	= 'Free Version, <a target="_blank" href="http://woocommerce-klick-tipp.com">Upgrade to Pro to get full data sync</a>';						
						}
					?>	
                    	<strong>License</strong> <?php echo $version; ?>
                    </div>
                    	<form action="" method="post" class="hangouts_form">
                    	<div class="gh_tabs_div_inner">
                        <div class="row-fluid-outer">
                        <div class="row-fluid">
							<div class="span4">
							
								<strong>Email</strong> 
                            </div>
                            <div class="span8">
								<input type="text" class="longinput" id="license_email" name="license_email" value="<?php echo $license_email ?>">
                            </div>
                        </div>
                        </div>
                        <div class="row-fluid-outer">
                        <div class="row-fluid">
							<div class="span4">
								<strong>License Key</strong>
                            </div>
                            <div class="span8">
								<input type="text" class="longinput" id="license_key" name="license_key" value="<?php echo $license_key; ?>" />
                            </div>
                        </div>
                        </div>
                        
                         <div class="row-fluid-outer">
                        <div class="row-fluid">
							
                        	 <div class="span8">
								<strong>Get an additional License Key:</strong> 
								<br>
								<a target="_blank" href="http://woocommerce-klick-tipp.com">Click here</a>
							</div>
                        </div>
        
                        </div>  
            
                        
                        </div>
                        <div class="actionBar">
                        	<button type="submit"  name="license_submit" class="hangout_btn"><i class="icon-save"></i> Save License</button>
                        </div>
                    	</form>
                    </div>
                <!-- End Hangouts Settings Panel -->
			
                <!-- Start Hangouts Panel -->
                <div id="hangouts_panel" class="gh_tabs_div">
                	<div class="gh_container_header">
					
                    	<strong>Klick-Tipp Account</strong>
                    </div>
                    	<form action="" method="post" class="hangouts_form">
                    	<div class="gh_tabs_div_inner">
                        <div class="row-fluid-outer">
                        <div class="row-fluid">
							<div class="span4">
								<strong>Username</strong>
                            </div>
                            <div class="span8">
								<input type="text" class="longinput" id="klicktip_username" name="klicktip_username" value="<?php echo $klicktip_username; ?>">
                            </div>
                        </div>
                        </div>
                        <div class="row-fluid-outer">
                        <div class="row-fluid">
							<div class="span4">			
								<strong>Password</strong>    
							</div>
                            <div class="span8">
								<input type="text" class="longinput" id="klicktip_password" name="klicktip_password" value="<?php echo $klicktip_password; ?>" />                            
							</div>
                        </div>
        
                        </div>      
            
            <div class="row-fluid-outer">
                        <div class="row-fluid">
							
                        	 <div class="span8">
								<strong>Get Klick-Tipp Account:</strong> 
								<br>
Go straight sales page: <a title="https://www.klick-tipp.com" href="https://www.klick-tipp.com/15194" target="_blank" rel="nofollow">https://www.klick-tipp.com/</a><br>
1€ all packets for the first month <br>
when finish webinar and click link below webinar: 
<br><a title="https://www.klick-tipp.com/webinar/" href="https://www.klick-tipp.com/webinar/15194" target="_blank" rel="nofollow">https://www.klick-tipp.com/webinar/</a>
   
							</div>
                        </div>
        
                        </div>  
            
               
                        </div>
                    
                        
                        <div class="actionBar">
                        	<button type="submit"  name="klicktip_submit" class="hangout_btn"><i class="icon-save"></i> Save Settings</button>               
							</div>
                    	</form>
                </div>
                <!-- End Hangouts Panel -->                
                <!-- Start Email Settings Panel -->
                <div id="email_settings_panel" class="gh_tabs_div">				
                	<div class="cron_back">					
                		Last data transfer to Klick-Tipp: <?php 
						$last_updated_date = trim(get_option('klicktippbridge_last_export'));
						if($last_updated_date == '')
							echo "Not transferred any data to Klick-Tipp.";
						else
						{	
							/* get gmt offset */
							$gmt_offset	=	get_option('gmt_offset');
							if($gmt_offset!=''){
								$gmt_offset = get_option('gmt_offset');
								$explode_time = explode('.',$gmt_offset);
								$matched = strpos($explode_time[0],"-");

								if(trim($matched)===''){
									$min_sign = '+';
								}
								else{
									$min_sign = '-';
								}

								if(!empty($explode_time[1]))
								{
									if($explode_time[1] == '5')
									{
										$min = '30';
									}
									elseif($explode_time[1] == '75')
									{
										$min = '45';
									}
									else
									{
										$min = '0';
									}
								}
								else
								{
									$min = '0';
								}
								
								echo date("Y-m-d H:i:s",strtotime($explode_time[0]." hours ".$min_sign.$min." min",$last_updated_date));

								
							}	
							else
								echo date("Y-m-d H:i:s",$last_updated_date); 
						}		
						?>				
                	</div>					
                	<div class="cron_back" class="extcron">
					You can to trigger the plugin via an external cron job.
					<br /><br />
					 wget -q <?php echo site_url();?>/wp-cron.php -o /dev/null<br /><br />
					 (e.g. enter "*/5" to minute field) May to define( 'DISABLE_WP_CRON', true ); in your wp-config.php file.<br />
					</div>
					<div class="cron_back">					
                		<form action="" method="post">
						Wordpress Cron : <input type="checkbox" name="klicktip_wordpress_cron" value="1" <?php echo get_option('klicktip_wordpress_cron')=='1'?'checked':''; ?> />
						<button class="hangout_btn" type="submit" name="klicktip_wordpress_cron_submit">Save</button>
						</form>
                	</div>
					<div class="cron_back">
					If on a site with less user traffic, visit page to trigger the sync within 5 minutes >><a target="_blank" href="<?php echo site_url();?>">Click Here</a><<
					</div>
					<div class="cron_back"> 
					Reset Sync Time <a href="admin.php?page=klicktippbridge&reset_sync=1">Click Here</a>
					</div>		
				</div>
                <!-- End Email Settings Panel -->
                </div>               
				</div>
    		</div>
			<?php } ?>    
			</div>    
			</div>    <!-- End Container --> 
			
</body>
</html>
