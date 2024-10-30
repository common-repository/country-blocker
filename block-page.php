<?php

function gwpcb_settings_page() 

{
	$ban_country = json_decode(get_option("gwpcb_list"),true);
	if(get_option('gwpcb_active') == 'admin'){
		
	
	if(count($ban_country)==0){
	$msg = '<p><font size="15">Error You can not have this active with no Country Pick</font>';
	update_option('gwpcb_active', ''); 
	}else{
		update_option('gwpcb_active','on');
	}
	}
	
	


	?>

<div class="wrap">
<?php 
if((get_option('gwpcb_active') == 'admin') || (get_option('gwpcb_active') == 'on')){
	
	$check = strlen(get_option('gwpcb_ipkey'));
	if($check < 5){
		update_option('gwpcb_active','');
		if(get_option('gwpcb_api_system') == 'ipstack'){ $url = 'https://ipstack.com';}
		if(get_option('gwpcb_api_system') == 'ipgeolocation'){ $url = 'https://ipgeolocation.io';}
		if(get_option('gwpcb_api_system') == 'ipapi'){ $url = 'https://ipapi.com';}

		$msg = '<p><font size="15">You must have a api key from '.$url.' for it to work</font>';

	}else{ 

	if(get_option("gwpcb_Check_uesr")=='no'){
			$a=gwpcb_Check_uesr();
			update_option('gwpcb_Check_uesr','done');
	}
		update_option('gwpcb_active','on');
	}
}else{
	$msg = '';
}
?>
	<h2>Country Blocker</h2>
	<?php echo $msg; ?>

	<form method="post" action="options.php">

    <?php settings_fields( 'gwpcb_settings' ); ?>

    <?php do_settings_sections( 'gwpcb_settings' ); ?>

    <p><?php _e('USA Country is not blocked by default','gwpcb');?>.</p>

    <table class="form-table">
 
	<tr style="width:420px" valign="top">

	<th scope="row">What API system do you want to use? 
<td>
	<table>

	<tr><td><input type="radio" name="gwpcb_api_system" value="ipstack" <?php if(get_option('gwpcb_api_system') == 'ipstack'){ echo 'checked';} ?>/> <a href="https://ipstack.com" target="_blank">https://ipstack.com</a> </td></tr>
	<tr><td><input type="radio" name="gwpcb_api_system" value="ipgeolocation" <?php if(get_option('gwpcb_api_system') == 'ipgeolocation'){ echo 'checked';} ?>/> <a href="https://ipgeolocation.io" target="_blank">https://ipgeolocation.io</a> </td></tr>
	<tr><td><input type="radio" name="gwpcb_api_system" value="ipapi" <?php if(get_option('gwpcb_api_system') == 'ipapi'){ echo 'checked';} ?>/> <a href="https://ipapi.com" target="_blank">https://ipapi.com</a> </td></tr>
	
	
</table></td>
	</tr>
		
	  <tr style="width:420px" valign="top">

			<th scope="row"><?php _e('API ACCESS KEY','gwpcb');?></th>

			<td><input style="width:320px" type="text" name="gwpcb_ipkey" value="<?php echo get_option('gwpcb_ipkey'); ?>" /></td>

        </tr>
		
		<tr style="width:420px" valign="top">

			<th scope="row"><?php _e('Active','gwpcb');?> Country Blocker</th>

			<td><input type="checkbox" name="gwpcb_active" value="on" <?php echo get_option('gwpcb_active')?'checked="checked"':''; ?>/></td>

        </tr>

        <tr style="width:420px" valign="top">

			<th scope="row"><?php _e('Logging','gwpcb');?> Country Blocker </th>

			<td><input type="checkbox" name="gwpcb_log" value="on" <?php echo get_option('gwpcb_log')?'checked="checked"':''; ?>/></td>

        </tr> 

		<tr style="width:420px" valign="top">

			<th scope="row"><?php _e('Action','gwpcb');?></th>

			<td>

			<select style="width:120px" name="gwpcb_action">

				<option value="default" <?php echo(get_option('gwpcb_action')=="default"?'selected="selected"':'')?>><?php _e('Default','gwpcb');?></option>

				<option value="blank" <?php echo(get_option('gwpcb_action')=="blank"?'selected="selected"':'')?>><?php _e('Blank Page','gwpcb');?></option>

				<option value="error404" <?php echo(get_option('gwpcb_action')=="error404"?'selected="selected"':'')?>><?php _e('Error 404','gwpcb');?></option>

				<option value="redirection" <?php echo(get_option('gwpcb_action')=="redirection"?'selected="selected"':'')?>><?php _e('Redirection','gwpcb');?></option>

			</select>

			</td>

        </tr>

		<tr style="width:420px" valign="top">

			<th scope="row">Redirection URL (http://...)</th>

			<td><input style="width:320px" type="text" name="gwpcb_redirect_url" value="<?php echo str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'),htmlspecialchars(get_option('gwpcb_redirect_url'),ENT_QUOTES)); ?>" /></td>

        </tr>

    </table>

    <?php submit_button(); ?>

	</form>

<b>If you like to help us out <p>Please donate<p></b>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">

<input type="hidden" name="cmd" value="_s-xclick">

<input type="hidden" name="hosted_button_id" value="2LM9R8M52KRD4">

<input type="image" src="http://globalwebplugin.com/img/paypal-donate-button.png" border="0" name="submit" alt="PayPal - The safer, easier way to donate online!">

</form>

</div>

<?php

}

function gwpcb_pick_page() 

{

	if(isset($_POST['run']))

	{

	$end = $_POST['run'];

	$new_list = array();

		for ($i = 1; ; $i++) 

		{

			if(isset($_POST['a'.$i]))

			{

			$c = $_POST['a'.$i];

			$n = $_POST['name'.$i];

			$new_list += array($n => $c);	

			}

		if ($i > $end) 

		{

		$new = json_encode($new_list); 

		update_option("gwpcb_list",$new);

        break;

		}

   

		}

	}

	?>

<div class="wrap">

	<h2>Pick The Country to not Block</h2>

	<form method="post" action="">

    <p><?php _e('USA Country is not blocked by default','gwpcb');?>.</p>

	<?php $master_list = json_decode(get_option('gwpcb_master_list') , true); ?>

    <?php $list = json_decode(get_option('gwpcb_list') , true); ?>



 <b>Check the Country you do not wish to blook<p>

 Do not unclick the one with this * next to it.<p>

 or you will not be able to come back<p>

 </b>

	<table class="form-table">

    

<?php 

$ip = gwpcb_getuserIP('back');

 

$API = get_option('gwpcb_ipkey');
if(trim($API) != ''){
if(get_option('gwpcb_api_system') == 'ipstack'){ $usercountry = gwpcb_ipstack($ip);}
	
if(get_option('gwpcb_api_system') == 'ipgeolocation'){ $usercountry = gwpcb_ipgeolocation($ip);}

if(get_option('gwpcb_api_system') == 'ipapi'){ $usercountry = gwpcb_ipapi($ip);}
}else{
	$usercountry ='';
}


$a=0;

$b=0;

	foreach ($master_list as $key => $value) 

	{ 

	$a++;

	if($b == 0 ) 

	{ ?>

	<tr style="width:420px" valign="top">

	<?php 

	} ?>

	<th scope="row"><?php echo $key; ?> <?php if($usercountry == $value){ echo '*'; } ?></th>

	<?php if(is_array($master_list) && in_array($value, $list)) { ?>

	<td><input type="checkbox" name="a<?php echo $a; ?>" value="<?php echo $value; ?>" checked="checked" <?php echo ($usercountry == $value)? 'onchange="mylast()"':'';?>/> </td>

	<?php } else { ?>

	<td><input type="checkbox" name="a<?php echo $a; ?>" value="<?php echo $value; ?>" /></td>

	<?php }?>

	<input type="hidden" name="name<?php echo $a; ?>" value="<?php echo $key; ?>" /> 

<?php if($b == 2){ $b=0; echo '</tr>'; }else{ $b++; }



	} ?>

        <script>

function mylast() {

   alert('You Do UNCLICK THIS\nif you do not reclick this box\nYou will not be able to come back');

   

}

</script>

    </table>

	<input type="hidden" name="run" value="<?php echo $a; ?>" />   

    <?php submit_button(); ?>

	</form>

</div>

<?php

}
function gwpcb_allow_list_page() 

{

	if(isset($_POST['add']))

	{
		if($_POST['add'] == 0){
			if(trim($_POST['ip']) != ''){
				$new = json_encode(array($_POST['ip'])); 

				update_option("gwpcb_allowlist",$new);
			}
			
		}else{
			$new_list = array();
			for ($i = 0; ; $i++) 

			{
	
				if(isset($_POST['ip'.$i]))
	
				{
					if(trim($_POST['ip'.$i]) != ''){
					
					$new_list[] = $_POST['ip'.$i];
	
					}
				}

			if ($i > $_POST['add']) 
	
			{
	
			$new = json_encode($new_list); 
	
			update_option("gwpcb_allowlist",$new);
	
			break;
	
			}
	
	   
	
			}
		}
	}

	?>

<div class="wrap">

	<h2>Allow IP List</h2>

	<b>Your IP Address <?php echo gwpcb_getuserIP('back'); ?></b></p>

	<form method="post" action="">

	<?php $master_list = json_decode(get_option('gwpcb_allowlist') , true); ?>

	<?php if(count($master_list) != 0) { ?>


	<table class="form-table">


<?php 

$a=0;

$b=0;

	foreach ($master_list as $key => $value) 

	{ 

	$a++;

	?>

	<tr style="width:420px" valign="top"><td><?php echo $a; ?>. <input type="text" name="ip<?php echo $b; ?>" value="<?php echo $value; ?>" /> </td></tr>


	<?php $b++; } $a++; ?>


	<tr style="width:420px" valign="top"><td><?php echo $a; ?>. <input type="text" name="ip<?php echo $b; ?>" value="" /> </td></tr>


    </table>
	<input type="hidden" name="add" value="<?php echo $a; ?>" />   

	<?php submit_button(); ?>

<p>
	<b>Note:</b> To remove a ip address for the list. Clear the entry and save changes
	<?php } else { ?>
		You have no IP in this allowed list<p>
		1. <input type="text" name="ip" value="" /> <p>

		
		<input type="hidden" name="add" value="0" />   

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add this IP To The allowed List"  /></p>



		<?php } ?>
	
		</form>

</div>

<?php

}

function gwpcb_ban_list_page() 

{

	if(isset($_POST['add']))

	{
		if($_POST['add'] == 0){
			if(trim($_POST['ip']) != ''){
				$new = json_encode(array($_POST['ip'])); 

				update_option("gwpcb_banlist",$new);
			}
			
		}else{
			$new_list = array();
			for ($i = 0; ; $i++) 

			{
	
				if(isset($_POST['ip'.$i]))
	
				{
					if(trim($_POST['ip'.$i]) != ''){
			
						$new_list[] = $_POST['ip'.$i];
	
					}
				}

			if ($i > $_POST['add']) 
	
			{
	
			$new = json_encode($new_list); 
	
			update_option("gwpcb_banlist",$new);
	
			break;
	
			}
	
	   
	
			}
		}
	}

	?>

<div class="wrap">

	<h2>Ban IP List</h2>

	<b>Your IP Address <?php echo gwpcb_getuserIP('back'); ?></b></p>

	<form method="post" action="">

	<?php $master_list = json_decode(get_option('gwpcb_banlist') , true); ?>

	<?php if(count($master_list) != 0) { ?>


	<table class="form-table">


<?php 

$a=0;

$b=0;

	foreach ($master_list as $key => $value) 

	{ 

	$a++;

	?>

	<tr style="width:420px" valign="top"><td><?php echo $a; ?>. <input type="text" name="ip<?php echo $b; ?>" value="<?php echo $value; ?>" /> </td></tr>


	<?php $b++; } $a++; ?>


	<tr style="width:420px" valign="top"><td><?php echo $a; ?>. <input type="text" name="ip<?php echo $b; ?>" value="" /> </td></tr>


    </table>
	<input type="hidden" name="add" value="<?php echo $a; ?>" />   

	<?php submit_button(); ?>

<p>
	<b>Note:</b> To remove a ip address for the list. Clear the entry and save changes
	<?php } else { ?>
		You have no IP in this Ban list<p>
		1. <input type="text" name="ip" value="" /> <p>

		<input type="hidden" name="add" value="0" />   

		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add this IP To The ban List"  /></p>



		<?php } ?>
	
		</form>

</div>

<?php

}

function gwpcb_tester_page() 

{

	$read = '';

	if(isset($_POST['ip']))

	{

	$ip = $_POST['ip'];

	$API = get_option('gwpcb_ipkey');
	if(get_option('gwpcb_api_system') == 'ipstack'){
		$json  = file_get_contents("http://api.ipstack.com/$ip?access_key=$API&output=json");
		$json  =  json_decode($json ,true);
		$country_code =  $json['country_code'];

		$country_name =  $json['country_name'];
	
		$region_name =  $json['region_name'];
	}
	
	if(get_option('gwpcb_api_system') == 'ipgeolocation'){ 
		$json  = file_get_contents("https://api.ipgeolocation.io/ipgeo?apiKey=$API&ip=$ip&lang=en&fields=*&excludes=");
		$json  =  json_decode($json ,true);
		$country_code =  $json['country_code2'];

		$country_name =  $json['country_name'];
	
		$region_name =  $json['state_prov'];
	}
	
	if(get_option('gwpcb_api_system') == 'ipapi'){
		$json  = file_get_contents("http://api.ipapi.com/$ip?access_key=$API");
		$json  =  json_decode($json ,true);
		$country_code =  $json['country_code'];

		$country_name =  $json['country_name'];
	
		$region_name =  $json['region_name'];
	}


	$ban_country = json_decode(get_option("gwpcb_list"),true);

		if(is_array($ban_country)) 

		{

			if(!in_array($country_code,$ban_country))

			{

			$read = 'The IP address you testing is '.$ip .' <p>Base on the setting they would be ban from your web site <p>country code = <b>'.$country_code .'</b><p>country name = <b>'.$country_name .'</b> <p>region name = <b>'.$region_name.'</b>';

			}

			else

			{

			$read = 'The IP address you testing is '.$ip .' <p>Base on the setting they would be <b>NOT </b>ban from your web site <p>country code = <b>'.$country_code .'</b><p>country name = <b>'.$country_name .'</b> <p>region name = <b>'.$region_name.'</b>';

			}

		}

	}

	?>

<div class="wrap">

<h2>Country Blocker IP TESTER</h2>

<?php echo $read; ?>

	<form method="post" action="">

    <p><?php _e('Type in the IP address you want to check','gwpcb');?>.</p>

	<input type="text" name="ip" value="" />

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Run Test"  /></p>

	</form>

</div>

<?php

}



function gwpcb_log_page() 

{

$read = '';

$master_list = json_decode(get_option('gwpcb_master_list') , true); 

	?>

<div class="wrap">

<h2>Country Blocker Log</h2>

    <p><?php _e('The people that was block with info','gwpcb');?>.</p>

	

   <?php

   $file = plugin_dir_path( __FILE__ ) . '/log.txt'; 

    $open = fopen( $file, "r" ); 

        $ln= 0;

    while ($line= fgets ($open)) 

	{

        ++$ln;

		echo '<P>';

        printf ("%2d: ", $ln);

        if ($line===FALSE) print ("FALSE\n");

        else print ($line);

    }

    fclose( $open );

	?>

</div>

<?php

}

?>