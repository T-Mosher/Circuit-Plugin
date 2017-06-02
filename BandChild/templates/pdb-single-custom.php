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

// define an array of fields to exclude here
$exclude = array();
$exclude[] = 'email';
$exclude[] = 'phone';
$exclude[] = 'announcer_script';
$exclude[] = 'spiel_doc';
$exclude[] = 'notes';
$exclude[] = 'transportation';
$exclude[] = 'secondary_contact_name';
$exclude[] = 'secondary_contact_email';
$exclude[] = 'secondary_contact_phone';

?>

<div class="wrap <?php echo $this->wrap_class ?>">

	
  <?php while ( $this->have_groups() ) : $this->the_group(); ?>
  
  <div class="section <?php $this->group->print_class() ?>" id="<?php echo Participants_Db::$prefix.$this->group->name ?>">
  
    <?php $this->group->print_title( '<h2 class="pdb-group-title">', '</h2>' ) ?>
    
    <?php $this->group->print_description() ?>
    
    
      <?php while ( $this->have_fields() ) : $this->the_field();
      
          // skip any field found in the exclude array
          if ( in_array( $this->field->name, $exclude ) ) continue;
					
          // CSS class for empty fields
					$empty_class = $this->get_empty_class( $this->field );
      
      ?>
    <dl class="<?php echo Participants_Db::$prefix.$this->field->name.' '.$this->field->form_element.' '.$empty_class?>">
      
      <dt class="<?php echo $this->field->name.' '.$empty_class?>"><?php $this->field->print_label() ?></dt>
      
      <dd class="<?php echo $this->field->name.' '.$empty_class?>"><?php $this->field->print_value() ?></dd>
    
    </dl>
  
    	<?php endwhile; // end of the fields loop ?>
    
  </div>
  
  <?php endwhile; // end of the groups loop ?>
  
  <?php
  // here is the custome code to list the entries from the 'entries' table for this unit
  // un-comment these lines to only run this code if someone is logged-in
  //if(!is_user_logged_in()){
  //  die('Entry list not supported yet...');
  //}
  
  global $wpdb;
  
  echo '<h4>Event Entries:</h4>';
  
  // DEBUG
  //var_dump($id);
  //echo '<br>';

  // get all of the fields of this record into an indexable array
  $part_id_fields = Participants_Db::get_participant($id);
  // DEBUG
  //echo '<br>';
  //var_dump($part_id_fields);
  //echo '<br>';
  
  // OK, that worked. Now try reading the "entries" database
  $query = "SELECT * FROM entries WHERE what_participant = ".$id;
  $entry_response = $wpdb->get_results($query, ARRAY_A);

  // DEBUG
  //echo '<br>Here are the entries<br>';
  //var_dump($entry_response);
  //echo '<br>';
  
  if (!empty($entry_response)) {
  	// start creating a table of events
  	echo '<table border="1" cell_spacing="3">';
  	echo '<tr><th>Date</th><th>Event</th><th>Location</th><th>Type</th></tr>';
  	
    	foreach($entry_response as $ent) {

	// for each entry, get the event, check if the event is active, then print the name and location in a table
	$query = 'SELECT * FROM events WHERE event_id = '.$ent['what_event'];
	
	// DEBUG
	//echo 'the event query<br>';
	//var_dump($query);
	//echo '<br>';
	
	$event_resp = $wpdb->get_results($query, ARRAY_A);
	  // DEBUG	
	  //echo '<br>Here is the event<br>';
	  //var_dump($event_resp);
	  //echo '<br>'; 
	  
	  // add check here for if event is active...
	  if(!empty($event_resp)) {
	    foreach($event_resp as $r) {
	    
	    //echo $r['event_date'].'<br>';
	    //echo $r['event_name'].'<br>';
	    //echo $r['event_location'].'<br>';
	    //echo $ent['entry_type'].'<br>';
	    	    
	     if ($r['event_active'] === 'T') {
		echo '<tr><td>'.$r['event_date'].'</td><td>'.$r['event_name'].'</td><td>'.$r['event_location'].'</td><td>'.$ent['entry_type'].'</td></tr>';
	      }
	    }
	  } else {
	    echo 'Oops, could not find event'.$ent['what_event'].'<br>';
	  }

	    	
   	}
   	echo '</table>';
  } else {
  	echo "No entries for this unit.";
  }
 

  
  // done listing the entries
  ?>
  
  
</div>