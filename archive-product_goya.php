<?php   // do not copy this "<?php"



/**
 * Plugin Name:       Product To Top,  this file used to goya theme
 * Plugin URI:        https://ensky.tech
 * Description:       make the specific product to the top according to the URL parameters
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            ensky
 * Author URI:        https://ensky.tech
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * How  to use ?
 * 1. find the "woocommerce_product_loop_start();" code from archive-product.php file, comment it until "woocommerce_product_loop_end()"
 * 2. copy all the code of this file
 * 3. paste to archive-product.php file
 * @date 2021.7.8
 * @auther ensky
 * @web
 *
 */

    /*
    woocommerce_product_loop_start();
    if ( wc_get_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();

            do_action( 'woocommerce_shop_loop' );

            wc_get_template_part( 'content', 'product' );
        }
    }
    woocommerce_product_loop_end();
    */

    function original_product_display()
    {
        woocommerce_product_loop_start();
        
        if ( wc_get_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();
        
        
            do_action( 'woocommerce_shop_loop' );
        
            wc_get_template_part( 'content', 'product' );
            }
        }
        
        woocommerce_product_loop_end();

    }
    
    
    function make_prodcut_to_the_top($inputSku, $inputProductId)
    {
        
        woocommerce_product_loop_start();
 
        //get sku from URL
        //$inputSku=$_GET["sku"];
        //$inputProductId=$_GET["id"];
        
        if($inputProductId!=null)
        {
            $exclude_ids_array=explode(',',$inputProductId);
 
               // echo "exclude exclude_ids_array start";
              //  var_dump($exclude_ids_array);
               //  echo "exclude exclude_ids_array end";
        }
        else
        {
                //spilit sku to array
                $exclude_sku_array=explode(',',$inputSku);
                
                //echo "exclude sku array start";
                //var_dump($exclude_sku_array);
                // echo "exclude sku array end";
             
                $exclude_ids_array=array();
                $i=0;
                foreach ($exclude_sku_array as $skuId)
                {
                    //using loop to find the product id
                    //because function wc_get_product_id_by_sku() is not working, so we use loop to get the product id
                    
                    //loop to get product id start
                    $args01 = array(
                        'post_type' => 'product',
                        );
                        
                    $loop = new WP_Query( $args01 );
                     
                    if ( $loop->have_posts() ) {
                        while ( $loop->have_posts() ) : $loop->the_post();
                        
                        //echo "loop001";
                        
                        global $product;
                        
                        if($product!=null)
                        {
                            $loopProductSku=$product->get_sku();
                            $loopProductId=$product->get_id();
                        }
                        else
                        {
                            //this code is not working @2021.7.7
                            $loopProductId=wc_get_product_id_by_sku($skuId);
                            //echo 'prodcut is null';
                            //echo "loopProductId=".$loopProductId;
                        }
                        
                        if($loopProductSku==$skuId)
                        {
                            $exclude_ids_array[$i++]=$loopProductId;
                        }
                        endwhile;
                    }  
                    //loop to get product id finish 
                }
        }
        
        wp_reset_postdata();
    
        //first display the top product
        $args01 = array(
            'post_type' => 'product',
            'post__in'=>$exclude_ids_array,
            );
        $loop = new WP_Query( $args01 );
         
        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) : $loop->the_post();
                wc_get_template_part( 'content', 'product' );
            endwhile;
        } else {
           //echo __( 'No products found, debug001' );
        }
        wp_reset_postdata();
     
        //2nd display the remaining product
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        
        //for args, pls read:https://developer.wordpress.org/reference/classes/wp_query/
        $args = array(
            'post_type' => 'product',
            'post__not_in'=>$exclude_ids_array,   // use post ids. Specify post NOT to retrieve.
             // 'posts_per_page' => 55     //parameter "-1" means fetch all the post
             // 'posts_per_archive_page'=>'16',  //(int) – number of posts to show per page – on archive pages only
             // 'paged' => 10,
             // 'orderby' => $orderBy,
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
             //echo __( 'No products found, debug002' );
        }
     
        wp_reset_postdata();
      
        woocommerce_product_loop_end();
        
    }  
		
    
    $orderBy=$_GET["orderby"];
    $inputSku=$_GET["sku"];
    $inputProductId=$_GET["id"];

    if(($orderBy!=null)||($inputProductId==null))
    {
        //if the original order is coming, we use default code
        original_product_display();
    }
    else
    {
        make_prodcut_to_the_top($inputSku, $inputProductId);
    }
			  
 				
					


					
					