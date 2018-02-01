<?php
class maghub_api {
	private $base_url;
	private $private_key;
	private $public_key;
	protected $headers = array();

	public function __construct( $base_url, $private_key, $public_key ){
		$this->base_url = $base_url;
		$this->private_key = $private_key;
		$this->public_key = $public_key;
	}


	public function prepare_headers( $method, $endpoint, $resource_name ){
		$this->headers	 = array();
		$signature 		 = $this->build_signature( $method, $endpoint );
		$accepts_header  = $this->build_accept_header( $resource_name );

		$this->headers[] = $this->build_authorization_header( $signature );
		$this->headers[] = "Accept: {$accepts_header}";

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


	public function get_request( $endpoint, $resource_name ){
		$method = "GET";
		$headers = $this->prepare_headers( $method, $endpoint, $resource_name );
		//$signature = $this->build_signature( $endpoint, $resource_name );
		//$accepts_header = $this->build_accept_header( $resource_name );
		$uri = $this->build_uri( $endpoint );
		//echo '<pre>'. print_r($headers,true) .'</pre>';
		//echo "STARTING REQUEST FOR {$endpoint}:<br>\n";
		$curl = curl_init();
		curl_setopt_array($curl, array(
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

	//application/vnd.maghub+json;version=1.0
	//application/vnd.maghub.companies+json;version=1.0

	public function get_endpoints(){
		$endpoint = "";
		$resource_name = "";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	public function get_companies( $args = array() ){
		$available_args = array(
			'company',
			'active',
			'org_id',
			'organization',
		);
		
		$endpoint = "companies";
		$resource_name = "companies";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	function get_company( $id ){
		$id = intval( $id );
		$endpoint = "companies/{$id}";
		$resource_name = "company";
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
	
	function get_company_category( $company_id, $category_id ){
		$company_id = intval( $company_id );
		$category_id = intval( $cocategory_idmpany_id );
		$endpoint = "companies/{$company_id}/category{$category_id}";
		$resource_name = "company-category";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}
	
	function get_company_attribute_fields( ){
		$endpoint = "company-attribute-fields";
		$resource_name = "company-attribute-fields";
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

	public function get_contacts(){
		$endpoint = "contacts";
		$resource_name = "contacts";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

	function get_contact( $id ){
		$id = intval( $id );
		$endpoint = "contacts/{$id}";
		$resource_name = "contact";
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

	public function get_publications(){
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

	public function get_personel( $id ){
		$id = intval( $id );
		$endpoint = "personnel/{$id}";
		$resource_name = "personnel";
		$results = $this->get_request( $endpoint, $resource_name );
		return $results;
	}

}
?>