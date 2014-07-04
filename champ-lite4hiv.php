<?php
/**
 * @package CHAMP LITE4HIV
 * @version 0.0.2
 */
/*
Plugin Name: CHAMP LITE4HIV
Plugin URI: http://healthspotohio.org/technology
Description: Client Health Activity Management Platform - Lightweight Information Transcription Environment for HIV Testing. Plugin for accessing ATGC-specific, HIV testing information from Formstack and converting it to ODH/CDC compatible EvaluationWeb documentation
Author: Miquel Brazil
Version: 0.0.2
Author URI: http://wone.co
*/

require_once 'lib/vendor/zend/ZendPdf/vendor/autoload.php';

use ZendPdf\PdfDocument;

//$pdf = PdfDocument::load('/Users/thingwone/Sites/healthspotohio/wp-content/plugins/champ-lite4hiv/docs/test.pdf');
//var_dump($pdf);