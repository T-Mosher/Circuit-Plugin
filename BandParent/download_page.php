<?php
/*
Template Name: Download Page
*/
?>
<?php
// if logged-in, automatically send a text download file of all the fields
// ...  else, display the fields in the page as HTML like normal
if (is_user_logged_in()) {
	// yes, a user is logged-in, so output the current record as a downloaded text file
	$field_array = array(
		"unit_name","unit_type","classification","size","director_name",
		"email","phone","city","state","nwapa_member",
		"photo","show_title","design_staff","instruction_staff","announcer_script",
		"spiel_doc","notes","transportation",
		"secondary_contact_name","secondary_contact_email","secondary_contact_phone","date_updated");
	$field_titles = array(
		"Unit Name:\r\n","Unit Type:\r\n","Classification:\r\n","Size:\r\n","Director:\r\n",
		"Director email:\r\n","Director phone:\r\n","City:\r\n","State:\r\n","NWAPA Member:\r\n",
		"Logo/Image:\r\nhttp://nwapa.net/wp-content/uploads/participants-database/","Show Title:\r\n","Design Staff:\r\n",
		"Instruction Staff:\r\n","Announcer Script:\r\n",
		"Spiel document:\r\nhttp://nwapa.net/wp-content/uploads/participants-database/","Notes:\r\n","Transportation:\r\n",
		"Secondary Contact:\r\n","Secondary email:\r\n","Secondary phone:\r\n","Last Update\r\n");
	
	
	if (!isset($participant_id)) {
	
	// if there is no id in the request, use the default record
		$participant_id = isset($_REQUEST['pdb']) ? $_REQUEST['pdb'] : false;
		//echo $participant_id;
	}


	// get all of the fields of this record into an indexable array
	$unit_fields = Participants_Db::get_participant($participant_id);

/*	
	// loop through all values
	$fld_cnt = count($field_array);
	//echo "field count:".$fld_cnt."<br>";
	for ($idx = 0; $idx < $fld_cnt; $idx++) {
		echo $field_titles[$idx]."<br>";
		$field = $field_array[$idx];
		if strlen($field) == 0 {
			$field = "-none-";
		}
			
		echo $unit_fields[$field]."<br><br>";
	}
*/	
    	//     maybe also add a button later
    	
    	// build the unit file name
    	$fname = "none.txt";
    	$fld_cnt = count($field_array);
    	if ($fld_cnt > 2) {
    		$tok1 = strtok($unit_fields["unit_name"], " .");
    		$tok2 = strtok(" .");
        	$fname = $tok1 . $tok2 . ".txt";
        }
        //echo $fname."<br>";
        
	// create a file pointer connected to the output stream
        $out_file = fopen('php://output', 'w');

        //header('Content-type: application/csv'); // some sources say it should be this
        header('Content-Type: text/plain; charset=utf-8');
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename='.$fname);

	fwrite($out_file, "NWAPA Unit Information\r\n");    // add a title
	
	// now loop over all the fields, outputting each label and field
        $fld_cnt = count($field_array);
	//echo "field count:".$fld_cnt."<br>";
	for ($idx = 0; $idx < $fld_cnt; $idx++) {
		fwrite($out_file, $field_titles[$idx]);
		$field = $field_array[$idx];
		$txt = $unit_fields[$field];
		if (strlen($txt) == 0) {
			$txt = "-none-";
		}
		fwrite($out_file, $txt."\r\n\r\n");
	}
        
        // output the data lines
        //foreach ($data as $line) {
        //	fputcsv($output, $line, ',', self::$CSV_enclosure);
        //}
        
	//for($i = 0; $i < $arr_len; $i++){
        //	fwrite($output, $unit_info[i]);
        //}
        fclose($out_file);

        // we must terminate the script to prevent additional output being added to the CSV file
        exit;  	

} else { ?>
<!-- not logged-in, so display the normal theme page template, using the page's shortcode template -->
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
			<?php get_sidebar('bottom'); ?>
              <div class="cleared"></div>
            </div>
        </div>
    </div>
</div>
<div class="cleared"></div>
<?php get_footer(); ?>
<?php
}
?>