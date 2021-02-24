<?php
include_once( 'includes/php_build_url.php');
/*

TODO: add field filter to every GET call_user_func
TODO: add _embed to collapse objects to one call
https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/

*/
class maghub_api {
	private $base_url;
	private $private_key;
	private $public_key;
	protected $headers = array();

	public function __construct( $site_name = MAGHUB_SITE, $private_key = MAGHUB_PRIVATE_KEY, $public_key = MAGHUB_PUBLIC_KEY ){
		$this->base_url = 'https://'. $site_name .'.api.maghub.com/';
		$this->private_key = $private_key;
		$this->public_key = $public_key;
	}


	public function prepare_headers( $method, $endpoint, $resource_name, $extra_headers = array() ){
		$this->headers	 = array();
		$signature 		 = $this->build_signature( $method, $endpoint );
		$accepts_header  = $this->build_accept_header( $resource_name );

		$this->headers[] = $this->build_authorization_header( $signature );
		$this->headers[] = "Accept: {$accepts_header}";
		foreach( $extra_headers as $eh ){
			$this->headers[] = $eh;
		}
		return $this->headers;
	}


	public function build_signature( $method, $endpoint ){
		$method = strtoupper( $method ); //sets all methods to uppercase as the lower is never expected by endpoint
		$uri = $this->build_uri( $endpoint );
		return base64_encode( hash_hmac( 'sha512', "{$method}\n{$uri}", $this->private_key ) );
	}

	public function build_accept_header( $resource_name = ""){
		
		if( empty( $resource_name ) ){
			return "application/vnd.maghub+json;version=1.0";
		} else {
			return "application/vnd.maghub.{$resource_name}+json;version=1.0";
		}
	}

	public function build_authorization_header( $signature ){
		return "Authorization: MAGHUB {$this->public_key}:{$signature}";
	}

	public function build_uri( $endpoint ){
		return $this->base_url . $endpoint;
	}

	public function get_request( $endpoint, $resource_name, $extra_headers = array(), $get = array() ){
		$method = "GET";
		
		if(!empty($get)){
			$endpoint_array = parse_url( $endpoint );
			$endpoint_array['query'] = http_build_query( $get );
			$endpoint = http_build_url($endpoint_array);
		}

		if(substr($endpoint,0,1) == "/"){
			$endpoint = substr($endpoint,1);
		}

		$headers = $this->prepare_headers( $method, $endpoint, $resource_name, $extra_headers );
		
		//$signature = $this->build_signature( $endpoint, $resource_name );
		//$accepts_header = $this->build_accept_header( $resource_name );
		$uri = $this->build_uri( $endpoint );
		//echo '<pre>'. print_r($headers,true) .'</pre>';
		//echo "STARTING REQUEST FOR {$endpoint}:<br>\n";
		$curl = curl_init();
		curl_setopt_array( $curl, array(
		   CURLOPT_RETURNTRANSFER => 1,
		   CURLOPT_URL => $uri,
		   CURLOPT_HTTPHEADER => $headers,
		   /*
		   CURLOPT_HTTPHEADER => array(
				"Accept: {$accepts_header}",
				"Authorization: MAGHUB {$this->public_key}:{$signature}",
			),
			*/
		   )
		);
		$content = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		return array( 'content' => json_decode( $content, true ), 'info' => $info );
	}
	
	public function post_request( $endpoint, $resource_name, $extra_headers = array(), $body = array() ){
		$method = "POST";
		/*
		if(!empty($get)){
			$endpoint_array = parse_url( $endpoint );
			$endpoint_array['query'] = http_build_query( $get );
			$endpoint = http_build_url($endpoint_array);
		}
		*/
		
		$default_post_headers = array(
			"Content-Type: application/x-www-form-urlencoded"
		);
		
		$extra_headers = array_merge( $default_post_headers, $extra_headers );
		
		if(!empty($body)){
			$fields_string = http_build_query( $body );
		} else {
			$fields_string = "";
		}

		if(substr($endpoint,0,1) == "/"){
			$endpoint = substr($endpoint,1);
		}

		$headers = $this->prepare_headers( $method, $endpoint, $resource_name, $extra_headers );
		
		//$signature = $this->build_signature( $endpoint, $resource_name );
		//$accepts_header = $this->build_accept_header( $resource_name );
		$uri = $this->build_uri( $endpoint );
		//echo '<pre>'. print_r($headers,true) .'</pre>';
		//echo "STARTING REQUEST FOR {$endpoint}:<br>\n";
		$curl = curl_init();
		curl_setopt_array( $curl, array(
		   CURLOPT_RETURNTRANSFER => 1,
		   CURLOPT_URL => $uri,
		   CURLOPT_POST => 1,
		   CURLOPT_HTTPHEADER => $headers,
		   CURLOPT_POSTFIELDS => $fields_string

		   /*
		   CURLOPT_HTTPHEADER => array(
				"Accept: {$accepts_header}",
				"Authorization: MAGHUB {$this->public_key}:{$signature}",
			),
			*/
		   )
		);
		
		$content = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		return array( 'content' => json_decode( $content, true ), 'info' => $info );
	}
	
