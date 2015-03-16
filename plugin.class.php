<?php 
/**
* Main Class for Plugin WCP Query
*/
class WCP_Query
{
	
	function __construct()
	{
		add_shortcode( 'wp-query', array($this, 'render_wcp_query_shortcode') );
		add_action( 'wp_enqueue_scripts', array($this, 'wcp_query_externals') );
		add_action( 'admin_enqueue_scripts', array($this, 'query_generator_scripts') );
		add_action( 'admin_menu', array($this, 'wcp_query_generator_page') );
	}

	public function render_wcp_query_shortcode($atts){


		$grid_styles = 'query-postsall';
		$tags = true;
		$showdate = true;
		$showauthor = true;
		$col = 1;
		$titletag = 'h3';
		$hr = true;
		$postname = true;
		$showthumbnail = true;
		$showpagi = true;
		$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;


		// Extracting From User
		if($atts != ''){
			extract($atts);
		}

		$tags = ($tags == 'true' ? true : false);
		$showauthor = ($showauthor == 'true' ? true : false);
		$showdate = ($showdate == 'true' ? true : false);
		$showthumbnail = ($showthumbnail == 'true' ? true : false);
		$showpagi = ($showpagi == 'true' ? true : false);
		$hr = ($hr == 'true' ? true : false);

		// Conditions Based on User
		if ($col == 1 || $col == 2 ) {
			$grid_styles = 'query-posts12';
		}
		
		/**
		 * The WordPress Query class.
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query
		 *
		 */
		$args = array(
			
			//Post & Page Parameters
			'p'             => $p,
			'name'          => $name,
			'page_id'       => $page_id,
			'pagename'     => $pagename,
			'post_parent'  => $post_parent,
			'post__in'     => $post__in,
			'post__not_in' => $post__not_in,
			
			//Author Parameters
			'author'      => $author,
			'author_name' => $author_name,
			
			//Category Parameters
			'cat'              => $cat,
			'category_name'    => $category_name,
			
			//Type & Status Parameters
			'post_type'   => $post_type,
			'post_status' => $post_status,
			
			//Order & Orderby Parameters
			'order'               => $order,
			'orderby'             => $orderby,
			
			//Tag Parameters
			'tag'           => $tag,
			'tag_id'        => $tag_id,
			
			//Pagination Parameters
			'pagination'             => true,
			'posts_per_page'         => $posts_per_page,
			'paged'                  => $paged,
			
		);

		$grid_counter = 0; 
		
		$the_query = new WP_Query( $args ); ?>

		<?php if ( $the_query->have_posts() ) : ?>

			<div class="grid">

			<!-- the loop -->
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); $grid_counter++ ?>
				
				<div class="col-1-<?php echo $col; ?>">
					<div <?php post_class( 'content' ); ?>>
						<?php 
						if ($showthumbnail === true) {
							
							if( has_post_thumbnail() ){
				 				the_post_thumbnail(array('150'), array('class'=> $grid_styles));
				 	 		}
				 			else { ?>
				 				<img class="<?php echo $grid_styles; ?>"  src="<?php echo plugin_dir_url( __FILE__ ) ?>images/thumb.png" />
				 			<?php
				 			}
						}
						?>
						<?php if($postname === true) { ?><a href="<?php the_permalink(); ?>"><<?php echo $titletag; ?>><?php the_title(); ?></<?php echo $titletag; ?>></a><?php } ?>
						<?php if ($col == 1) {
							the_excerpt();
						} ?>
						<?php if($hr === true){ echo '<hr>'; } ?>
						<div>
							<?php if ($showdate === true) { ?> <span>Posted on: </span> <?php the_date(); } ?><span><?php if($showauthor === true) { ?> By: </span>
							<?php the_author(); } ?> <br> <?php if ($tags === true) { the_tags(); } ?>
						</div>
					</div>
				</div>
				<?php if ($grid_counter == $col) {
					echo '</div><div class="grid">';
					$grid_counter = 0;
				} ?>
			<?php endwhile; ?>
			<!-- end of the loop -->
			<?php if ($showpagi === true) { ?>
				<div class="nav-previous alignleft"><?php next_posts_link( 'Older posts' ); ?></div>
				<div class="nav-next alignright"><?php previous_posts_link( 'Newer posts' ); ?></div>
			<?php } ?>

			<?php wp_reset_postdata(); ?>

