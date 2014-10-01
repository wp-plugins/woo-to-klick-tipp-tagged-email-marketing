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
	update_option('woocomklicktip_last_export','');
	$message = 'Time is reset, next cron will update all orders to klick-tipp';
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
			$error = "Enter Klicktip Username";			
		}	
		else if($klicktip_password==''){	
			$error	= 'Enter Klicktip Password';	
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
				$message	=	'Klicktip Account Saved Successfully';	
			}
			else{
				$error	= 'Wrong Klicktip Credentials';	
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
                	Woocom Klicktip <span>PLUGIN</span>
                </div>
    		</div>
    		<div class="span6">
            	<div class="block_right">
     				<img src="<?php echo plugin_dir_url(__FILE__); ?>/img/activated.png" alt="" align="top" /> Activated
                </div>
    		</div>
    	</div>
    </div>
    <!-- End Header -->
    <div class="hangout_activated">
		<?php 
				
		if ( !function_exists( 'woocommerce_get_page_id' ) )
			echo '<div class="error"><p>Please activate woocomerce plugin for use of this plugin.</p></div>';
		else{		
		?>
    	<!-- Start Tabs -->
    	<div class="gh_tabs">
		<div class="row-fluid">
			<div class="span12">
     		<ul class="gh_tabs_list">
				<li class="span4"><a href="#hangouts_settings_panel"><span><i class="icon-time"></i></span>Settings </a></li>
				<li class="span4"><a href="#hangouts_panel"><span><i class="icon-envelope"></i></span>Klicktip Account </a></li>
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
							$version	= 'Pro Version';
						}else{	
							$version	= 'Free Version, <a target="_blank" href="https://www.digistore24.com/product/31397/8083">Upgrade to Pro</a><br/>';						
						}
					?>	
                    	<strong>Settings</strong>
                    </div>
                    	<form action="" method="post" class="hangouts_form">
                    	<div class="gh_tabs_div_inner">
                        <div class="row-fluid-outer">
                        <div class="row-fluid">
							<div class="span4">
								<strong>Email</strong> <?php echo $version; ?>
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
                        </div>
                        <div class="actionBar">
                        	<button type="submit"  name="license_submit" class="hangout_btn"><i class="icon-save"></i> Save Settings</button>
                        </div>
                    	</form>
                    </div>
                <!-- End Hangouts Settings Panel -->
			
                <!-- Start Hangouts Panel -->
                <div id="hangouts_panel" class="gh_tabs_div">
                	<div class="gh_container_header">
					
                    	<strong>Klicktip Account</strong>
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
                		Last updated date : <?php 
						$last_updated_date = trim(get_option('woocomklicktip_last_export'));
						if($last_updated_date == '')
							echo "Not updated yet.";
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
					You need to set the External Cron from cpanel.
					<br /><br />
					- copy&paste next command to "Command" field:
					<br /><br />
					- You need to Set Following command.
					<br /><br />
					 wget -O /dev/null <?php echo site_url();?>/wp-cron.php 2>/dev/null	<br /> (enter "*/5" to minute field)<br /><br />
					</div>
					<div class="cron_back">					
                		<form action="" method="post">
						Wordpress Cron : <input type="checkbox" name="klicktip_wordpress_cron" value="1" <?php echo get_option('klicktip_wordpress_cron')=='1'?'checked':''; ?> />
						<button class="hangout_btn" type="submit" name="klicktip_wordpress_cron_submit">Save</button>
						</form>
                	</div>
					<div class="cron_back">
					To Set 5 minute Wordpress Cron Schedule Click Here <a target="_blank" href="<?php echo site_url();?>/wp-cron.php">Click Here</a>
					</div>
					<div class="cron_back">
					Reset Sync Time <a href="admin.php?page=woocomklicktip&reset_sync=1">Reset Sync</a>
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
