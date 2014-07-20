<?php

use ZendPdf\PdfDocument;
use ZendPdf\Font;
	
class HSO_Chart {

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
			//'data' => 0,
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
		
		//var_dump( $chart );
		
		// send record and pdf object to an array of function calls for each data element
		
		$chart = $this->draw_data_element_odh_id( $record , $chart );
		
		$dir = wp_upload_dir();

		$chart->save( $dir['basedir'] . '/pdf/' . $record->data->{'25165815'}->value . '-' . $record->data->{'25165816'}->value . '.pdf' );
	}
	
	private function load_chart_template() {
		
		$pdf = PdfDocument::load('/Users/thingwone/Sites/healthspotohio/wp-content/plugins/champ-lite4hiv/docs/ew_2014v2_part1.pdf');
		
		return $pdf;
	}
	
	private function draw_data_element_odh_id( $record , $pdf ) {
		
		$ew_form = $pdf->pages[0];

		$font = Font::fontWithName(Font::FONT_HELVETICA_BOLD);
  
		// Apply font
		$ew_form->setFont($font, 11);

		$ew_form->drawText( $record->data->{'25165815'}->value , 150 , 707 );	// Client ID
		
		return $pdf;
	}
	
	private function draw_data_element_session_date() {
		
	}
	
	public function save ( $pdf ) {
		
		
	}
}
?>