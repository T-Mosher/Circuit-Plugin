<?php
/*
Template Name: BP Payment Form
May 2017:
Reads the entries from the db table.
Reads the event fees from the events db table.
*/
?>
<?php $eud_due = 0;?>
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
<input name="amount" value="<?php echo number_format("$eud_due", 2,'.','');?>">
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
	global $eud_due;
	global $eud_unit_name;
	$prem_event = 0;
	$std_event = 0;
	$pri_event = 0;
	
	// for May 2017 update
	$fee_total = 0;
	
	// for 2016 Fall, no membership fee, all events $325 ($75 goes to to NWAPA)
	// for 2015/16 Winter, no membership fee, all events $225
	// fee is $250 for Winter 2017 
	define ("PREM_COST", 250.00);	// entry fee for a premiere event
	define ("REG_COST", 250.00);	// entry fee for a regular season event
	define ("CHAMP_COST", 250.00);	// entry fee for the Championships event
	define ("MEM_FEE", 0.00);	// fee for membership
	
	// defines for $event_array
	define ("NAME", 0);			// name of event
	define ("FEE", 1);			// event entry fee

	//$cmd = $_POST['eud_cmnd'];
	
	// get the participant id
	if (!isset($participant_id)) {
		// if there is no id in the request, use the default record
		$participant_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	}

	// does the $participant_id need more error checking?
	
	$eud_id = Participants_Db::get_participant_id($participant_id);

	// DEBUG
	//echo '<br>participant_id is ';
	//var_dump($eud_id);
	//echo '<br>';

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
	echo '<br>';
	var_dump($event_array);
	echo '<br>';
	
	//echo 'Test Array<br>';
	//$test_array = array();
	//$test_array['15'][0] = 100;
	//$test_array['15'][1] = 'Some Name';
	//$test_array['15'][2] = 'Some Location';
	//$test_array['17'] = 101;
	//$test_array['18'] = 102;
	
	//print_r(count($test_array));
	//echo '<br>';
	
	//echo 'test array [1] = ';
	//var_dump($test_array);
	
	echo '<br>';
	var_dump($event_array['17']);
	echo '<br>';
		
	var_dump(array_key_exists(17,$event_array));
	//var_dump(array_keys($event_array));
	echo '<br>';
	
	echo 'here are my events<br>';
	var_dump($entry_response);
	echo '<br>';
	
	// for each entry, check if it is for an active event, total the fee, and 
	//   add a row to the HTML fee summary table
	
	//echo '<table>';
	
	if (!empty($entry_response)) {
		foreach($entry_response as $entry) {
			if(array_key_exists($entry['what_event'],$event_array)) {
				echo 'Found an active event<br>';
				// also need to check if the entry_type is "COMPETING"
				// add this entry fee to the total
				$ev_name = $event_array[$entry['what_event']][NAME];
				echo 'event:'.$ev_name.'<br>';
				$ev_fee = $event_array[$entry['what_event']][FEE];
				echo 'fee:'.$ev_fee.'<br>';
				$fee_total = $fee_total + $ev_fee;
			} else {
				echo 'This event is not active<br>';
			}
	
	
		}
	}
	
	echo 'total fee:'.$fee_total.'<br>';
	echo '<br>';
	
	// get all of the fields of this record into an indexable array
	$eud_fields = Participants_Db::get_participant($eud_id);
	
	$eud_unit_name = $eud_fields["unit_name"];
	//var_dump($eud_fields);
	
	// create an empty array of event text labels
	$event_array = array();
	
	// Compute how many of each type of event is entered
	// Note: 
	// Adjust the prem_events for Winter season (one each for guard and percussion)
	// Typically     Only one premier event for Fall
	if ($eud_fields["event_1"] == "Yes") {
		// this is usually $prem_event for fall and winter
		$prem_event += 1;
		$event_array[] = "Guard Premier @ Tigard";
	}
	if ($eud_fields["event_2"] == "Yes") {
		// this is $std_event for Fall, and $prem_event for winter
		$prem_event += 1;
		$event_array[] = "Percussion Premier @ Glencoe";
	}
	if ($eud_fields["event_3"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Regular Show @ St. Helens ";
	}
	if ($eud_fields["event_4"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Regular Show @ Evergreen";
	}
	if ($eud_fields["event_5"] == "Yes") {
		$pri_event += 1;
		$event_array[] = "Guard Championships @ Liberty";
	}
	if ($eud_fields["event_6"] == "Yes") {
		$pri_event += 1;
		$event_array[] = "Percussion/Winds Champinoships @ West Salem";
	}
	if ($eud_fields["event_7"] == "Yes") {
		$std_event += 0;
		$event_array[] = "Event 7";
	}
	if ($eud_fields["event_8"] == "Yes") {
		$pri_event += 0;
		$event_array[] = "Event 8";
	}
	if ($eud_fields["event_9"] == "Yes") {
		$std_event += 0;
		$event_array[] = "Event 9";
	}
	if ($eud_fields["event_10"] == "Yes") {
		$std_event += 0;
		$event_array[] = "Event 10";
	}
	if ($eud_fields["event_11"] == "Yes") {
		$std_event += 0;
		$event_array[] = "Event 11";
	}
	
	// for testing
	//print_r($event_array);
		
	// echo "<br>Totals: ".$std_event." ".$pri_event,"<br>";
	
	
	// retrieve the balance - typ. surety bond or show host credit
	if (isset($eud_fields["balance"])) {
		//echo "found a balance:".$eud_fields["balance"]."<br>";
		$eud_credit = $eud_fields["balance"];
	} else {
		//echo "no balance<br>";
		$eud_credit = 0;
	}
	
	// exhibition units pay no fee, so credit them their payment amount
	$class = $eud_fields['classification'];
	
	//var_dump($class);
	//echo '<br>';
	
	if (stristr($class, 'exhib') !== FALSE) {
		echo '<br>Note: Exhibition units pay no event fees<br>';
		// note- quick and dirty, only works for 2017 winter fees
		$eud_credit = ($prem_event + $std_event + $pri_event) * REG_COST;
		}
	
	// retrieve the total previously paid
	if (isset($eud_fields["paid_amt"])) {
		//echo "found previous payments<br>";
		//echo "paid_amt:".$eud_fields["paid_amt"]."<br>";
		//echo "payment_date:".$eud_fields["payment_date"]."<br>";
		$eud_paidamt = $eud_fields["paid_amt"];
		if(isset($eud_fields["payment_date"])){
			$eud_paymentdate = date('M d, Y', $eud_fields["payment_date"]);
		} else {
			$eud_paymentdate = "n/a";
		}	
		//echo $eud_paymentdate;
	} else {
		//echo "no previous payments<br>";
		$eud_paidamt = 0;
		$eud_paymentdate = "   ";
	}
	
	// compute if membership fee is due - verify this logic for fall and winter seasons
	if (($prem_event + $std_event + $pri_event) > 1) {
		//echo "Membership fee is due.<br>";
		$member_str = "Rqd";
		$member_fee = MEM_FEE;
	} else {
		$member_fee = 0;
		$member_str = "n/a";
	}
	
	// calculate the values for the invoice table
	$eud_prem_cost = $prem_event * PREM_COST;
	$eud_reg_cost = $std_event * REG_COST;
	$eud_pri_cost = $pri_event * CHAMP_COST;
	$eud_subtotal = $eud_prem_cost + $eud_reg_cost + $eud_pri_cost + $member_fee;
	$eud_due = $eud_subtotal - $eud_paidamt - $eud_credit;
	
	// here we format the output for the screen
	?>
	<!-- switch to HTML with php inserts-->
	<html>
	<h2>NWAPA Fee Payment Invoice</h2>
	<h3>Unit: <?php echo $eud_unit_name;?></h3>
	<p><?php echo date("M d, Y H:i:s")." UTC"?></p>
	<table border="1" cellpadding='6'>
	<tbody>
	<tr>
		<td><b>Item</b></td><td><b>Qty.</b></td><td><b>Each</b></td><td><b>Amount</b></td>
	</tr>
	<tr>
		<td>Premier Event</td><td><?php echo $prem_event?></td><td><?php echo "$ ".PREM_COST?></td><td><?php echo "$ ".$eud_prem_cost?></td>
	</tr>
	<tr>
		<td>Reg. Events</td><td><?php echo $std_event?></td><td><?php echo "$ ".REG_COST?></td><td><?php echo "$ ".$eud_reg_cost?></td>
	</tr>
	<tr>
		<td>Champs. Event</td><td><?php echo $pri_event?></td><td><?php echo "$ ".CHAMP_COST?></td><td><?php echo "$ ".$eud_pri_cost?></td>
	</tr>
	<tr>
		<td>NWAPA Membership</td><td><?php echo $member_str?></td><td><?php echo "$ ".MEM_FEE?></td><td><?php echo "$ ".$member_fee?></td>
	</tr>
	<tr>
		<td><b>Subtotal</b></td><td></td><td></td><td><?php echo "$ ".$eud_subtotal?></td>
	</tr>
	<tr>
		<td>Previous Payments<br>  <?php echo $eud_paymentdate?></td><td></td><td></td><td><?php echo " $ ".$eud_paidamt?></td>
	</tr>
	<tr>
		<td>Credit</td><td></td><td></td><td><?php echo "- $ ".$eud_credit?></td>
	</tr>
	<tr>
		<td><b>Balance Due</b></td><td></td><td></td><td><?php echo " $ ".$eud_due?></td>
	</tr>
	</tbody>
	</table>
	<h4>Events Entered:</h4>
	<?php
		$arrlength = count($event_array);
		for($x = 0; $x < $arrlength; $x++) {
    			echo $event_array[$x];
    			echo "<br>";
		}
	?>
	
	<br><h3>Option 1: Print invoice and send payment to:</h3>
	NWAPA Treasurer<br>
	PO Box 91308<br>
	Portland, OR 97291<br><br>
	
	<h3>Official use only: </h3>
	<p>Date payment received: ________________</p>
	<p>Check number: ____________</p>
	<p>Invoice Rev. 3</p> 
	<!--<?php
	echo "Membership fee:".$member_fee."<br>";
	echo "Event Total:".$eud_total."<br>";
	echo "     Credit:".$eud_credit."<br>";
	echo "    Amt Due:".sprintf("%f",$eud_due)."<br>";
	echo "<br>";?>
	-->
	</html>
<?php
} ?>