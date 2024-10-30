<?php

/*

Plugin Name: Country Blocker

Plugin URI: http://globalwebplugin.com/country-blocker/

Description: This will block any country IP that you want. To keep it as easy to setup and use, we use a 3rd party service. If you need help, go to http://globalwebplugin.com. As well if it doses not work for you.

Version: 3.2

Author: Karl Kiesinger

Author URI: http://globalwebplugin.com/

*/



/*

Copyright (C) 2017 Karl Kiesinger, http://globalwebplugin.com/



This program is free software; you can redistribute it and/or modify

it under the terms of the GNU General Public License as published by

the Free Software Foundation; version 2 of the License.



This program is distributed in the hope that it will be useful,

but WITHOUT ANY WARRANTY; without even the implied warranty of

MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

GNU General Public License for more details.



You should have received a copy of the GNU General Public License

along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/



/*

 * Country Blocker.

 *

 * @package 

 * @version 3.2

 */

 

include 'block-page.php';



 function gwpcb_action(){

	$active=get_option("gwpcb_active");

// ******************************IF YOU ARE READING THIS YOU ARE LOCK OUT CHANGE THIS "on" PUT IN "on1" THIS IS A LAST THING TO TRY***********************************

	if($active=="on")

	{
		gwpcb_database_check();
		$ip=gwpcb_getuserIP('run');
	
	}

}


function gwpcb_ipstack($ip) {

	$API = get_option('gwpcb_ipkey');

	$json  = file_get_contents("http://api.ipstack.com/$ip?access_key=$API&output=json");

    $json  =  json_decode($json ,true);

	return  $json['country_code'];
}

function gwpcb_ipgeolocation($ip) {

	$API = get_option('gwpcb_ipkey');
	
	$json  = file_get_contents("https://api.ipgeolocation.io/ipgeo?apiKey=$API&ip=$ip&lang=en&fields=*&excludes=");

    $json  =  json_decode($json ,true);

	return  $json['country_code2'];
}

function gwpcb_ipapi($ip) {

	$API = get_option('gwpcb_ipkey');

	$json  = file_get_contents("http://api.ipapi.com/$ip?access_key=$API");

    $json  =  json_decode($json ,true);

	return  $json['country_code'];
}

function gwpcb_checkban($ip) {

	 $master_list = json_decode(get_option('gwpcb_banlist'), true);

	 if(count($master_list) != 0) { 
		foreach ($master_list as $key => $value) 

		{
			if($value == $ip){
				return 'ban';
			}
		}
		return 'noban';
	}else{
		return 'noban';
	}

	
}
function gwpcb_checkallow($ip) {

	$master_list = json_decode(get_option('gwpcb_allowlist'), true);

	if(count($master_list) != 0) { 
	   foreach ($master_list as $key => $value) 

	   {
		   if($value == $ip){
			   return 'good';
		   }
	   }
	   return 'noip';
   }else{
	   return 'noip';
   }

   
}

