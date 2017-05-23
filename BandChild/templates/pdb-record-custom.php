<?php
/*
 * Customized default template for the [pdb_record] shortcode for editing a record on the frontend
 * Customized by tlm for supporting separate events and entries databases
 *   and a link to the fee invoice page
 * this template uses a table to format the form
 */
?>
<div class="wrap <?php echo $this->wrap_class ?>">

  <?php // output any validation errors
 $this->print_errors(); ?>
  
  <?php // print the form header
  $this->print_form_head()
  ?>
  
  <?php while ($this->have_groups()) : $this->the_group(); ?>
    <?php $this->group->print_title() ?>
    <?php $this->group->print_description() ?>
    
    <table  class="form-table">
      
      <tbody class="field-group field-group-<?php echo $this->group->name ?>">

      <?php
      // step through the fields in the current group
      
        while ($this->have_fields()) : $this->the_field();
          ?>
      
      <tr class="<?php $this->field->print_element_class() ?>">
      
        <th><?php $this->field->print_label() ?></th>
        <td id="<?php $this->field->print_element_id() ?>">
        
          <?php $this->field->print_element(); ?>
          
              <?php if ($this->field->has_help_text()) : ?>
          <span class="helptext"><?php $this->field->print_help_text() ?></span>
          <?php endif ?>
          
        </td>
        
      </tr>
      
      <?php endwhile; // field loop ?>
      
      </tbody>

    </table>
    
  <?php endwhile; // group loop ?>
    <table class="form-table">
      
    <tbody class="field-group field-group-submit">

      <tr>
        <th><h3><?php $this->print_save_changes_label() ?></h3></th>
        <td class="submit-buttons">
          <?php $this->print_submit_button('button-primary'); // you can specify a class for the button ?>
        </td>
      </tr>
      
    </tbody>

    </table><!-- end group -->
  
  <?php $this->print_form_close() ?>
  <?php
	echo "--- pdb-record-custom template starts here ---<br>";
	
	// this seems like a convenient dirty method for getting the private id
	// really should be able to do something better
	$priv_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	
	global $wpdb;
	
	echo "--- Will have event entries here ---<br>";
	//echo "-- get get_class<br>";
	//print_r(get_class($this));
	//echo "<br>...end...<br>";
	//echo "-- get all class methods<br>";
	//foreach(get_class_methods($this) as $t) {
	//	print_r($t);
	//	echo "<br>";
	//}
	//echo "...end...<br>";
	
	//echo "-- get the object variables<br>";
	//print_r(get_object_vars($this));
	//echo "<br>";
	
	//echo "-- get the_record";
	// Note: this doesn't work...
	//print_r($this->the_record());
	//echo "<br>... end ...<br>";
	
	//if (isset($participant_id)) {
	//	echo "Participant ID is set<br>";
	//}
	
	$part_id = $this->participant_id;
	echo "The participant ID is:".$part_id."<br>";

	// query the hjk_participants_database table directly for this participant, 
	//    so we can get the unit_type
	$query = "SELECT * FROM hjk_participants_database WHERE id = ".$part_id;
	$response = $wpdb->get_results($query, ARRAY_A);
	foreach ($response as $row){
		// this really needs some error checking...
		echo "unit type is: ".$row['unit_type']."<br>";
		$unit_type = $row['unit_type'];
	}

	// create a query for the this unit's entries 	
	$query = "SELECT * FROM entries WHERE what_participant = ".$part_id;
	//$query = "SELECT * FROM entries WHERE what_participant = 117";
	//print_r($query);
	//echo '<br>';
	$response = $wpdb->get_results($query, ARRAY_A);
	// event list starts as an empty array
	$event_list = array();
	//print_r($response);
	if (!empty($response)) {
		foreach($response as $row) {	
			echo 'Event:'.$row['what_event'].' Participant:'.$row['what_participant'].'<br>';
			// built an array with this unit's events by pushing new elements
			$event_list[] = $row['what_event'];
		}	
	} else {
		echo 'Sorry, no event entries found for this unit.<br>';
	}	
	
	// DEBUG
	var_dump($event_list);
	echo '<br>';
	var_dump(strtoupper($unit_type));
	echo '<br>';
	
	// create a query for all active events for this unit type

	// for testing, this just gets all events by date
	//$query = "SELECT * FROM events WHERE event_active = 'T' ORDER BY event_date ASC";
	
	// query for active events for this unit type
	//$query = "SELECT * FROM events WHERE (event_active = 'T' AND event_type = \'".$unit_type."'") ORDER BY event_date ASC";
	//$query = "SELECT * FROM events WHERE (event_active = 'T' AND event_type = 'Band') ORDER BY event_date ASC";
	//$query = "SELECT * FROM events WHERE (event_active = 'T' AND (find_in_set(strtoupper($unit_type), event_type) > 0)) ORDER BY event_date ASC";
	$unit_type_uc = strtoupper($unit_type);
	$query = "SELECT * FROM events WHERE find_in_set('".$unit_type_uc."', event_type) > 0";
	// NOTE: also need to do this for other event types...
	
	$response = $wpdb->get_results($query, ARRAY_A);
	echo "the private id is:".$priv_id.'<br>';
	
	// start the HTML submit form -----------------------------------
	echo '<form action="/add-entries/?pid='.$priv_id.'" method="post">';
	
	//print_r($response);
	if (!empty($response)) {
		foreach($response as $row) {	
			//echo 'key:'.$row['event_id'].' Date:'.$row['event_date'].' Name:'.$row['event_name'].' Type:'.$row['event_type'].'<br>';
			// show whether this unit has entered this event
			// use array_search($event_list, ...)
			$eid = $row['event_id'];
			
			// note if "array_search()" returns index 0, it is also bool FALSE
			// so use "if_int()", not '=='
			$yep = array_search($eid, $event_list);
			
			//if ($yep == FALSE) {
			if (!is_int($yep)) {
				//echo "event entered? False<br>";
				$tag = 'input type="checkbox" name="eids[]" value = '.$eid;
				echo $tag.'<br>';
				echo '<'.$tag.'>';
			
			}else {
				//echo "event entered? True<br>";
				$tag = 'input type="checkbox" name="eids[]" value = '.$eid.' checked';
				echo $tag.'<br>';
				echo '<'.$tag.'>';
			}
			echo $eid.' Date:'.$row['event_date'].' Name:'.$row['event_name'].' Type:'.$row['event_type'].'<br>';			
		}	
	} else {
		echo 'Sorry, found no events of this type.<br>';
	}
	echo '<input type="submit" name="submit" value="Sendo"/>'; 
	echo '</form>';
	// end of form -------------------------------------------------------
	
	// create a query for the entries
	//$query = "SELECT * FROM entries";
	
	
	// this seems like a convenient dirty method for getting the private id
	// really should be able to do something better
	//$priv_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	
	echo "<h3>--- Paying your fees ---</h3>";
	echo "Will have link to invoice here<br>";
	//echo "<a title=\"Fee Invoice\" href=\"http://nwapa.net/fee-invoice/?pid=".$priv_id."\">Click here for payment options.</a>";
	echo "<a title=\"Fee Invoice\" href=\"/fee-invoice/?pid=".$priv_id."\">Click here for payment options.</a>";
	echo "<br><br>";
  ?>
  
  
  
  
</div>