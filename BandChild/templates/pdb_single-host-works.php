<?php
/*
 * default template for displaying a single record
 * 
 * this is the new "WordPress style" template
 *
 * each group with the "visible" attribute checked will display its fields in the order set 
 * in the manage database fields page.
 *
 * if there are specific fields you wish to exclude from display, you can include the "name" value of 
 * the field in the $exclude array like this: $exclude = array( 'city','state','country' ); or whatever 
 * you want. Leave it empty (like it is here) if you don't want to exclude any fields.
 *
 * this template is a simple demonstration of what is possible
 *
 * for those unfamiliar with PHP, just remember that something like <?php echo $group->name ?> just prints out 
 * the group name. You can move it around, but leave all the parts between the <> brackets as they are.
 *
 */

// define an array of fields to exclude from public list
$exclude_public = array('email', 'phone', 'billing_street', 'billing_city', 'ack', 'design_staff', 'instruction_staff', 'announcer_script', 'spiel_doc', 'notes', 'transportation', 'secondary_contact_name', 'secondary_contact_email', 'secondary_contact_phone');

// define an array of fields to exclude from the host list
$exclude_host = array('billing_street','billing_city','ack');

// define an array into which all of the unit data is stored temporaraily
//   it's sent to the browser for display as HTML, and optionally downloaded as plain text for show hosts
$unit_info = array();

?>

<div class="wrap <?php echo $this->wrap_class ?>">
  <?php if (is_user_logged_in()) echo "<h3>--- Host Mode ---</h3>";?>
  
<!--	<form action="output_me()"> <input type="submit"> </form> -->

 	
  <?php while ( $this->have_groups() ) : $this->the_group(); ?>
  
  <div class="section" id="<?php echo Participants_Db::$prefix.$this->group->name ?>">

    <?php $this->group->print_title( '<h2>', '</h2>' ) ?>
    
    <?php $this->group->print_description( '<p>', '</p>' ) ?>
    
    
      <?php while ( $this->have_fields() ) : $this->the_field();
       	  if (! is_user_logged_in()) {
	  	
          	// skip any field found in the exclude array
          	if ( in_array( $this->field->name, $exclude_public ) ) continue;
          } else {
          	if ( in_array( $this->field->name, $exclude_host ) ) continue;
          }
					
          // CSS class for empty fields
		$empty_class = $this->get_empty_class( $this->field );
      
      ?>
    <dl class="<?php echo Participants_Db::$prefix.$this->field->name.' '.$this->field->form_element.' '.$empty_class?>">
      
      <?php $text = $this->field->print_label(false);
      array_push($unit_info, "<br>".$text.":");?>
      
      <dt class="<?php echo $this->field->name.' '.$empty_class?>"><?php echo $text ?></dt>

      <?php $text = $this->field->print_value(false);
      array_push($unit_info, $text);?>
      
      <dd class="<?php echo $this->field->name.' '.$empty_class?>"><?php echo $text ?></dd>
    
    </dl>
  
    	<?php endwhile; // end of the fields loop ?>
    <?php
    if (is_user_logged_in()) {
	$filename = 'none';
    	$arr_len = count($unit_info);
    	
    	// for debugging, output all the unit values to the browser
    	for($i = 0; $i < $arr_len; $i++){
    		echo $unit_info[$i];
    		echo "<br>";
    	}
    	// initially, automatically send a download file of all the fields
    	//     later, add a button.
        // create a file pointer connected to the output stream
        //$output = fopen('php://output', 'w');

        //header('Content-type: application/csv'); // some sources say it should be this
        //header('Content-Type: text/csv; charset=utf-8');
        //header("Cache-Control: no-store, no-cache");
        //header('Content-Disposition: attachment; filename="' . $filename . '"');

        // output the data lines
        //foreach ($data as $line) {
        //	fputcsv($output, $line, ',', self::$CSV_enclosure);
        //}
        //if ($arr_len > 2) {
        //	$filename = $unit_info[2];
        //}
	//for($i = 0; $i < $arr_len; $i++){
        //	fwrite($output, $unit_info[i]);
        //}
        //fclose($output);

        // we must terminate the script to prevent additional output being added to the CSV file
        //exit;  	
    	
    	}
    
    ?>
  </div>
  
  <?php 
  // endo of the loops group
  endwhile;
   ?>
  
</div>


<?php
//function output_me() {} 
/* here's where we put the file output handler


 if (!empty($filename)) {

          // create a file pointer connected to the output stream
          $output = fopen('php://output', 'w');

          //header('Content-type: application/csv'); // some sources say it should be this
          header('Content-Type: text/csv; charset=utf-8');
          header("Cache-Control: no-store, no-cache");
          header('Content-Disposition: attachment; filename="' . $filename . '"');

          // output the data lines
          foreach ($data as $line) {
            fputcsv($output, $line, ',', self::$CSV_enclosure);
          }

          fclose($output);

          // we must terminate the script to prevent additional output being added to the CSV file
          exit;
        }
*/
?>