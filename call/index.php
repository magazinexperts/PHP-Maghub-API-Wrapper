<?php
/*
This file is untested in this configuration - changed to be made public, but should still work.
You will need to rewrite all URLs to redirect to this file. 
Then, you can call functions like this:
GET /call/get_company?id=COMPANY_ID

*/
ob_start();
include_once( '../maghub-api.php' );
//include your extended class here

//optional
include_once('maghub_api_config.php');

/*
Either edit this file, or create the file above and define the following three lines:
define( 'MAGHUB_SITE','SITE_ID' ); //SITE_ID.maghub.com do not include maghub.com or .api.maghub.com
define( 'MAGHUB_PRIVATE_KEY','' );
define( 'MAGHUB_PUBLIC_KEY','' );
*/

$maghub_api = new maghub_api( );
//if extending, comment out the line above, and uncomment the line below with your class name
//$maghub_api = new maghub_api_extended( );

if(!empty($_SERVER['REDIRECT_URL'])){
$query_array = implode("/",array_filter(explode("/", $_SERVER['REDIRECT_URL'])));

if( is_callable( array( $maghub_api, "get_{$query_array}" ) ) ){
	
	$method_name = "get_{$query_array}";
	
} elseif( is_callable( array( $maghub_api, $query_array ) ) ){
	
	$method_name = $query_array;
	
} else {
	
	echo "NO, 'get_{$query_array}' is not callable";
	die();
	
}
	
$output = array('function' => $method_name );
if(empty($_GET)){
	$results = call_user_func( array( $maghub_api, $method_name ) );
} else {
	$results = call_user_func_array( array( $maghub_api, $method_name ), $_GET );
}

if(isset($_GET['debug'])){
	$output['results']= '<pre>'. print_r( $results, true ) .'</pre>';
} else {
	header('Content-Type: application/json');
	http_response_code(200);
	echo json_encode( $results );
	die();
}
} 


if(!isset($output)){
	$output = '';
}
?>
<!DOCTYPE HTML>
<head>
</head>
<body>
<?php
echo $output;
?>
</body>
</html>
<?php
ob_end_flush();
mysqli_close( $mysql );
?>