	public function put_request( $endpoint, $resource_name, $extra_headers = array(), $body = array() ){
		$method = "PUT";
		/*
		if(!empty($get)){
			$endpoint_array = parse_url( $endpoint );
			$endpoint_array['query'] = http_build_query( $get );
			$endpoint = http_build_url($endpoint_array);
		}
		*/
		
		$default_post_headers = array(
			"Content-Type: application/x-www-form-urlencoded"
		);
		
		$extra_headers = array_merge( $default_post_headers, $extra_headers );
		
		if(!empty($body)){
			$fields_string = http_build_query( $body );
		} else {
			$fields_string = "";
		}

		if(substr($endpoint,0,1) == "/"){
			$endpoint = substr($endpoint,1);
		}

		$headers = $this->prepare_headers( $method, $endpoint, $resource_name, $extra_headers );
		
		//$signature = $this->build_signature( $endpoint, $resource_name );
		//$accepts_header = $this->build_accept_header( $resource_name );
		$uri = $this->build_uri( $endpoint );
		//echo '<pre>'. print_r($headers,true) .'</pre>';
		//echo "STARTING REQUEST FOR {$endpoint}:<br>\n";
		$curl = curl_init();
		curl_setopt_array( $curl, array(
		   CURLOPT_RETURNTRANSFER => 1,
		   CURLOPT_URL => $uri,
		   CURLOPT_CUSTOMREQUEST => 'PUT',
		   CURLOPT_HTTPHEADER => $headers,
		   CURLOPT_POSTFIELDS => $fields_string

		   /*
		   CURLOPT_HTTPHEADER => array(
				"Accept: {$accepts_header}",
				"Authorization: MAGHUB {$this->public_key}:{$signature}",
			),
			*/
		   )
		);
		
		$content = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		return array( 'content' => json_decode( $content, true ), 'info' => $info );
	}

	//application/vnd.maghub+json;version=1.0
	//application/vnd.maghub.companies+json;version=1.0

