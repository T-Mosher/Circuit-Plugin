<?php
/*
Template Name: BP Payment Form
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
	global $eud_due;
	global $eud_unit_name;
	$prem_event = 0;
	$std_event = 0;
	$pri_event = 0;
	// for 2016 Fall, no membership fee, all events $325 ($75 goes to to NWAPA)
	// for 2015/16 Winter, no membership fee, all events $225
	define ("PREM_COST", 325.00);	// entry fee for a premiere event
	define ("REG_COST", 325.00);	// entry fee for a regular season event
	define ("CHAMP_COST", 325.00);	// entry fee for the Championships event
	define ("MEM_FEE", 0.00);	// fee for membership

	$cmd = $_POST['eud_cmnd'];
	
	if (!isset($participant_id)) {
	
	// if there is no id in the request, use the default record
		$participant_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	}

	/* if (!isset($participant_id)) {
		// if there is no id in the request, use the default record
		echo "Participant ID isn't defined.<br>";
	} else {
		echo "Participant ID is:".$participant_id."<br>";
	} */
	
	$eud_id = Participants_Db::get_participant_id($participant_id);
	/* comment this out */
	/* echo "id:".$eud_id."<br>"; */

	// get all of the fields of this record into an indexable array
	$eud_fields = Participants_Db::get_participant($eud_id);
	
	$eud_unit_name = $eud_fields["unit_name"];
	
	// create an empty array of event text labels
	$event_array = array();
	
	// Compute how many of each type of event is entered
	// Note: 
	// Adjust the prem_events for Winter season (one each for guard and percussion)
	// Typically     Only one premier event for Fall
	if ($eud_fields["event_1"] == "Yes") {
		// this is usually $prem_event for fall and winter
		$std_event += 1;
		$event_array[] = "McKenzie Classic (Evergreen)";
	}
	if ($eud_fields["event_2"] == "Yes") {
		// this is $std_event for Fall, and $prem_event for winter
		$std_event += 1;
		$event_array[] = "Pac. Coast Invit. (Sprague)";
	}
	if ($eud_fields["event_3"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Music in Motion (Kamiak)";
	}
	if ($eud_fields["event_4"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Century Showcase";
	}
	if ($eud_fields["event_5"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Pride of the Northwest (Grants Pass)";
	}
	if ($eud_fields["event_6"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Spectacle of Sound (Southridge)";
	}
	if ($eud_fields["event_7"] == "Yes") {
		$std_event += 1;
		$event_array[] = "Sunset Classic";
	}
	if ($eud_fields["event_8"] == "Yes") {
		$pri_event += 1;
		$event_array[] = "Championships";
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
		<td>Previous Payments<br>  <?php echo $eud_paymentdate?></td><td></td><td></td><td><?php echo "- $ ".$eud_paidamt?></td>
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