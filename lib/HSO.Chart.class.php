<?php

use ZendPdf\PdfDocument;
use ZendPdf\Font;
	
class HSO_Chart {
	
	private $font;
	
	public function __construct(){
		
		$this->font = Font::fontWithName(Font::FONT_HELVETICA_BOLD);
	}

	public function get_charts() {
		echo 'Get Charts<br />';
		
		$res = new TW_Formstack();

		$object = array(
			'primary_object' => 'form',
			'primary_object_id' => '1725093',
			'sub_object' => 'submission'
		);

		$params = array (
			'data' => '',
			'expand_data' => '',
		);

		$data = $res->request( $object , $params );

		//var_dump( $data );
		
		$dir = wp_upload_dir();
		
		$files = scandir( $dir['basedir'] . '/pdf' );
		
		$list = array();
		
		foreach( $files as $key => $file ) {
			
			$ext = substr( strrchr( $file , '.' ) , 1 );
			
			if ( !$ext ) {
				unset( $files[ $key ] );
			} else {
				$list[] = substr( $file , 0 , 10 );
			}
		}
		
		//var_dump( $list );
		
		$rows = array();
		$hidden_fields = array();

		foreach( $data as $record ) {
	
			//var_dump( $record->data->{'25165815'} );
			//var_dump( $record->data );
			
			if( in_array( $record->data->{'25165815'}->value , $list ) ) {
				echo 'Don\'t display record. OpScan has already been generated.<br />';
			} else {
				echo 'Display record for OpScan generation.<br />';
				
				$rows[] = '<tr><td>' . $record->data->{'25203032'}->value . '</td><td>' . $record->data->{'25165815'}->value . '</td><td>' . $record->data->{'25165816'}->value . '</td></tr>';
				
				$hidden_fields[] = '<input type="hidden" name="_chart_ids[]" value="' . $record->id .'">';
			}
		}
		
		$table = '<table border=1><caption>Ungenerated EvaluationWeb Charts</caption><thead><tr><th>Modality</th><th>ODH ID</th><th>AHF ID</th></tr></thead><tbody>' . implode( $rows ) . '</tbody></table>';
		
		var_dump( $rows );
		//echo $table;
		
		echo '<form action="' . get_permalink() . '" method="post">'. $table . implode( $hidden_fields ) . '<input type="submit" name="submit" value="Generate Charts"></form>';
	}
	
	private function get_charts_pdf_index() {
		
	}
	
	public function generate_charts_pdf( $ids ) {
		
		$res = new TW_Formstack();

		$object = array(
			'primary_object' => 'form',
			'primary_object_id' => '1725093',
			'sub_object' => 'submission'
		);

		$params = array (
			'data' => 0,
			'expand_data' => '',
		);

		$records = $res->request( $object , $params );
		
		foreach ( $records as $record ) {
			
			if ( in_array( $record->id , $ids ) ) {
				
				//var_dump( $record );
				
				$this->generate_chart_pdf( $record );
			}
		}
	}
	
	private function generate_chart_pdf( $record ) {
		
		// create pdf object
		
		$chart = $this->load_chart_template();
		
		//var_dump( $record );
		
		
		// iterate through all methods that insert each data element into charts
		
		// send record and pdf object to an array of function calls for each data element
		$data_points = array (
			'odh_id',
			'session_date',
			'agency_name',
			'agency_id',
			'site_name',
			'site_id',
			'dob',
			'state',
			'county',
			'zip_code',
			'ethnicity',
			'race',
			'sex',
			'gender',
			'previous_test',
			'previous_test_result',
			'sample_date_a',
			'worker_id_a',
			'election_a',
			'technology_a',
			'result_a',
			'result_provided_a',
			'no_result_a',
			'behavioral_risk_profile',
			'behavioral_risk_sexual_partners',
			'msm_partner',
			'male_partners',
			'behavioral_risk_c',
			'behavioral_risk_d',
			'behavioral_risk_e',
			'behavioral_risk_f',
			'behavioral_risk_g',
			'behavioral_risk_h',
			'behavioral_risk_i',
			'behavioral_risk_j',
			'behavioral_risk_k',
			'behavioral_risk_l',
			'behavioral_risk_m',
			'behavioral_risk_n',
			'behavioral_risk_o',
			'additional_risk_a',
			'additional_risk_b',
			'additional_risk_c',
			'additional_risk_d'
		);
		
		foreach ( $data_points as $data ) {
			
			if ( method_exists( $this , 'draw_data_point_' . $data ) ) {
				
				//echo '<br /> Renderer is defined';
				
				$chart = call_user_func_array( array( $this , 'draw_data_point_' . $data ) , array( $record , $chart ) );
				
			} else {
				
				//echo '<br /> Renderer is not defined';
			}
			
			
			
			//$chart = $this->draw_data_element_odh_id( $record , $chart );
		}
				
		
		// save chart
		
		$dir = wp_upload_dir();

		//$chart->save( $dir['basedir'] . '/pdf/' . $record->data->{'25165815'}->value . '-' . $record->data->{'25165816'}->value . '.pdf' );
	}
	