	public function get_endpoints(){
		$endpoint = "";
		$resource_name = "";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_companies_by( $field, $value, $page = 0, $num_per_page = 10 ){
		$available_args = array(
			'company',
			'company_id',
			'active',
			'org_id',
			'organization',
		);
		
		$endpoint = "companies";
		$resource_name = "companies";
		$extra_headers = array( "X-OPT-LIMIT: {$num_per_page}", "X-OPT-OFFSET: {$page}" );
		$results = $this->get_request( $endpoint, $resource_name, $extra_headers, array( $field => $value ));
		return $results;
	}
	
	public function get_companies( $page = 0, $num_per_page = 100, $args = array() ){
		$available_args = array(
			'company',
			'company_id',
			'active',
			'org_id',
			'organization',
		);
		
		$endpoint = "companies";
		$resource_name = "companies";
		$extra_headers = array( "X-OPT-LIMIT: {$num_per_page}", "X-OPT-OFFSET: {$page}" );
		$results = $this->get_request( $endpoint, $resource_name, $extra_headers );
		return $results;
	}

	function get_company( $id ){
		$id = intval( $id );
		$endpoint = "companies/{$id}";
		$resource_name = "company";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_assets( ){
		$endpoint = "companyassets";
		$resource_name = "companyassets";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_orders( $company_id ){
		$company_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/orders";
		$resource_name = "company-orders";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_contacts( $company_id ){
		$company_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/contacts";
		$resource_name = "company-contacts";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_categories( $company_id ){
		print_r($company_id);
		if(is_array( $company_id )){
			
		}
		
		$company_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/categories";
		$resource_name = "company-categories";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_activities( $company_id ){
		$company_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/activities";
		$resource_name = "company-activities";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
		
	function get_company_attribute_fields( ){
		$endpoint = "company-attribute-fields";
		$resource_name = "company-attribute-fields";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_category( $company_id, $category_id ){
		$company_id = intval( $company_id );
		$category_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/category{$category_id}";
		$resource_name = "company-category";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_attribute_field( $field_id ){
		$field_id = intval( $field_id );
		$endpoint = "company-attribute-fields/{$field_id}";
		$resource_name = "company-attribute-field";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_attribute_field_values( $company_id  ){
		$company_id = intval( $company_id );
		$endpoint = "companies/{$company_id}/attribute-values";
		$resource_name = "company-attribute-values";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_attribute_field_value( $company_id, $field_id ){
		//parse_str($company_id);
		//return print_r(func_get_args());
		$company_id = intval( $company_id );
		$field_id = intval( $field_id );
		//https://magazinexperts.api.maghub.com/companies/68947/attribute-values/39
		$endpoint = "companies/{$company_id}/attribute-values/{$field_id}";
		$resource_name = "company-attribute-value";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_contact_by( $field, $value, $page = 0, $num_per_page = 10 ){
		$available_args = array(
			'company',
			'company_id',
			'active',
			'name',
			'email',
			'phone',
			//'changed_since',
		);
		$endpoint = "contacts";
		$resource_name = "contacts";
		$num_per_page = intval( $num_per_page );
		$page = intval( $page );
		$extra_headers = array( "X-OPT-LIMIT: {$num_per_page}", "X-OPT-OFFSET: {$page}" );
		
		$results = $this->get_request( $endpoint, $resource_name, $extra_headers, array( $field => $value ) );
		return $results;
	}
	
	
	public function get_contacts( $args = array(), $page = 0, $num_per_page = 100 ){	
		$endpoint = "contacts";
		$resource_name = "contacts";
		$num_per_page = intval( $num_per_page );
		$page = intval( $page );
		
		$search_critera_args = array(
			'company',
			'company_id',
			'active',
			'name',
			'email',
			'phone',
			//'changed_since'
		);
		
		$filters = array();

		if(!empty($args)){
		foreach($args as $arg_key => $arg_value ){
			if( in_array( $arg_key, $search_critera_args ) ){
				switch( $arg_key ){
					case "company_id":
						$filters[$arg_key] = intval( $arg_value );
					break;
					case "phone":
						$filters[$arg_key] = preg_replace('/[^0-9]/', '', $arg_value);
					break;
					default:
						$filters[$arg_key] = $arg_value;
					break;
				}
			}
			
		}
		}
		
		$extra_headers = array( "X-OPT-LIMIT: {$num_per_page}", "X-OPT-OFFSET: {$page}" );
		$results = $this->get_request( $endpoint, $resource_name, $extra_headers, $filters );
		return $results;
	}

	function get_contact( $id ){
		$id = intval( $id );
		$endpoint = "contacts/{$id}";
		$resource_name = "contact";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_editorials(){
		$endpoint = "editorials";
		$resource_name = "editorials";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}	
	
	public function get_editorial( $id ){
		$id = intval( $id );
		$endpoint = "editorials/{$id}";
		$resource_name = "editorials";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_lead_statuses(){
		$endpoint = "lead-statuses";
		$resource_name = "lead-statuses";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_lead_sources(){
		$endpoint = "lead-sources";
		$resource_name = "lead-sources";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_orders(){
		$endpoint = "orders";
		$resource_name = "orders";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_order( $id ){
		$id = intval( $id );
		#$endpoint = "orders?order_id={$id}";
		$endpoint = "orders/{$id}";
		$resource_name = "order";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_personel( $id ){
		$id = intval( $id );
		$endpoint = "personnel/{$id}";
		$resource_name = "personnel";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_projects(){
		$endpoint = "projects";
		$resource_name = "projects";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_project( $id ){
		$id = intval( $id );
		$endpoint = "projects/{$id}";
		$resource_name = "project";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_project_tasks( $id ){
		$id = intval( $id );
		$endpoint = "projects/{$id}/tasks";
		$resource_name = "project-tasks";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_task( $id ){
		$id = intval( $id );
		$endpoint = "tasks/{$id}";
		$resource_name = "task";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_publications(){
		$available_args = array(
			'name',
			'type'
		);
		
		$endpoint = "publications";
		$resource_name = "publications";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_publication( $id ){
		$id = intval( $id );
		$endpoint = "publications/{$id}";
		$resource_name = "publication";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_tickets(){
		$endpoint = "tickets";
		$resource_name = "tickets";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_ticket_statuses(){
		$endpoint = "ticket-statuses";
		$resource_name = "ticket-statuses";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_ad_ticket( $id ){
		$id = intval( $id );
		$endpoint = "ad-ticket/{$id}";
		$resource_name = "ad-ticket";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_service_ticket( $id ){
		$id = intval( $id );
		$endpoint = "service-ticket/{$id}";
		$resource_name = "service-ticket";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_vendors(){
		$endpoint = "vendors";
		$resource_name = "vendors";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_vendor( $id ){
		$id = intval( $id );
		$endpoint = "vendors/{$id}";
		$resource_name = "vendors";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_subscribers(){
		$endpoint = "subscribers";
		$resource_name = "subscribers";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	public function get_subscriber_by_id( $id ){
			$this->get_subscriber( $id );
	}
	
	public function get_subscriber( $id ){
		$id = intval( $id );
		$endpoint = "subscribers/{$id}";
		$resource_name = "subscribers";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_entitlement( $email , $password, $type ){
		//$types_possible = array( 'user', 'contact' );
		
		$endpoint = "entitlement";
		$resource_name = "";
		$results = $this->post_request( $endpoint, $resource_name, array(), array( 
			'email' 	=> $email,
			'password' 	=> $password,
			//'type' 		=> $type
			));
		return $results['content'];
	}
	
	public function get_user_entitlement( $email, $password ){
		return $this->get_entitlement( $email, $password, 'user' );
	}
	
	public function get_contact_entitlement( $email, $password ){
		return $this->get_entitlement( $email, $password, 'contact' );
	}

	
	public function get_login(){
		return $this->login();
	}
	
	public function login(){
		$endpoint = "login";
		$resource_name = "login";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	
	/*
	BELOW THIS COMMENT FUNCTIONS MAY NOT WORK YET
	*/
	
	function set_company_attribute_field_value( $company_id, $field_id, $value ){
		$company_id = intval( $company_id );
		$field_id = intval( $field_id );
		$endpoint = "companies/{$company_id}/attribute-values/{$field_id}";
		$resource_name = "company-attribute-value";
		$results = $this->post_request( $endpoint, $resource_name, array(), array(
			'value'	=> $value
		));
		return $results;
		
		//$extra_headers = array(), $body = array() 
	}
	
	/* Work In Process */
	function helper_set_company_attribute_field_value( $company_id, $field_name, $value ){
		
	}
	
	/* Work In Process */
	public function get_user_information(){
		
	}
	
	/* Work In Process */
	public function get_user_info(){
		return $this->get_user_information();
	}
	
	/* Work In Process */
	public function get_issues_by_token( $token ){
		
	}
	
	/* Work In Process */
	public function get_subscriptions_by_token( $token ){
		
	}
	
	/* Work In Process */
	public function get_issues_by_subscriptions_and_token( $subscription_id, $token ){
		
	}
	
	/* Work In Process */
	public function get_issues_by_publicatication_and_token( $publication_id, $token ){
		
	}
	
	/* Work In Process */
	public function create_contact(){
		$endpoint = "contacts/0";
		$resource_name = "contacts";
		
		$available_args = array(
			'firstName',
			'lastName',
			'address1',
			'address2',
			'city',
			'state',
			'zipCode',
			'contact_country',
			'emailAddress',
			'cellPhoneNumber',
			'officePhoneNumber',
			'alternatePhoneNumber',
			'alternateFaxNumber',
			'faxNumber',
			'isPrimaryContact',
			'companyId',
			'contact_notes'
		);
		
	}
	
	/* Work In Process */
	public function update_contact( $contact_id ){
		$contact_id = intval( $contact_id );
		
	}
	
	/* Work In Process */
	public function create_company(){
		
	}
	
	/* Work In Process */
	public function update_company( $company_id ){
		$company_id = intval( $company_id );
		
	}
	
	/* Work In Process */
	public function create_subscriber(){
		$available_args = array(
			'firstName',
			'lastName',
			'address1',
			'address2',
			'city',
			'state',
			'zipCode',
			'contact_country',
			'emailAddress',
			'cellPhoneNumber',
			'officePhoneNumber',
			'alternatePhoneNumber',
			'alternateFaxNumber',
			'faxNumber',
			'isPrimaryContact',
			'companyId',
			'contact_notes',
			'subscriber_type_ID',
			'active',//bool as int
			'password'
		);
		
		$defaults = array(
			'active' 	=> 1,
		);
	}
	
	/* Work In Process */
	public function create_subscription(){
		$endpoint = "subscriptions/0";
		$resource_name = "subscription";
		
		$available_args = array(
			'subscriber_id', //int
			'cost',
			'pubid', //int
			'comp', //bool as int
			'paid', //bool as int
			'source', //string
			'status',
			'startdate',
			'endate',
			'subscriber_type_ID',
			'copies',
			'media_type_id',
			'is_gift', //bool as int
			'gift_subscriber_id',
			'verified_date',
			'qualified_date',
			'promo_code',
		);
		
		$defaults = array(
			'copies' 			=> 1,
			'startdate'			=> date('Y-m-d', time() ),
			'verified_date'		=> date('Y-m-d', time() ),
			'is_gift'			=> 0,
		);
		
	}
	
	/* Work In Process */
	public function update_subscription( $subscription_id ){
		$subscription_id = intval( $subscription_id );
		
	}
	
	
}
?>
