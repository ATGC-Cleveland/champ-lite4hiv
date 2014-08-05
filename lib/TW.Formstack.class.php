<?php

class TW_Formstack {
	
	const CLIENT_ID = '12050';
	const CLIENT_SECRET = '8f91e11412';
	//const REDIRECT_URL = get_permalink();

	const API_URL = 'https://www.formstack.com/api/v2/';
	
	const AUTHORIZE_EP = '/oauth2/authorize';
	//const AUTHORIZE_URL = self::SERVICE_URL . self::AUTHORIZE_EP;

	const TOKEN_EP = '/oauth2/token';
	//const TOKEN_URL = self::SERVICE_URL . self::TOKEN_EP;

	const API_KEY = '219b6adc6faa59ad78add7e9dd904eef';
	
	/**
     * Makes a call to the Formstack API and returns a JSON array object.
     *
     * @link http://support.formstack.com/index.php?pg=kb.page&id=29
     * @param mixed $id submission id
     * @param array $args optional arguments
     * @return array
     */
	public function request( $object = array() , $params = array() , $res = '' , $totals = array() , &$merged_data = array()  ) {
	
		//var_dump( $res );
		
		$res = '1';
		
		if ( empty( $res ) ) {
			
			$res = curl_init( self::API_URL . implode( $object , '/' ) . '.json' . '?' . http_build_query( $params ) );
			curl_setopt($res, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($res, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . self::API_KEY ) );
			
			//var_dump( $res );
			
			$data = json_decode( curl_exec( $res ) );
			
			curl_close( $res );
			
		} else {
			
			/* OFFLINE DATA
			 * Allows data to be accessed from a server-based cache file rather than
			 * querying the Formstack server.
			 */
			
			$dir = wp_upload_dir();
			
			$data = json_decode( file_get_contents( $dir['basedir'] . '/pdf/hso-data.json' ) );
		}
		
		//var_dump( curl_getinfo( $res ) );
		
		//var_dump( $data );
		
		//file_put_contents( $dir['basedir'] . '/pdf/data.json' , json_encode( $data ) );
		
		if ( property_exists( $data , 'submissions' ) ) {
			
			//echo 'Submission field found.';
			
			// only necessary for data collections
			
			//var_dump( $data );
			
			$merged_data = array_merge( $merged_data , $data->submissions );
			
			if ( empty( $totals ) ) {
				
				$totals['objects'] = $data->total;
				$totals['pages'] = $data->pages;
			}
			
			// retrieves additional records if it discovers more are available
			
			if ( !array_key_exists( 'page' , $params ) ) {
			
				$params['page'] = 1;
				
			}
			
			if ( $params['page'] < $totals['pages'] ) {
				
				$params['page']++;
				
				$this->request( $object , $params , '' , $totals , $merged_data );
			}
			
		} elseif ( property_exists( $data , 'data' ) ) {
			
			echo 'Data field found.';
			
			// processing individual records
			
			$merged_data = $data;
			
		} else {
			
			echo 'Submission or Data fields not found.';
			$merged_data = $data;
		}
		
		return $merged_data;
	}
	
	
	public function retrieve() {
		
	}
	
	
	public function create( $object , $data ) {
	
		//var_dump(http_build_query( $data ));
	
		$res = curl_init( self::API_URL . implode( $object , '/' ) );
		curl_setopt( $res , CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $res , CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . self::API_KEY ) );
		curl_setopt( $res , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $res , CURLOPT_POST, 1 );
		//curl_setopt( $res , CURLOPT_CUSTOMREQUEST , "PUT" );
		curl_setopt( $res , CURLOPT_POSTFIELDS , http_build_query( $data ) );
		
		//var_dump( curl_getinfo( $res ) );
		
		return $this->request( $object , array() , $res );
		
	}
	
	
	public function update( $object , $data ) {
	
		//var_dump(http_build_query( $data ));
	
		$res = curl_init( self::API_URL . implode( $object , '/' ) );
		curl_setopt( $res , CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $res , CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . self::API_KEY ) );
		curl_setopt( $res , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $res , CURLOPT_POST, 1 );
		curl_setopt( $res , CURLOPT_CUSTOMREQUEST , "PUT" );
		curl_setopt( $res , CURLOPT_POSTFIELDS , http_build_query( $data ) );
		
		//var_dump( curl_getinfo( $res ) );
		
		return $this->request( $object , array() , $res );
	}
	
	
	public function delete() {
		
	}
	
	
	public function prepare_params( $params , $fields , $defaults = array() ) {
	
		//var_dump($fields);
	
		$params = $this->parse_params( $defaults , $params );
		
		if ( array_key_exists( 'search_params' , $params ) ) {
			
			$search = atgc_asdm_resolve_search( $params['search_params'] , $fields );
			//var_dump($search);
			$params = array_merge( $params , $search );
			
			unset( $params['search_params'] );
			//var_dump($params);
		}
		
		return $params;
	}
	
	
	private function parse_params( $defaults , $params ) {
		
		foreach ( $defaults as $key => $value ) {
			
			if ( !array_key_exists( $key , $params ) ) {
				$params[ $key ] = $value;
			}
		}
		
		return $params;
	}
}

?>