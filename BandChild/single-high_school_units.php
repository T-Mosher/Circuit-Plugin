<?php

/**

 * Sample template for displaying single web_projects posts.

 * Save this file as as single-web_projects.php in your current theme.

 *

 * This sample code was based off of the Starkers Baseline theme: http://starkerstheme.com/

 */



get_header(); ?>

<div class="art-content-layout">

    <div class="art-content-layout-row">

        <div class="art-layout-cell art-sidebar1">

          <?php get_sidebar('default'); ?>

          <div class="cleared"></div>

        </div>

        <div class="art-layout-cell art-content">

			<?php get_sidebar('top'); ?>

			

				<div class="schoolUnitListing">

			

				<table cellpadding="5px">



				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					

						<h1><?php the_title(); ?></h1>

					

						<?php the_content(); ?>

						

						<div class="schoolUnitTable">



							<tr>

								<td width="35%"><strong>Unit Type:</strong></td>  

								<td width="65%"><?php echo get_post_meta($post->ID, 'unit_type', true) ?></td>

							</tr>

							<tr>

								<td><strong>Classification:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_classification', true) ?></td>

							</tr>

							<tr>

								<td><strong>City, State:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_city', true) ?>,  <?php echo get_post_meta($post->ID, 'unit_state', true) ?></td>

							</tr>

							<?php if ( get_post_meta($post->ID, 'unit_website', true)) : ?>

								<tr>

									<td></td>

									<td><?php echo '<a href="' . get_post_meta($post->ID, 'unit_website', true) . '" target="_blank">' . 'website' . '</a>';?>								

									

								</tr>

							<?php endif;  ?>

							<tr>

								<td><strong>Sponsor:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_sponsor', true) ?></td>

							</tr>

							<tr>

								<td><strong>Total Members:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_total_members', true) ?></td>

							</tr>

							<tr>

								<td><strong>Unit Director:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_director_name', true) ?></td>

							</tr>

							<tr>

								<td><strong>Band Director:</strong></td> 

								<td><?php echo get_post_meta($post->ID, 'unit_band_director_name', true) ?></td>

							</tr>

							<tr>

								<td><strong>Instructional Staff:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_instructional_staff', true) ?></td>

							</tr>

							<tr>

								<td><strong>Transportation:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_transportation', true) ?></td>

							</tr>

							<tr>

								<td><strong>Design Staff:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_design_staff', true) ?></td>

							</tr>

							<tr>

								<td><strong>Show Title:</strong></td>

								<td><?php echo get_post_meta($post->ID, 'unit_show_title', true) ?></td>

							</tr>

							<tr>

								<td><strong>Primary Contact:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_primary_contact', true) ?></td>

							</tr>

							<tr>

								<td><strong>Primary Contact eMail:</strong></td>

								<td><?php echo '<a href="mailto:' . get_post_meta($post->ID, 'unit_primary_contact_email', true) . '" target="_blank">' . get_post_meta($post->ID, 'unit_primary_contact_email', true) . '</a>'; ?></td>

							</tr>

							<tr>

								<td><strong>Primary Contact Phone:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_primary_contact_phone', true) ?></td>

							</tr>

							<tr>

								<td><strong>Secondary Contact:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_secondary_contact', true) ?></td>

							</tr>

							<tr>

								<td><strong>Secondary Contact eMail:</strong></td>  

								<td><?php echo '<a href="mailto:' . get_post_meta($post->ID, 'unit_secondary_contact_email', true) . '" target="_blank">' . get_post_meta($post->ID, 'unit_secondary_contact_email', true) . '</a>'; ?></td>

							</tr>

							<tr>

								<td><strong>Secondary Contact Phone:</strong></td>  

								<td><?php echo get_post_meta($post->ID, 'unit_secondary_contact_phone', true) ?></td>

							</tr>

							

						</div>



				<?php endwhile; // end of the loop. ?>

				</table>

				

			</div>



			<?php get_sidebar('bottom'); ?>

          <div class="cleared"></div>

        </div>

    </div>

</div>

<div class="cleared"></div>



<?php get_footer(); ?>