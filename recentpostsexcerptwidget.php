<?php
/*
Plugin Name: WordPress Recent Posts Excerpt Widget
Description: Adds a recent posts widget with excerpts.
Author: Alexander O'Mara
Version: 1.5
*/

//Extend the widget class and register the widget.
class RecentPostsExcepts_Widget extends WP_Widget {
	function RecentPostsExcepts_Widget() {
		//Create the widget.
		$this->WP_Widget( 'recentpostexcerpt-widget', 'Recent Post Excerpt Widget', 
			//Widget settings.
			array( 'classname' => 'recentpostexcerptwidget', 'description' => 'Recent Post Excerpt Widget.' ), 
			//Widget control settings.
			array( 'id_base' => 'recentpostexcerpt-widget' )
		);
	}
	
	//Widget display.
	function widget( $args, $instance ) {
		
		extract( $args );
		$title = isset( $instance['title'] ) ? $instance['title'] : 'Recent Posts';
		$numberposts = isset( $instance['numberposts'] ) ? $instance['numberposts'] : 5;
		$numberwords = isset( $instance['numberwords'] ) ? $instance['numberwords'] : 100;
		
		//Widget start wrapper.
		echo $before_widget;		
		//Show title between widget title surrounds.
		echo $before_title . $title . $after_title;
		
		?><ul><?php
		
		$recent_posts = wp_get_recent_posts( array(
			'numberposts' => $numberposts,
			'post_status' => 'publish'
		) );
		
		foreach( $recent_posts as &$p ) {
			?><li><a href="<?php echo get_permalink( $p['ID'] ); ?>"><?php
				$author = get_userdata( $p['post_author'] );
				$postdate = explode( ' ', $p['post_date'] );
				$excerpt = explode( ' ', trim( $p['post_excerpt'] !== '' ? $p['post_excerpt'] : strip_tags( array_pop( array_reverse( explode( '<!--more-->', $p['post_content'] ) ) ) ) ) );
				?><div><?php
					?><h4><?php echo $p['post_title']; ?></h4><?php
					?><h6><?php echo $author->display_name . ', ' . str_replace( '-', '/', $postdate[0] ); ?></h6><?php
				?></div><?php
				?><p><?php echo implode( ' ', array_slice( $excerpt, 0, $numberwords ) ) . ( count( $excerpt ) > $numberwords ? '. . .' : '' ); ?></p><?php
				?><p>&nbsp;</p><?php
			?></a></li><?php
		}
		unset( $p );
		?></ul><?php
		
		?><a href="<?php echo get_option( 'page_for_posts' ) != '0' ? get_permalink( get_option( 'page_for_posts' ) ) : site_url(); ?>">view all. . .</a><?php
		
		//Widget end wrapper.
		echo $after_widget;
	}
	
	//Save widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		//Save input.
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['numberposts'] = (int)$new_instance['numberposts'];
		$instance['numberwords'] = (int)$new_instance['numberwords'];
		
		return $instance;
	}
	
	//Widget settings.
	function form( $instance ) {
		//Default widget settings.
		$defaults = array(
			'title' => 'Recent Posts',
			'numberposts' => 5,
			'numberwords' => 100
		);
		$instance = wp_parse_args( (array)$instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Title:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text" />
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>"><?php echo 'Number of posts to show:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'numberposts' ); ?>" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" value="<?php echo $instance['numberposts']; ?>" class="widefat" type="text" />
			<label for="<?php echo $this->get_field_id( 'numberwords' ); ?>"><?php echo 'Number of words to show:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'numberwords' ); ?>" name="<?php echo $this->get_field_name( 'numberwords' ); ?>" value="<?php echo $instance['numberwords']; ?>" class="widefat" type="text" />
		</p>
		<?php
	}
}
function recentpostsexcepts_widgets_init() {
	register_widget( 'RecentPostsExcepts_Widget' );
}
add_action( 'widgets_init', 'recentpostsexcepts_widgets_init' );


?>