	private function load_chart_template() {
		
		$pdf = PdfDocument::load('/Users/thingwone/Sites/healthspotohio/wp-content/plugins/piras5/docs/ew_2014v2_part1.pdf');
		
		return $pdf;
	}
	
	private function draw_data_point_odh_id( $record , $pdf ) {
		
		//var_dump( $record );
		//var_dump( $pdf );
		
		$ew_form = $pdf->pages[0];

		// Apply font
		$ew_form->setFont( $this->font , 11 );

		$ew_form->drawText( $record->data->{'25165815'}->value , 150 , 707 );	// Client ID
		
		return $pdf;
	}
	
	private function draw_data_point_session_date( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );

		$ew_form->drawText( $record->data->{'25250082'}->value , 130 , 686 );	// Session Date
	
		return $pdf;
		
	}
	
	private function draw_data_point_agency_name( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		$ew_form->drawText( 'ATGC/AHF', 100 , 595 );	// Agency Name
		
		return $pdf;
	}
	
	private function draw_data_point_agency_id( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_site_name( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_site_id( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_dob( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25203849' ) ) {
			
			if ( $record->data->{'25203849'}->value == '1' ) {
				
				$ew_form->drawText( $record->data->{'25203950'}->value , 125 , 433 );	// Date of Birth
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_state( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25169210' ) ) {
			
			if ( $record->data->{'25169210'}->value == '1' ) {
				
				$ew_form->drawText( $record->data->{'25169128'}->value , 90 , 411 );	// State
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_county( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25169359' ) ) {
			
			if ( $record->data->{'25169359'}->value == '1' ) {
				
				$ew_form->drawText( $record->data->{'25169328'}->value , 175 , 392 );	// County
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_zip_code( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25169369' ) ) {
			
			if ( $record->data->{'25169369'}->value == '1' ) {
				
				$ew_form->drawText( $record->data->{'25169355'}->value , 130 , 368 );	// Zip Code
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_ethnicity( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25204036' ) ) {
			
			if ( $record->data->{'25204036'}->value == '1' ) {
				
				switch ( $record->data->{'25044945'}->value ) {
					
					case '0':
						$ew_form->drawText( 'X' , 40 , 323 );
						break;
					
					case '1';
						$ew_form->drawText( 'X' , 40 , 331 );
						break;
				}
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_race( $record , $pdf ) {
			
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25204021' ) ) {
			
			if ( $record->data->{'25204021'}->value == '1' ) {
				
				if ( is_string( $record->data->{'25168560'}->value ) ) {
					
					switch ( $record->data->{'25168560'}->value ) {
					
						case '1':	// White
							$ew_form->drawText( 'X' , 170 , 287 );
							break;
					
						case '2';	// Black/African American
							$ew_form->drawText( 'X' , 40 , 271 );
							break;
						
						case '3';	// Asian
							$ew_form->drawText( 'X' , 40 , 279 );
							break;
							
						case '4';	// Native HI/Pac. Islander
							$ew_form->drawText( 'X' , 40 , 263 );
							break;
							
						case '5';	// American IN/AK Native
							$ew_form->drawText( 'X' , 40 , 287 );
							break;
					}
					
				} elseif ( is_array( $record->data->{'25168560'}->value ) ) {
					
					foreach ( $record->data->{'25168560'}->value as $race ) {
						
						switch ( $race ) {
					
							case '1':	// White
								$ew_form->drawText( 'X' , 170 , 287 );
								break;
					
							case '2';	// Black/African American
								$ew_form->drawText( 'X' , 40 , 271 );
								break;
						
							case '3';	// Asian
								$ew_form->drawText( 'X' , 40 , 279 );
								break;
							
							case '4';	// Native HI/Pac. Islander
								$ew_form->drawText( 'X' , 40 , 263 );
								break;
							
							case '5';	// American IN/AK Native
								$ew_form->drawText( 'X' , 40 , 287 );
								break;
						}
					}
				}
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_sex( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25169084' ) ) {
			
			if ( $record->data->{'25169084'}->value == '1' ) {
				
				switch ( $record->data->{'25169070'}->value ) {
					
					case '1':	// Male
						$ew_form->drawText( 'X' , 40 , 235 );
						break;
					
					case '2';	// Female
						$ew_form->drawText( 'X' , 40 , 226 );
						break;
					
					/*case '3';	// Intersex
						$ew_form->drawText( 'X' , 40 , 331 );
						break;*/
				}
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_gender( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25169084' ) ) {
			
			if ( $record->data->{'25169084'}->value == '1' ) {
				
				$sex = $record->data->{'25169070'}->value;
			}
		}
		
		if ( property_exists( $record->data , '25169122' ) ) {
			
			if ( $record->data->{'25169122'}->value == '1' ) {
				
				$gender = $record->data->{'25169101'}->value;
			}
		}
		
		if ( $sex == '1' && $gender == '1' ) {
			
			// Male
			//echo '<p>Client is Male</p>';
			$ew_form->drawText( 'X' , 40 , 198 );
			
		} elseif ( $sex == '1' && $gender == '2' ) {
			
			// Transgender MTF
			//echo '<p>Client is Transgender (MTF)</p>';
			
		} elseif ( $sex == '2' && $gender == '2' ) {
			
			// Female
			//echo '<p>Client is Female</p>';
			$ew_form->drawText( 'X' , 40 , 190 );
			
		} elseif ( $sex == '2' && $gender == '1' ) {
			
			// Transgender FTM
			//echo '<p>Client is Transgender (FTM)</p>';
			
		} elseif ( $sex == '1' && ( $gender == '3' || $gender == '3a' ) ) {
			
			// Transgender Unspecified
			//echo '<p>Client is Transgender (Unspecified)</p>';
			
		}
		
		return $pdf;
	}
	
	private function draw_data_point_previous_test( $record , $pdf ) {
		
		/*$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25169084' ) ) {
			
			if ( $record->data->{'25169084'}->value == '1' ) {
				
				$sex = $record->data->{'25169070'}->value;
			}
		}*/
		
		return $pdf;
	}
	
	private function draw_data_point_previous_test_result( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_sample_date_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25250082' ) ) {
			
			$ew_form->drawText( $record->data->{'25250082'}->value , 350 , 705 );
		}
		
		return $pdf;
	}
	
	private function draw_data_point_worker_id_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 11 );
		
		if ( property_exists( $record->data , '25248205' ) ) {
			
			$i = 0;
			
			do {
				
				switch ( $i ) {
					
					case 0:
						$ew_form->drawText( substr( $record->data->{'25248205'}->value , $i , 1 ) , 340 , 669 );
						break;
					
					case 1:
						$ew_form->drawText( substr( $record->data->{'25248205'}->value , $i , 1 ) , 350 , 669 );
						break;
					
					case 2:
						$ew_form->drawText( substr( $record->data->{'25248205'}->value , $i , 1 ) , 360 , 669 );
						break;
					
					case 3:
						$ew_form->drawText( substr( $record->data->{'25248205'}->value , $i , 1 ) , 370 , 669 );
						break;
					
				}
				
				++$i;
				
			} while ( $i < 4 );			
		}
		
		return $pdf;
	}
	
	private function draw_data_point_election_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25166180' ) ) {
			
			switch ( $record->data->{'25166180'}->value ) {
				
				case '1.0':		// Anonymous
					$ew_form->drawText( 'X' , 341 , 652.5 );
					break;
				
				case '1.1':		// Confidential
					$ew_form->drawText( 'X' , 341 , 644.75 );
					break;
				
				case '1.2':		// Declined
					$ew_form->drawText( 'X' , 341 , 628.75 );
					break;
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_technology_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
		
		if ( property_exists( $record->data , '25166174' ) ) {
			
			$test_technology = $record->data->{'25166174'}->value;
			
			if ( $test_technology == '1' || $test_technology == '2' || $test_technology == '3' ) {
				
				$ew_form->drawText( 'X' , 341 , 606.5 );	// Rapid
				
			} elseif ( $test_technology == '4' ) {
				
				$ew_form->drawText( 'X' , 341 , 614.75 );	// Conventional				
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_result_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
			
		if ( property_exists( $record->data , '25203413' ) ) {
			
			switch ( $record->data->{'25203413'}->value ) {
				
				case '0':		// Non-Reactive
					$ew_form->drawText( 'X' , 341 , 568.25 );
					break;
				
				case '1':		// Reactive
					$ew_form->drawText( 'X' , 341 , 576.5 );
					break;
				
				case '0.1':		// Indeterminate
					$ew_form->drawText( 'X' , 341 , 560 );
					break;
				
				case '0.2':		// Invalid
					$ew_form->drawText( 'X' , 341 , 552 );
					break;
					
				case '0.3':		// No Result
					$ew_form->drawText( 'X' , 341 , 544.25 );
					break;
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_result_provided_a( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
			
		if ( property_exists( $record->data , '25245780' ) ) {
			
			switch ( $record->data->{'25245780'}->value ) {
				
				case '0':		// No
					$ew_form->drawText( 'X' , 341 , 529 );
					break;
				
				case '1':		// Yes
					$ew_form->drawText( 'X' , 341 , 520.5 );
					break;
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_no_result_a( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_behavioral_risk_profile( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		$ew_form->setFont( $this->font , 8 );
			
		if ( property_exists( $record->data , '26777153' ) ) {
			
			switch ( $record->data->{'26777153'}->value ) {
				
				case '0':		// Not Asked
					$ew_form->drawText( 'X' , 267.25 , 412.5 );
					break;
				
				case '1':		// Completed
					$ew_form->drawText( 'X' , 267.25 , 420 );
					break;
					
				case '2':		// No Risk Identified
					$ew_form->drawText( 'X' , 429.5 , 419.75 );
					break;
					
				case '3':		// Declined
					$ew_form->drawText( 'X' , 429.5 , 405.5 );
					break;
			}
			
		} else {
			
			$ew_form->drawText( 'X' , 267.25 , 412.5 );		// Not Asked
		}
		
		return $pdf;		
	}
	
	private function draw_data_point_behavioral_risk_sexual_partners( $record , $pdf ) {
		
		// sexual partners are male
		
		$ew_form = $pdf->pages[0];
			
		if ( property_exists( $record->data , '25169394' ) ) {
			
			if ( $record->data->{'25169394'}->value == '1' ) {
				
				$sexual_partners_gender = $record->data->{'25169384'}->value;
			}
		}
		
		if ( property_exists( $record->data , '25169405' ) ) {
			
			if ( $record->data->{'25169405'}->value == '1' ) {
				
				$sexual_behaviors = $record->data->{'25169406'}->value;
			}
		}
		
		if ( is_array( $sexual_behaviors ) ) {	// multiple sexual behaviors
			
			$dict_sexual_behaviors = array ( '2' , '2a' , '2b' , '3' , '3a' , '3b' );
			
			$bool_sexual_behaviors = false;
			
			foreach ( $dict_sexual_behaviors as $value ) {
				
				if ( in_array( $value , $sexual_behaviors ) ) {
					
					$bool_sexual_behaviors = true;
				}
			}
			
		} elseif ( is_string( $sexual_behaviors ) ) {	// single sexual behavior
			
			$dict_sexual_behaviors = array ( '2' , '2a' , '2b' , '3' , '3a' , '3b' );
			
			$bool_sexual_behaviors = false;
			
			foreach ( $dict_sexual_behaviors as $value ) {
				
				if ( $sexual_behaviors == $value ) {
					
					$bool_sexual_behaviors = true;
				}
			}
		}
		
		// determine if we're dealing with an array of values or a single value
		if ( is_array( $sexual_partners_gender ) ) {	// Client has had partners of multiple genders
			
			if ( in_array( '1' , $sexual_partners_gender ) ) {	// some partners have been Male
				
				if ( $bool_sexual_behaviors ) {
					
					$ew_form->drawEllipse( 493 , 344 , 507 , 350 );		// Client has had Vaginal or Anal sex with a male
					
				} else {
					
					$ew_form->drawEllipse( 445 , 344 , 459 , 350 );	// Client has not had Vaginal or Anal sex with a male
					
				}
				
			} else {	// no sexual partners have been Male
				
				$ew_form->drawEllipse( 445 , 344 , 459 , 350 );	// Client has not had Vaginal or Anal sex with a male
			}
			
			if ( in_array( '2' , $sexual_partners_gender ) ) {
				
				if ( $bool_sexual_behaviors ) {
					
					$ew_form->drawEllipse( 493 , 294 , 507 , 300 );		// Client has had Vaginal or Anal sex with a Female
					
				} else {
					
					$ew_form->drawEllipse( 445 , 294 , 459 , 300 );	// Client has not had Vaginal or Anal sex with a Female
					
				}
				
			} else {	// no sexual partners have been Male
				
				$ew_form->drawEllipse( 445 , 294 , 459 , 300 );	// Client has not had Vaginal or Anal sex with a Female
			}
			
			if ( in_array( '3' , $sexual_partners_gender ) ) {
				
				if ( $bool_sexual_behaviors ) {
					
					$ew_form->drawEllipse( 493.25 , 244.75 , 507.25 , 250.75 );		// Client has had Vaginal or Anal sex with a TG
					
				} else {
					
					$ew_form->drawEllipse( 445.25 , 244.75 , 459.25 , 250.75 );	// Client has not had Vaginal or Anal sex with a TG
					
				}
				
			} else {	// no sexual partners have been Male
				
				$ew_form->drawEllipse( 445.25 , 244.75 , 459.25 , 250.75 );	// Client has not had Vaginal or Anal sex with a TG
			}
			
		} elseif ( is_string( $sexual_partners_gender ) ) {		// Client has had partners of a single gender
			
			if ( is_array( $sexual_behaviors ) ) {	// multiple sexual behaviors
				
				$dict_sexual_behaviors = array ( '2' , '2a' , '2b' , '3' , '3a' , '3b' );
				
				$bool_sexual_behaviors = false;
				
				foreach ( $dict_sexual_behaviors as $value ) {
					
					if ( in_array( $value , $sexual_behaviors ) ) {
						
						$bool_sexual_behaviors = true;
					}
				}
				
			} elseif ( is_string( $sexual_behaviors ) ) {	// single sexual behavior
				
				$dict_sexual_behaviors = array ( '2' , '2a' , '2b' , '3' , '3a' , '3b' );
				
				$bool_sexual_behaviors = false;
				
				foreach ( $dict_sexual_behaviors as $value ) {
					
					if ( $sexual_behaviors == $value ) {
						
						$bool_sexual_behaviors = true;
					}
				}
			}
			
			if ( $sexual_partners_gender == '1') {
				
				if ( $bool_sexual_behaviors ) {
				
					$ew_form->drawEllipse( 493 , 344 , 507 , 350 );		// Client has had Vaginal or Anal sex with a male
					
				} else {
					
					$ew_form->drawEllipse( 445 , 344 , 459 , 350 );	// Client has not had Vaginal or Anal sex with a male
					
				}
				
			} else {
				
				$ew_form->drawEllipse( 445 , 344 , 459 , 350 );	// Client has not had Vaginal or Anal sex with a male
			}
			
			
			if ( $sexual_partners_gender == '2' ) {
				
				if ( $bool_sexual_behaviors ) {
				
					$ew_form->drawEllipse( 493 , 294 , 507 , 300 );		// Client has had Vaginal or Anal sex with a Female
					
				} else {
					
					$ew_form->drawEllipse( 445 , 294 , 459 , 300 );	// Client has not had Vaginal or Anal sex with a Female
					
				}
				
			} else {
				
				$ew_form->drawEllipse( 445 , 294 , 459 , 300 );	// Client has not had Vaginal or Anal sex with a Female
			}
			
			if ( $sexual_partners_gender == '3' ) {
				
				if ( $bool_sexual_behaviors ) {
				
					$ew_form->drawEllipse( 493.25 , 244.75 , 507.25 , 250.75 );		// Client has had Vaginal or Anal sex with a TG
					
				} else {
					
					$ew_form->drawEllipse( 445.25 , 244.75 , 459.25 , 250.75 );	// Client has not had Vaginal or Anal sex with a TG
					
				}
			} else {
				
				$ew_form->drawEllipse( 445.25 , 244.75 , 459.25 , 250.75 );	// Client has not had Vaginal or Anal sex with a TG
			}
		}
		
		return $pdf;
	}
	
	private function draw_data_point_male_partners ( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
		
		if ( $this->has_male_partners( $record ) ) {
			
			echo '<p>Client has male partners.</p>';
			
			$bio_sex = $this->get_client_biological_sex( $record );
			
			if ( $bio_sex == 'male' ) {
				
				if ( $this->valid_dqm( '25169405' , $record ) ) {
					
					$sexual_behaviors = $record->data->{'25169406'}->value;
			
					if ( is_string( $sexual_behaviors ) ) {
				
						// run through function
				
					} elseif ( is_array( $sexual_behaviors ) ) {
				
						// run through function
					}	
				}
				
			} elseif ( $bio_sex == 'female' ) {
				
				
			} elseif ( $bio_sex == 'intersex' ) {
				
				
			}
			
		} else {
			
			echo '<p>Client <b>DOES NOT</b> have male partners.</p>';
		}
		
		// determine if client has male partners regardless of sex/gender
		// if they don't have male partners it's pointless to run these functions
		// if client has male partners, determine their sex to find which data points to examine
		
		
		
		return $pdf;
	}
	
	private function draw_data_point_female_partners ( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_transgender_partners ( $record , $pdf ) {
		
		return $pdf;
	}
	
	private function draw_data_point_msm_partner( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];
			
		if ( property_exists( $record->data , '25201561' ) ) {
			
			if ( $record->data->{'25201561'}->value == '1' ) {
				
				switch ( $record->data->{'25201575'}->value ) {
					
					case 0:
						$ew_form->drawEllipse( 445.5 , 169.75 , 459.5 , 175.75 );
						break;
					
					case 1:
						$ew_form->drawEllipse( 493.5 , 169.75 , 507.5 , 175.75 );
						break;
				}
			}
		}
		
		return $pdf;
	}
	
	
	private function has_male_partners ( $record ) {
		
		if ( $this->valid_dqm( '25169394' , $record ) ) {
			
			$sexual_partners = $record->data->{'25169384'}->value;
			
			if ( is_string( $sexual_partners ) ) {
				
				if ( $sexual_partners != '1' ) {
					
					return false;
				}
				
			} elseif ( is_array( $sexual_partners ) ) {
				
				if ( !in_array( '1' , $sexual_partners ) ) {
					
					return false;
				}
			}
			
		} else {
			
			return false;
		}
		
		return true;
	}
	
	
	private function get_client_biological_sex ( $record ) {
		
		if ( $this->valid_dqm( '25169084' , $record ) ) {
			
			switch ( $record->data->{'25169070'}->value ) {
				
				case '1':
					$bio_sex = 'male';
					break;
					
				case '2':
					$bio_sex = 'female';
					break;
				
				case '3';
					$bio_sex = 'intersex';
					break;
			}
			
		} else {
			
			$bio_sex = false;
		}
		
		return $bio_sex;
	}
	
	
	private function valid_dqm ( $dqm_id , $record ) {
		
		if ( property_exists( $record->data , $dqm_id ) ) {
			
			$dqm = $record->data->$dqm_id->value;
			
			if ( $dqm != '1' ) {
				
				return false;
			}
			
		} else {
			
			return false;
			
		}
			
		return true;
	}
	
	
	private function draw_sexual_behaviors ( $sexual_behaviors , $pdf ) {
		
		if ( is_string( $sexual_behaviors ) ) {
			
			$list_sexual_behaviors = array( '2' , '2a' , '2b' , '3' , '3a' , '3b' );
			
			if ( in_array( $sexual_behaviors , $list_sexual_behaviors ) ) {
				
				echo 'Client HAS had Vaginal or Anal sex with a Male<br />';
				
			} else {
				
				echo 'Client HAS NOT had Vaginal or Anal sex with a Male<br />';
			}
			
		} elseif ( is_array( $sexual_behaviors  ) ) {
			
			
		}
		
		return $pdf;
	}
	
	
	public function save ( $pdf ) {
		
		
	}
}
?>