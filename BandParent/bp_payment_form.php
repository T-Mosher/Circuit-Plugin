<?php
/*
Template Name: BP Payment Form
May 2017:
Reads the entries from the db table.
Reads the event fees from the events db table.
*/
?>
<?php $fee_total = 0;?>
<html>
<body>
<?php /* get_header() */;?>

<?php bppf_display_invoice(); ?>

<button onclick="bppf_printme()">Click to print invoice</button>
<script>
function bppf_printme() {
	window.print();
}
</script>

<h3>Option 2: Pay now using PayPal</h3>
Balance due:<br>
<!-- customized PayPal button -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="P5E8ETJU7CM4E">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="NWAPA Fees">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Add special instructions to the seller:">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="currency_code" value="USD">
<input name="amount" value="<?php echo number_format("$fee_total", 2,'.','');?>">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHosted">
<table>
<tr><td><input type="hidden" name="on0" value="Unit Name">Unit Name</td></tr><tr><td><input type="text" name="os0" value="<?php echo $eud_unit_name;?>" maxlength="200"></td></tr>
</table>
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<br>
</body>
</html>
<?php /* get_footer(); */ ?>
<!-- end of main -->

<?php function bppf_display_invoice() {
	global $wpdb;
	global $eud_unit_name;
	
	// for May 2017 update
	global $fee_total;
	
	define ("MEM_FEE", 0.00);	// fee for membership
	
	// defines for $event_array
	define ("NAME", 0);			// name of event
	define ("FEE", 1);			// event entry fee

	// get the participant id
	//if (!isset($participant_id)) {
	//	// if there is no id in the request, use the default record
	//	$participant_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	//}
	if (isset($_REQUEST['pid'])) {
		$participant_id = $_REQUEST['pid'];
	} else {
		die('<br><b>Error:</b> There was no particpant ID requested.');
	}
		
	$eud_id = Participants_Db::get_participant_id($participant_id);
	// get all of the fields of this record into an indexable array
	$eud_fields = Participants_Db::get_participant($eud_id);
	$eud_unit_name = $eud_fields["unit_name"];
	
	//var_dump($eud_fields);
	
	// set up the invoice header
	echo '<h2>NWAPA Fee Payment Invoice</h2>';
	echo '<h3>Unit: '.$eud_unit_name.'</h3>';
	echo '<p>Date: '.date("M d, Y H:i:s").' UTC</p>';
	
	// set up the invoice fee table
	echo '<table border="1" cellpadding="6">';
	echo '<tr><th>Event Name</th><th>Fee Amount</th></tr>';
	
	// comment-out the next line to add rows to the table
	//echo '</table>';	
	
	// query the entries db and get a list of all events for this unit
	// note: this query includes previous events which are now inactive
	$query = "SELECT what_event, entry_type FROM entries WHERE what_participant = ".$eud_id;
	$entry_response = $wpdb->get_results($query, ARRAY_A);

	// query the events database for all active events
	$query = "SELECT event_id, event_name, event_entry_fee FROM events WHERE event_active = 'T' ORDER BY event_date";
	$event_response = $wpdb->get_results($query, ARRAY_A);
	
	// build the events into an associative array with event_id as the key, with name and fee as data
	$event_array = array();
	if (!empty($event_response)) {
		foreach($event_response as $row) {	
			//echo 'Event:'.$row['what_event'].' Participant:'.$row['what_participant'].'<br>';
			// built an array with this unit's events by pushing new elements
			$event_array[$row['event_id']][NAME] = $row['event_name'];
			$event_array[$row['event_id']][FEE] = $row['event_entry_fee'];
			//var_dump($row);
			//echo '<br>';
			//$event_array[] = ($row['event_id'] => $row['event_entry_fee']);
		}
	} else {
		echo 'No event entries found for this unit.<br>';
	}
	
	// DEBUG
	//echo '<br>';
	//var_dump($event_array);
	//echo '<br>';
	
	// DEBUG
	//echo '<br>';
	//var_dump($event_array['17']);
	//echo '<br>';
		
	//var_dump(array_key_exists(17,$event_array));
	//var_dump(array_keys($event_array));
	//echo '<br>';
	
	// DEBUG
	//echo 'here are my events<br>';
	//var_dump($entry_response);
	//echo '<br>';
	
	// for each entry, check if it is for an active event, total the fee, and 
	//   add a row to the HTML fee summary table
	
	if (!empty($entry_response)) {
		foreach($entry_response as $entry) {
			if(array_key_exists($entry['what_event'],$event_array)) {
				//echo 'Found an active event<br>';
				// also need to check if the entry_type is "COMPETING"
				//    so that host units aren't charged a fee
				// add this entry fee to the total
				$ev_name = $event_array[$entry['what_event']][NAME];
				//echo 'event:'.$ev_name.'<br>';
				echo '<tr><td>'.$ev_name.'</td>';
				$ev_fee = $event_array[$entry['what_event']][FEE];
				//echo 'fee:'.$ev_fee.'<br>';
				echo '<td>'.number_format($ev_fee,2).'</td><tr>';
				$fee_total = $fee_total + $ev_fee;
			} else {
				//echo 'This event is not active<br>';
			}
	
	
		}
	}
	// add the membership fee
	echo '<tr><td>Membership Fee</td><td>'.number_format(MEM_FEE,2).'</td></tr>';
	//echo 'total fee:'.$fee_total.'<br>';
	echo '<tr><td><b>Total Fees Due</b></td><td><b> $'.number_format($fee_total + MEM_FEE, 2).'</b></td></tr>';
	echo '</table>';
	echo '<br>';
	
	?>
	<html>

	<h3>Payment Option 1: Print invoice and send payment to:</h3>
	NWAPA Treasurer<br>
	PO Box 91308<br>
	Portland, OR 97291<br><br>
	
	<h3>Official use only: </h3>
	<p>Date payment received: ________________</p>
	<p>Check number: ____________</p>
	<p>Invoice Rev. 4</p> 

	</html>
<?php
} ?>