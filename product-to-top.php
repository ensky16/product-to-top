<?php
/**
 * Plugin Name:       Product To Top
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       make the specific product to the top according to the URL parameters
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            ensky
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */
 
function removeAllAction()
{

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
 
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
 
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10-1 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20-1 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30-1 );
 
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 ); 
 
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
 
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
 
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
 
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
 
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
 
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

}

/*
function removeLoopStart()
{
    woocommerce_product_loop_start([bool $echo = false ]) : string
    
    woocommerce_product_loop_end([bool $echo = false ]) : string
}
*/


//add_action( 'init', 'removeLoopStart');

//wc_reset_loop();


/*add_filter('woocommerce_product_is_visible', function($is_visible, $id) {
                $is_visible = false;
                return $is_visible;
            }, 10,2);
            */

add_filter( 'woocommerce_show_page_title', '__return_false' );
remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

//add_action( 'woocommerce_after_shop_loop','make_prodcut_to_the_top' );

//add_filter( 'woocommerce_before_shop_loop','make_prodcut_to_the_top' );

//test start
//add_action( 'pre_get_posts', 'bbloomer_remove_products_from_shop_page' );

function bbloomer_remove_products_from_shop_page( $q ) {

    if ( ! $q->is_main_query() ) return;
    if ( ! $q->is_post_type_archive() ) return;

    if ( ! is_admin() && is_shop() ) {


        $q->set( 'tax_query', array(array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array( 'null' ),
            'operator' => 'IN'
        )));
        

    }

    remove_action( 'pre_get_posts', 'bbloomer_remove_products_from_shop_page' );

}

//****222
/**
 * Exclude products from a particular category on the shop page
 */
function custom_pre_get_posts_query( $q ) {

    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'clothing' ), // Don't display products in the clothing category on the shop page.
           'operator' => 'IN'
    );


    $q->set( 'tax_query', $tax_query );

}
//add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );  



//add_action( 'woocommerce_product_query','themelocation_product_query' );


//*****3333

/*
   function wc_no_products_found() 
  {
      if ( is_shop() ) {echo '<style>p.woocommerce-info{display:none}</style';}
    }
    */

//test end

//test 1
 // Reset query 
//wp_reset_query();

/*
add_action( 'woocommerce_product_query', 'hide_specific_products_from_shop', 20, 2 );
function hide_specific_products_from_shop( $q, $query ) {
    if( is_admin() && WC()->cart->is_empty() )
        return;

    // HERE Set the product IDs in the array
    $targeted_ids = array( 1122334455, 6107, 14202, 14203 );

    $products_in_cart = array();

    // Loop through cart items
    foreach( WC()->cart->get_cart() as $cart_item ){
        if( in_array( $cart_item['product_id'], $targeted_ids ) ){
            // When any defined product is found we add it to an array
            $products_in_cart[] = $cart_item['product_id'];
        }
    }
    // We remove the matched products from woocommerce lopp
    if( count( $products_in_cart ) > 0){
        $q->set( 'post__in', $products_in_cart );
    }
}
*/
//test 2


function file_replace() {

    $plugin_dir = plugin_dir_path( __FILE__ ) . 'library/front-page.php';
    $theme_dir = get_stylesheet_directory() . '/front-page.php';

    if (!copy($plugin_dir, $theme_dir)) {
        echo "failed to copy $plugin_dir to $theme_dir...\n";
    }
}

add_action( 'wp_head', 'file_replace' );
 
//add_action( 'woocommerce_before_shop_loop','make_prodcut_to_the_top', 1);
add_action( 'woocommerce_after_shop_loop','make_prodcut_to_the_top' );

function make_prodcut_to_the_top()
{


 echo get_template_directory_uri();   
 
 //wp_reset_query();
 //	wp_reset_postdata();
	woocommerce_product_loop_start();

	
	//get sku from URL
	$inputSku=$_GET["sku"];
	//spilit sku to array
	$exclude_sku_array=explode(',',$inputSku);
 
	$exclude_ids_array=array();
	$i=0;
	foreach ($exclude_sku_array as $skuId)
	{
	    $product_id = wc_get_product_id_by_sku( $skuId );
	    $exclude_ids_array[$i++]=$product_id;
	}
	
	//first display product
   
	$args01 = array(
		'post_type' => 'product',
	    //'post__in'=>array($product_id),
	    'post__in'=>$exclude_ids_array,
	//	'posts_per_page' => 55
		);
	$loop = new WP_Query( $args01 );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			wc_get_template_part( 'content', 'product' );
		endwhile;
	} else {
	//	echo __( 'No products found' );
	}
 
// add_action( 'pre_get_posts', '$loop' );
	
	//2nd display product
	 $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type' => 'product',
	   // 'post__not_in'=>array($product_id),
	    'post__not_in'=>$exclude_ids_array,
	//	'posts_per_page' => 55
	   // 'posts_per_page' => '-1',  //parameter "-1" means fetch all the post
	    // 'posts_per_archive_page'=>'16',
	    // 'paged' => 10,
	     'paged' => $paged,
		);
	$loop = new WP_Query( $args );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) : $loop->the_post();
			wc_get_template_part( 'content', 'product' );
		endwhile;
	} 
	else 
	{
		//	echo __( 'No products found' );
	}
  
	wp_reset_postdata();
 
	/*
	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			
			 // Hook: woocommerce_shop_loop.
			
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}
*/

	woocommerce_product_loop_end();
	
 
}


