<?php
/*
Template Name: BP Custom Page
*/
?>
<!-- Here is a copy of the BandParent theme page.php file -->
<?php get_header(); ?>
<div class="art-layout-wrapper">
    <div class="art-content-layout">
        <div class="art-content-layout-row">
            <div class="art-layout-cell art-sidebar1">
              <?php get_sidebar('default'); ?>
              <div class="cleared"></div>
            </div>
            <div class="art-layout-cell art-content">
			<?php get_sidebar('top'); ?>
			<?php 
				if(have_posts()) {
					
					/* Start the Loop */ 
					while (have_posts()) {
						the_post();
						get_template_part('content', 'page');
						/* Display comments */
						if ( theme_get_option('theme_allow_comments')) {
							comments_template();
						}
					}

				} else {
				
					 theme_404_content();
					 
				} 
		    ?>
		<?php eud_handler()?>    
		<?php get_sidebar('bottom'); ?>
              <div class="cleared"></div>
            </div>
        </div>
    </div>
</div>
<div class="cleared"></div>
<?php get_footer(); ?>

<?php
function eud_handler() {
$std_events = 0;
$pri_events = 0;
define ("STD_COST", 250.00);
define ("CHAMP_COST", 300.00);
define ("MEM_FEE", 200.00);


	if (!isset($participant_id)) {
	
	// if there is no id in the request, use the default record
		$participant_id = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : false;
	}

	$eud_id = Participants_Db::get_participant_id($participant_id);
	// echo "id:".$eud_id."<br>";

	echo "<a title=\"Fee Invoice\" href=\"http://nwapa.net/fee-invoice/?pid=".$participant_id."\">Click here for payment options.</a>";
	echo "<br><br>";

/* commented-out
	// get all of the fields of this record into an indexable array
	$eud_fields = Participants_Db::get_participant($eud_id);
	
	// var_dump($eud_fields);
	
	// Compute how many of each type of event is entered
	if ($eud_fields["event_1"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_2"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_3"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_4"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_5"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_6"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_7"] == "Yes") {
		$std_event += 1;
	}
	if ($eud_fields["event_8"] == "Yes") {
		$pri_event += 1;
	}
	
	// echo "<br>Totals: ".$std_event." ".$pri_event,"<br>";
	
	// compute credit (mostly from returned surety bond
	if (isset($eud_fields["balance"])) {
		echo "found a balance<br>";
		$eud_credit = $eud_fields["balance"];
		} else {
		echo "no balance<br>";
		$eud_credit = 0;
	}
	// compute if membership fee is due
	if (($std_event + $pri_event) > 1) {
		echo "Membership fee is due.<br>";
		$member_str = "Rqd";
		$member_fee = MEM_FEE;
	} else {
		$member_fee = 0;
		$member_str = "n/a";
	}
	
	// calculate the values for the invoice table
	$eud_reg_cost = $std_event * STD_COST;
	$eud_pri_cost = $pri_event * CHAMP_COST;
	$eud_subtotal = $eud_reg_cost + $eud_pri_cost + $member_fee;
	$eud_due = $eud_subtotal - $eud_credit;
	*/
	// here we format the output for the screen
	?>
	<!-- switch to HTML with php inserts-->
	<html>
	
	<!-- commented-out
		<h2>NWAPA Fall 2014 Invoice</h2>
	<h3>Unit: <?php echo $eud_fields["unit_name"];?></h3>
	<table border="1">
	<tbody>
	<tr>
		<td>Item</td><td>Qty.</td><td>Each</td><td>Amount</td>
	</tr>
	<tr>
		<td>Reg. Events</td><td><?php echo $std_event?></td><td><?php echo "$ ".STD_COST?></td><td><?php echo "$ ".$eud_reg_cost?></td>
	</tr>
	<tr>
		<td>Champs. Events</td><td><?php echo $pri_event?></td><td><?php echo "$ ".CHAMP_COST?></td><td><?php echo "$ ".$eud_pri_cost?></td>
	</tr>
	<tr>
		<td>NWAPA Membership</td><td><?php echo $member_str?></td><td><?php echo "$ ".MEM_FEE?></td><td><?php echo "$ ".$member_fee?></td>
	</tr>
	<tr>
		<td>Subtotal</td><td></td><td></td><td><?php echo "$ ".$eud_subtotal?></td>
	</tr>
	<tr>
		<td>Credit</td><td></td><td></td><td><?php echo "- $ ".$eud_credit?></td>
	</tr>
	<tr>
		<td>Balance Due</td><td></td><td></td><td><?php echo " $ ".$eud_due?></td>
	</tr>
	</tbody>
	</table>
	<br><h3>Send invoice and payment to:</h3><br>
	NWAPA Treasurer<br>
	538 McClure Ave<br>
	Astoria, Or 97103<br><br>
	-->
	</html>
<?php
} ?>
