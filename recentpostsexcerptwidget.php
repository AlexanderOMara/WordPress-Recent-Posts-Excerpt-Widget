<?php
/*
Plugin Name: WordPress Recent Posts Excerpt Widget
Description: Adds a recent posts widget with excerpts.
Author: Alexander O'Mara
Version: 1.8.0
*/

//Extend the widget class and register the widget.
class RecentPostsExcepts_Widget extends WP_Widget {
	function RecentPostsExcepts_Widget() {
		//Create the widget.
		$this->WP_Widget( 'recentpostexcerpt-widget', __( 'Recent Post Excerpt Widget', 'rpew' ), 
			//Widget settings.
			array( 'classname' => 'recentpostexcerptwidget', 'description' => __( 'Recent Post Excerpt Widget.', 'rpew' ) ), 
			array( 'id_base' => 'recentpostexcerpt-widget' )
		);
	}
	
	function widget( $args, $instance ) {
		//Get settings.
		$title       = isset( $instance['title'] )       ? $instance['title']       : __( 'Recent Posts', 'rpew' );
		$readmore    = isset( $instance['readmore'] )    ? $instance['readmore']    : __( 'Read More...', 'rpew' );
		$numberposts = isset( $instance['numberposts'] ) ? $instance['numberposts'] : 5;
		$numberwords = isset( $instance['numberwords'] ) ? $instance['numberwords'] : 100;
		//Get the date format.
		$date_format = get_option( 'date_format' );
		//Widget start wrapper.
		echo $args['before_widget'];		
		//Show title between widget title surrounds.
		echo $args['before_title'], esc_html( $title ), $args['after_title'];
		//The list of posts.
		?><ul><?php
		$recent_posts = (array)wp_get_recent_posts( array(
			'numberposts' => $numberposts,
			'post_status' => 'publish'
		) );
		foreach( $recent_posts as &$p ) {
			?><li><?php
				?><a href="<?php echo get_permalink( $p['ID'] ); ?>"><?php
					$author = get_userdata( $p['post_author'] );
					$postdate = apply_filters( 'get_the_date', mysql2date( $date_format, $p['post_date'] ), '', (int)$p['ID'] );
					$excerpt = trim(
						wp_trim_words(
							strip_tags(
								$p['post_excerpt'] ?
									$p['post_excerpt'] :
									str_replace( ']]>', ']]&gt;',
										apply_filters( 'the_content', strip_shortcodes( $p['post_content'] ) )
									)
							)
						, $numberwords )
					);
					?><div class="rpew-title"><?php
						?><big><?php
							echo esc_html( $p['post_title'] );
						?></big><?php
					?></div><?php
					?><div class="rpew-meta"><?php
						?><small><?php
							echo esc_html( $author->display_name ), _x( ', ', 'author-postdate-sep', 'rpew' ), esc_html( $postdate );
						?></small><?php
					?></div><?php
					?><p class="rpew-excerpt"><?php echo esc_html( $excerpt ); ?></p><?php
				?></a><?php
			?></li><?php
		}
		unset( $p );
		?></ul><?php
		//The read more link.
		?><a href="<?php
			echo (int)get_option( 'page_for_posts' ) !== 0 ? get_permalink( get_option( 'page_for_posts' ) ) : site_url();
		?>"><?php
			echo esc_html( $readmore );
		?></a><?php
		//Widget end wrapper.
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//Sanitize settings.
		$instance['title']       = (string)$new_instance['title'];
		$instance['readmore']    = (string)$new_instance['readmore'];
		$instance['numberposts'] = (int)$new_instance['numberposts'];
		$instance['numberwords'] = (int)$new_instance['numberwords'];
		//Return settings.
		return $instance;
	}
	
	function form( $instance ) {
		//PArse settings.
		$instance = wp_parse_args( (array)$instance, array(
			'title'       => __( 'Recent Posts', 'rpew' ),
			'readmore'    => __( 'Read More...', 'rpew' ),
			'numberposts' => 5,
			'numberwords' => 100
		) );
		//Loop over settings fields.
		$fields = array(
			array(
				'name'  => 'title',
				'label' => __( 'Title:', 'rpew' ),
				'value' => $instance['title'],
				'type'  => 'text'
			),
			array(
				'name'  => 'readmore',
				'label' => __( 'Read More:', 'rpew' ),
				'value' => $instance['readmore'],
				'type'  => 'text'
			),
			array(
				'name'  => 'numberposts',
				'label' => __( 'Max Posts:', 'rpew' ),
				'value' => $instance['numberposts'],
				'type'  => 'number'
			),
			array(
				'name'  => 'numberwords',
				'label' => __( 'Max words:', 'rpew' ),
				'value' => $instance['numberwords'],
				'type'  => 'number'
			)
		);
		?><p><?php
			foreach ( $fields as &$field ) {
				?><label<?php
					?> for="<?php echo $this->get_field_id( $field['name'] ); ?>"<?php
				?>><?php
					echo $field['label'];
				?></label><?php
				?><input class="widefat"<?php
					?> id="<?php echo $this->get_field_id( $field['name'] ); ?>"<?php
					?> name="<?php echo $this->get_field_name( $field['name'] ); ?>"<?php
					?> value="<?php echo esc_attr( $field['value'] ); ?>"<?php
					?> type="<?php echo esc_html( $field['type'] ); ?>"<?php
				?> /><?php
			}
			unset( $field );
		?></p><?php
	}
}
function recentpostsexcepts_widgets_init() {
	register_widget( 'RecentPostsExcepts_Widget' );
}
add_action( 'widgets_init', 'recentpostsexcepts_widgets_init' );
