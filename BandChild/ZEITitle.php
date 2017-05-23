<?php

	global $post;

	theme_post_wrapper(

		array(

				'id' => theme_get_post_id(), 

				'class' => theme_get_post_class(),

				'thumbnail' => theme_get_post_thumbnail(),

				'title' => '<a href="' . get_permalink( $post->ID ) . '" rel="bookmark" title="' . get_the_title() . '">' . get_the_title() . '</a>', 

		)

	);

?>