function gwpcb_checkip($ip){

	$ban_country = json_decode(get_option("gwpcb_list"),true);

 	if(gwpcb_checkban($ip) == 'noban') {

	
	if(get_option('gwpcb_api_system') == 'ipstack'){ $usercountry = gwpcb_ipstack($ip);}
	
	if(get_option('gwpcb_api_system') == 'ipgeolocation'){ $usercountry = gwpcb_ipgeolocation($ip);}
	
	if(get_option('gwpcb_api_system') == 'ipapi'){ $usercountry = gwpcb_ipapi($ip);}
	}else{
		$usercountry = 'ban';
	}
	

	if(is_array($ban_country)) {

		if(!in_array($usercountry,$ban_country))

		{
		
		get_option('gwpcb_log')=="on"?gwpcb_loger($ip, $usercountry):'';	
		
		if(($_GET['help'] == "yes") || ($_POST['help'] == "Run")){
			header("HTTP/1.0 404 Not Found");
			
			if($_POST['help'] == "Run"){
				$e = $_POST['ea'];
				
				if(get_option('admin_email') == $e){
				update_option("gwpcb_active","");				
				$headers = 'From: country-blocker unlock' . "\r\n";
				wp_mail( $e, 'Country blocker', 'You or someone turn off this Country blocker.', $headers, $attachments );
					echo('<html><head>

						<title>Admin Help</title>

						</head><body>');
						
						echo('<h1>You have turn off this plugin</h1>

						<button onclick="myFunction()">Click here the reload page.</button>

						<script>
						function myFunction() {
						window.location.href = "/"
						}
						</script>
						</body></html>');
				get_option('gwpcb_log')=="on"?gwpcb_loger("OFF", "Someone has to turn off"):'';

				die();
			}else{
				$msg = "<b>That is not right. Please try again.</b>";
			}
			}
		get_option('gwpcb_log')=="on"?gwpcb_loger("Unlock", "Someone has try to turn off"):'';
		

						echo('<html><head>

						<title>Admin Help</title>

						</head><body>');
						
						echo('<h1>Please fill in the info to turn off this plugin</h1>
						<p>'.$msg.'<p>
						<form method="post" action="">

						<p>

						Administrator Email address<p>
						This is the email you have setup in wordpress<p>
						<input type="text" name="ea" value="" /><p>
						<input type="submit" name="help" id="submit" value="Run"  />
						
						</form>
						</body></html>');
						

				die();
		
		} 
			$action = get_option("gwpcb_action");

				switch($action)

					{

					case "blank":

						echo("Sorry, not found");

						break;

					case "redirection":

						$url = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'),htmlspecialchars(get_option("gwpcb_redirect_url"),ENT_QUOTES));

						if(!isset($url)||$url==null||trim($url)=="")

						{

						$url='http://www.google.com';

						}

				

						header('Location: '.$url,true,302);

					break;

						case "error404":

						header("HTTP/1.0 404 Not Found");

						echo("<html><head>

						<title>404 Not Found</title>

						</head><body>

						<h1>Not Found</h1>

						<p>The requested URL was not found on this server.</p>

						<p>Additionally, a 404 Not Found

						error was encountered while trying to use an ErrorDocument to handle the request.</p>

						<hr>

						<address>Server</address>

						</body></html>");

					break;

						default:

						$path=get_bloginfo('url')."/wp-content/plugins/".basename( dirname( __FILE__ ) )."/";

						header("HTTP/1.0 404 Not Found");

						echo("<html><head>

						<title>404 Not Found</title>

						</head><div align='center'>

						<img src='".$path."images/default.jpg' alt='Country Blocker'/></div>

						</body></html>");					

					}

		

		die();

		}

	

	}

}
function gwpcb_database_check() {
	
	$new = array();
	$ban_country = json_decode(get_option("gwpcb_list"),true);
	foreach ($ban_country as $key => $value) {
    if($value != ''){$new+= array($key => $value);}
	
}
	if(count($new) != count($ban_country)){
	count($new)?0:update_option('gwpcb_active', ''); 
	$new_db = json_encode($new); 
	update_option("gwpcb_list",$new_db);
	get_option('gwpcb_log')=="on"?gwpcb_loger('System,', 'Database remade'):'';
	}

}
function gwpcb_loger( $ip, $code ) {   

    $time = date( "F j, Y g:i a" );

    $ban = "$ip $code $time\r\n"; 

    $file = plugin_dir_path( __FILE__ ) . '/log.txt'; 

    $open = fopen( $file, "a" ); 

    $write = fputs( $open, $ban ); 

    fclose( $open );

}

function gwpcb_getuserIP($go){

	if (isset($_SERVER['HTTP_CLIENT_IP']))

	{

		$ip=$_SERVER['HTTP_CLIENT_IP'];
		
		if($go == 'back')

		{

			return $ip;	

		}

		else

		{

			gwpcb_checkip($ip);

		}

	}

	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))

	{

		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		//$ip="32.212.40.74";
		if($go == 'back')

		{

			return $ip;	

		}

		else

		{

			gwpcb_checkip($ip);

		}

	}

	if (isset($_SERVER['REMOTE_ADDR']))

	{

		$ip=$_SERVER['REMOTE_ADDR'];
		if($go == 'back')

		{

			return $ip;	

		}

		else

		{
			if(gwpcb_checkallow($ip) == 'noip') {
				gwpcb_checkip($ip);
			}
			

		}

	}

	return $ip;

}
 





function gwpcb_Check_uesr() 

{

	$ip = gwpcb_getuserIP('back');

	$API = get_option('gwpcb_ipkey');
	$json  = file_get_contents("http://api.ipstack.com/$ip?access_key=$API&output=json");


	$json  =  json_decode($json ,true);

	$usercountry =  $json['country_code'];

	if($usercountry != 'US')

	{

	update_option("gwpcb_list",json_encode(array('US', $usercountry)));

	}
	
	return '';

}

	

function gwpcb_register_mysettings() 

{

	//register our settings

	
	register_setting( 'gwpcb_settings', 'gwpcb_active' );

	register_setting( 'gwpcb_settings', 'gwpcb_action' );
	
	register_setting( 'gwpcb_settings', 'gwpcb_ipkey' );

	register_setting( 'gwpcb_settings', 'gwpcb_api_system' );

	register_setting( 'gwpcb_settings', 'gwpcb_log' );

	register_setting( 'gwpcb_settings', 'gwpcb_redirect_url' );

}



// Add settings link on plugin page

function gwpcb_settings_link($links) 

{ 

  $settings_link = '<a href="admin.php?page=gwpcb">Settings</a>';

  array_unshift($links, $settings_link); 

  return $links; 

}



function gwpcb_install() 

{

	if ( !gwpcb_plugin_current_version() ) 

	{ 

		gwpcb_plugin_activation();

	}

	else

	{

	$db_made = json_encode(array("Afghanistan" => "AF", "Aland Islands" => "AX", "Albania" => "AL", "Algeria" => "DZ", "American Samoa" => "AS", "Andorra" => "AD", "Angola" => "AO", "Anguilla" => "AI", "Antarctica" => "AQ", "Antigua & Barbuda" => "AG", "Argentina" => "AR", "Armenia" => "AM", "Aruba" => "AW", "Australia" => "AU", "Austria" => "AT", "Azerbaijan" => "AZ", "Bahamas, The" => "BS", "Bahrain" => "BH", "Bangladesh" => "BD", "Barbados" => "BB", "Belarus" => "BY", "Belgium" => "BE", "Belize" => "BZ", "Benin" => "BJ", "Bermuda" => "BM", "Bhutan" => "BT", "Bolivia" => "BO", "Bonaire, St.Eustat, Saba" => "BQ", "Bosnia and Herzegovina" => "BA", "Botswana" => "BW", "Bouvet Island" => "BV", "Brazil" => "BR", "British Indian Ocean T." => "IO", "British Virgin Islands" => "VG", "Brunei Darussalam" => "BN", "Bulgaria" => "BG", "Burkina Faso" => "BF", "Burundi" => "BI", "Cabo Verde" => "CV", "Cambodia" => "KH", "Cameroon" => "CM", "Canada" => "CA", "Cayman Islands" => "KY", "Central African Republic" => "CF", "Chad" => "TD", "Chile" => "CL", "China" => "CN", "Christmas Island" => "CX", "Cocos (Keeling) Islands" => "CC", "Colombia" => "CO", "Comoros" => "KM", "Congo" => "CG", "Congo, Dem. Rep. of the" => "CD", "Cook Islands" => "CK", "Costa Rica" => "CR", "Cote D'Ivoire" => "CI", "Croatia" => "HR", "Cuba" => "CU", "Curaçao" => "CW", "Cyprus" => "CY", "Czech Republic" => "CZ", "Denmark" => "DK", "Djibouti" => "DJ", "Dominica" => "DM", "Dominican Republic" => "DO", "East Timor (Timor-Leste)" => "TP", "Ecuador" => "EC", "Egypt" => "EG", "El Salvador" => "SV", "Equatorial Guinea" => "GQ", "Eritrea" => "ER", "Estonia" => "EE", "Ethiopia" => "ET", "European Union" => "EU", "Falkland Is. (Malvinas)" => "FK", "Faroe Islands" => "FO", "Fiji" => "FJ", "Finland" => "FI", "France" => "FR", "French Guiana" => "GF", "French Polynesia" => "PF", "French Southern Terr." => "TF", "Gabon" => "GA", "Gambia, the" => "GM", "Georgia" => "GE", "Germany" => "DE", "Ghana" => "GH", "Gibraltar" => "GI", "Greece" => "GR", "Greenland" => "GL", "Grenada" => "GD", "Guadeloupe" => "GP", "Guam" => "GU", "Guatemala" => "GT", "Guernsey and Alderney" => "GG", "Guiana, French" => "GF", "Guinea" => "GN", "Guinea-Bissau" => "GW", "Guinea, Equatorial" => "GP", "Guyana" => "GY", "Haiti" => "HT", "Heard & McDonald Is." => "HM", "Holy See (Vatican)" => "VA", "Honduras" => "HN", "Hong Kong, (China)" => "HK", "Hungary" => "HU", "Iceland" => "IS", "India" => "IN", "Indonesia" => "ID", "Iran, Islamic Republic of" => "IR", "Iraq" => "IQ", "Ireland" => "IE", "Israel" => "IL", "Italy" => "IT", "Ivory Coast (Cote d'Ivoire)" => "CI", "Jamaica" => "JM", "Japan" => "JP", "Jersey" => "JE", "Jordan" => "JO", "Kazakhstan" => "KZ", "Kenya" => "KE", "Kiribati" => "KI", "Korea Dem. People's Rep." => "KP", "Korea, (South) Republic of" => "KR", "Kosovo" => "KV", "Kuwait" => "KW", "Kyrgyzstan" => "KG", "Lao People's Dem. Rep." => "LA", "Latvia" => "LV", "Lebanon" => "LB", "Lesotho" => "LS", "Liberia" => "LR", "Libyan Arab Jamahiriya" => "LY", "Liechtenstein" => "LI", "Lithuania" => "LT", "Luxembourg" => "LU", "Macao, (China)" => "MO", "Macedonia, TFYR" => "MK", "Madagascar" => "MG", "Malawi" => "MW", "Malaysia" => "MY", "Maldives" => "MV", "Mali" => "ML", "Malta" => "MT", "Man, Isle of" => "IM", "Marshall Islands" => "MH", "Martinique (FR)" => "MQ", "Mauritania" => "MR", "Mauritius" => "MU", "Mayotte (FR)" => "YT", "Mexico" => "MX", "Micronesia, Fed. States of" => "FM", "Moldova, Republic of" => "MD", "Monaco" => "MC", "Mongolia" => "MN", "Montenegro" => "CS", "Montserrat" => "MS", "Morocco" => "MA", "Mozambique" => "MZ", "Myanmar (ex-Burma)" => "MM", "Namibia" => "NA", "Nauru" => "NR", "Nepal" => "NP", "Netherlands" => "NL", "Netherlands Antilles" => "AN", "New Caledonia" => "NC", "New Zealand" => "NZ", "Nicaragua" => "NI", "Niger" => "NE", "Nigeria" => "NG", "Niue" => "NU", "Norfolk Island" => "NF", "Northern Mariana Islands" => "MP", "Norway" => "NO", "Oman" => "OM", "Pakistan" => "PK", "Palau" => "PW", "Palestinian Territory" => "PS", "Panama" => "PA", "Papua New Guinea" => "PG", "Paraguay" => "PY", "Peru" => "PE", "Philippines" => "PH", "Pitcairn Island" => "PN", "Poland" => "PL", "Portugal" => "PT", "Puerto Rico" => "PR", "Qatar" => "QA", "Reunion (FR)" => "RE", "Romania" => "RO", "Russia (Russian Fed.)" => "RU", "Rwanda" => "RW", "Sahara, Western" => "EH", "Saint Barthelemy (FR)" => "BL", "Saint Helena (UK)" => "SH", "Saint Kitts and Nevis" => "KN", "Saint Lucia" => "LC", "Saint Martin (FR)" => "MF", "S Pierre & Miquelon(FR)" => "PM", "S Vincent & Grenadines" => "VC", "Samoa" => "WS", "San Marino" => "SM", "Sao Tome and Principe" => "ST", "Saudi Arabia" => "SA", "Senegal" => "SN", "Serbia" => "RS", "Seychelles" => "SC", "Sierra Leone" => "SL", "Singapore" => "SG", "Slovakia" => "SK", "Slovenia" => "SI", "Solomon Islands" => "SB", "Somalia" => "SO", "South Africa" => "ZA", "S.George & S.Sandwich" => "GS", "South Sudan" => "SS", "Spain" => "ES", "Sri Lanka (ex-Ceilan)" => "LK", "Sudan" => "SD", "Suriname" => "SR", "Svalbard & Jan Mayen Is." => "SJ", "Swaziland" => "SZ", "Sweden" => "SE", "Switzerland" => "CH", "Syrian Arab Republic" => "SY", "Taiwan" => "TW", "Tajikistan" => "TJ", "Tanzania, United Rep. of" => "TZ", "Thailand" => "TH", "Timor-Leste (East Timor)" => "TP", "Togo" => "TG", "Tokelau" => "TK", "Tonga" => "TO", "Trinidad & Tobago" => "TT", "Tunisia" => "TN", "Turkey" => "TR", "Turkmenistan" => "TM", "Turks and Caicos Is." => "TC", "Tuvalu" => "TV", "Uganda" => "UG", "Ukraine" => "UA", "United Arab Emirates" => "AE", "United Kingdom" => "UK", "United States" => "US", "US Minor Outlying Isl." => "UM", "Uruguay" => "UY", "Uzbekistan" => "UZ", "Vanuatu" => "VU", "Vatican (Holy See)" => "VA", "Venezuela" => "VE", "Viet Nam" => "VN", "Virgin Islands, British" => "VG", "Virgin Islands, U.S." => "VI", "Wallis and Futuna" => "WF", "Western Sahara" => "EH", "Yemen" => "YE", "Zambia" => "ZM", "Zimbabwe" => "ZW"));

	update_option("gwpcb_ipkey",'');
	
	update_option("gwpcb_active",'');

	update_option("gwpcb_log",'on');

	update_option("gwpcb_action",'default');

	update_option("gwpcb_master_list", $db_made);

	update_option("gwpcb_list",'["US"]');

	
	
	update_option("gwpcb_Check_uesr",'no');

	$IP = gwpcb_getuserIP('back');
	update_option("gwpcb_allowlist",'["'.$IP.'"]');

	update_option("gwpcb_banlist",'[]');

	update_option("gwpcb_api_system",'ipstack');

	
	update_option("gwpcb_pluginversion",gwpcb_PLUGIN_VERSION());
	//gwpcb_Check_uesr();

	}

}

function gwpcb_unistall() {

	delete_option("gwpcb_ipkey");
	
	delete_option("gwpcb_active");

	delete_option("gwpcb_log");

	delete_option("gwpcb_action");

	delete_option("gwpcb_master_list");

	delete_option("gwpcb_list");

	delete_option("gwpcb_pluginversion");

	delete_option("gwpcb_redirect_url");
	
	delete_option("gwpcb_Check_uesr");

	delete_option("gwpcb_allowlist");
	
	delete_option("gwpcb_banlist");

	delete_option("gwpcb_api_system");
	
	unregister_setting( 'gwpcb_settings', 'gwpcb_ipkey' );

	unregister_setting( 'gwpcb_settings', 'gwpcb_active' );

	unregister_setting( 'gwpcb_settings', 'gwpcb_action' );

	unregister_setting( 'gwpcb_settings', 'gwpcb_log' );

	unregister_setting( 'gwpcb_settings', 'gwpcb_redirect_url' );

	unregister_setting( 'gwpcb_settings', 'gwpcb_api_system' );

}



function gwpcb_adminmenu() {

	add_menu_page( 'Country Blocker', 'Country Blocker Setting', 'manage_options', 'gwpcb', 'gwpcb_settings_page', '','Dashboard' );

	add_submenu_page( 'gwpcb', 'Country Blocker', 'Pick The Country to not Block', 'manage_options', 'gwpcb_pick','gwpcb_pick_page' );

	add_submenu_page( 'gwpcb', 'Country Blocker', 'Allow Ip List', 'manage_options', 'gwpcb_allowlist','gwpcb_allow_list_page' );

	add_submenu_page( 'gwpcb', 'Country Blocker', 'Ban Ip List', 'manage_options', 'gwpcb_banlist','gwpcb_ban_list_page' );

	add_submenu_page( 'gwpcb', 'Country Blocker', 'Test IP Address', 'manage_options', 'gwpcb_tester','gwpcb_tester_page' );
	
	get_option('gwpcb_log')=="on"?add_submenu_page( 'gwpcb', 'Country Blocker', 'Log', 'manage_options', 'gwpcb_log','gwpcb_log_page' ):'';



}

//update

function gwpcb_plugin_activation() 

{

	$version = get_option( 'gwpcb_pluginversion' );

		register_setting( 'gwpcb_settings', 'gwpcb_api_system' );
	
			update_option("gwpcb_ipkey",'');
	
			update_option("gwpcb_active",'');

			update_option("gwpcb_Check_uesr",'done');
			
			
			$IP = gwpcb_getuserIP('back');
			update_option("gwpcb_allowlist",'["'.$IP.'"]');

			update_option("gwpcb_banlist",'[]');

			update_option("gwpcb_api_system",'ipstack');

	

	update_option( 'gwpcb_pluginversion', gwpcb_PLUGIN_VERSION() );

	

}



function gwpcb_plugin_current_version()

{

    $version = get_option( 'gwpcb_pluginversion' );

	if($version == false) 

	{

		return true;

	} 

	else 

	{
		
    return version_compare($version, gwpcb_PLUGIN_VERSION(), '=') ? true : false;

	}

}

function gwpcb_PLUGIN_VERSION() {

	return '3.2';

}

//Add action

add_action('init', 'gwpcb_action');

register_activation_hook( __FILE__, 'gwpcb_install' );

register_deactivation_hook( __FILE__, 'gwpcb_unistall' );



if ( is_admin() ){

	$plugin = plugin_basename(__FILE__); 

	add_action( 'admin_init', 'gwpcb_register_mysettings' );

	add_filter("plugin_action_links_$plugin", 'gwpcb_settings_link' );

	add_action( 'admin_menu', 'gwpcb_adminmenu' );

}



?>