		<?php else : ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; 	
	}

	public function wcp_query_externals(){
		wp_enqueue_style( 'simple-grid-css', plugin_dir_url( __FILE__ ) .'css/simplegrid.css' );
	}


	// Options Page
	function wcp_query_generator_page(){
		add_menu_page( 'WP Query Shortcodes Generator', 'WP Query', 'manage_options', 'wcp_query', array($this, 'rander_wcp_query_generator_page'), 'dashicons-format-aside' );
	}

	function rander_wcp_query_generator_page(){
		echo "<h2>WP Query Shortcodes Generator</h2>";
		?>
			<style>
				select {
					display: block;
					margin-bottom: 10px;
					width: 300px;
				}
				input[type=number] {
					width: 300px;
				}
			</style>
			<div ng-app="wcpApp">
				<div ng-controller="queryGenerator">
					<label>Select either single or multiple</label>
					<select ng-model="queryshortcode.qty">
						<option value="1">Single</option>
						<option value="2">Multiple</option>
					</select>

					<!-- In case single -->
					<label ng-if="queryshortcode.qty == 1">Select single post type
						<select ng-model="queryshortcode.singleType">
							<option value="page">Page</option>
							<option value="post">Post</option>
							<option value="product">Product</option>
						</select>
					</label>

					<label ng-if="queryshortcode.singleType == 'page' && queryshortcode.qty == 1">Select page
	                    <select ng-model="queryshortcode.pageid">
	                         <?php
	                         global $post;
	                         $args = array( 'numberposts' => -1, 'post_type' => array( 'page' ) );
	                         $posts = get_posts($args);
	                         foreach( $posts as $post ) : setup_postdata($post); ?>
	                            <option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
	                         <?php endforeach; ?>
	                    </select>  					
                    </label>

					<label ng-if="queryshortcode.singleType == 'post' && queryshortcode.qty == 1">Select post
	                    <select ng-model="queryshortcode.postid">
	                         <?php
	                         global $post;
	                         $args = array( 'numberposts' => -1, 'post_type' => array( 'post' ) );
	                         $posts = get_posts($args);
	                         foreach( $posts as $post ) : setup_postdata($post); ?>
	                            <option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
	                         <?php endforeach; ?>
	                    </select> 
                    </label>

					<label ng-if="queryshortcode.singleType == 'product' && queryshortcode.qty == 1">Select product
	                    <select ng-model="queryshortcode.productid">
	                         <?php
	                         global $post;
	                         $args = array( 'numberposts' => -1, 'post_type' => array( 'product' ) );
	                         $posts = get_posts($args);
	                         foreach( $posts as $post ) : setup_postdata($post); ?>
	                            <option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
	                         <?php endforeach; ?>
	                    </select>
                    </label>
					<label ng-if="queryshortcode.qty == 2">Select taxonomy
						<select ng-model="queryshortcode.multipleType">
							<option value="category">Category</option>
							<option value="tag">Tag</option>
						</select>
					</label>
					
					<label ng-if="queryshortcode.multipleType == 'category' && queryshortcode.qty == 2">Select Category
						<select ng-model="queryshortcode.categoryid">
							<?php
								$categories = get_categories( 'exclude=1' ); 

								foreach ($categories as $cat) {
									?>
									<option value="<?php echo $cat->term_id ?>"><?php echo $cat->name ?></option>
									<?php
								}
							?>
						</select>
					</label>
					
					<label ng-if="queryshortcode.multipleType == 'tag' && queryshortcode.qty == 2">Select tag
						<select ng-model="queryshortcode.tagid">
							<?php
								$tags = get_tags( 'exclude=1' ); 
								foreach ($tags as $tag) {
									?>
									<option value="<?php echo $tag->term_id ?>"><?php echo $tag->name ?></option>
									<?php
								}
							?>
						</select>
					</label>

					<label ng-if="queryshortcode.qty == 2">How Many Posts: <br> <input type="number" ng-model="queryshortcode.numberofposts"></label>
					<br>
					<label ng-if="queryshortcode.qty == 2">Grid Columns?
					<select ng-model="queryshortcode.cols">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
					</select>
					</label>
					<div id="otheroptions" style="margin: 20px; text-align:center;">
						<label><input type="checkbox" ng-model="queryshortcodeOptions.tags">	Show Tags</label> &nbsp;
						<label><input type="checkbox" ng-model="queryshortcodeOptions.author">	Show Author</label> &nbsp;
						<label><input type="checkbox" ng-model="queryshortcodeOptions.date">	Show Date</label> &nbsp;
						<label><input type="checkbox" ng-model="queryshortcodeOptions.image">	Show Thumbnail</label> &nbsp;
						<label><input type="checkbox" ng-model="queryshortcodeOptions.pagination">	Show Pagination</label> &nbsp;
						<label><input type="checkbox" ng-model="queryshortcodeOptions.titlehr">	Show Line after title</label> &nbsp;
					</div>
					<hr>
						<button class="button-primary" ng-click="createShortcode()">Generate Shortcode</button>
					<div style="text-align:center;" ng-show="showShortcode">
						<br>
						<br>
						<input type="text" ng-model="finalShortcode" disabled style="padding: 10px; width: 90%;text-align:center;color:#000">
						<br>
						<br>
						<p class="description">Please copy and paste above shortcode in your desired area.</p>					
					</div>
				</div>
			</div>
		<?php
	}

	public function query_generator_scripts($hook){
		if ($hook != 'toplevel_page_wcp_query') {
			return;
		}
		wp_enqueue_script( 'angular-js', plugin_dir_url( __FILE__ ) .'js/angular.min.js');
		wp_enqueue_script( 'custom-angular-js', plugin_dir_url( __FILE__ ) .'js/script.js', 'angular-js');
	}
}

?>