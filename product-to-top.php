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


remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');
add_action( 'woocommerce_before_shop_loop','make_prodcut_to_the_top', -1);

//add_action( 'woocommerce_after_shop_loop','make_prodcut_to_the_top' );

//add_filter( 'woocommerce_before_shop_loop','make_prodcut_to_the_top' );

function make_prodcut_to_the_top()
{
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


