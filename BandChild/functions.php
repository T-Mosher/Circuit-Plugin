<?php
function nwapaGetUnitInfo(string $id) {
	echo '<br>Testing the function<br>';

}
function var_disp($var) {
	// this function puts some HTML framing around var_dump()'s output
	
	echo '<br>';
	var_dump($var);
	echo '<br>';
}

function get_nwapa_settings($param) {
/*
* Read a parameter $param from the nwapa_settings table
*/
  global $wpdb;
  $val = FALSE;
  $msg = NULL;
  $query = "SELECT * FROM nwapa_settings";
  $response = $wpdb->get_results($query, ARRAY_A);
  // Debug
  //var_disp($response);
  if (!empty($response)){
	  foreach($response as $r) {
		  if (strcmp($r['parameter'], $param) == 0) {
			  $val = $r['value'];
			  $msg = $r['message'];
		} 
	  }
  } else {
	  die('<br>No nwapa settings table found<br>');
  }
  return array('val'=>$val,'msg'=>$msg);
}


?>