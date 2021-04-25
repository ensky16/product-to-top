<?php
/**
 * Plugin Name:       Product To Top
 * Plugin URI:        https://ensky.tech
 * Description:       make the specific product to the top according to the URL parameters
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            ensky
 * Author URI:        https://ensky.tech
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */
 


add_action( 'woocommerce_after_shop_loop','self_main_process', 10);

function self_main_process()
{
    $inputSku=$_GET["sku"];
    $orderBy=$_GET["orderby"];
    
    prepare_woocommerce_template();
    
    if($orderBy!=null)
    {
        original_product_display();
    }
    else
    {
        make_prodcut_to_the_top();
    }
    
}

function comment_archive_product_loop_code()
{
    $woo_archive_product_file='archive-product.php';
    $path=$woo_archive_product_file;
    $theme_path = get_stylesheet_directory();
    $full_path = $theme_path . '/woocommerce/' . $path;

    //- comment
    $search='woocommerce_product_loop_start';
    $replace='//woocommerce_product_loop_start';
    file_content_replace($full_path, $search, $replace);
    
    //- comment
    $search='have_posts()';
    $replace='FALSE';
    file_content_replace($full_path, $search, $replace);
    
    //- comment
    $search='the_post()';
    $replace='//the_post()';
    file_content_replace($full_path, $search, $replace);
    
    //- comment
    $search='woocommerce_shop_loop';
    $replace=' ';
    file_content_replace($full_path, $search, $replace);

     //- comment
    $search='wc_get_template_part';
    $replace='//wc_get_template_part';
    file_content_replace($full_path, $search, $replace);
    
    //- comment
    $search='woocommerce_product_loop_end';
    $replace='//woocommerce_product_loop_end';
    file_content_replace($full_path,$search,$replace);
     
}

function check_if_comment_is_already_done()
{
    $compare_final_result=FALSE;
    $woo_archive_product_file='archive-product.php';
    $path=$woo_archive_product_file;
    $theme_path = get_stylesheet_directory();
    $full_path = $theme_path . '/woocommerce/' . $path;
    
    $search='//woocommerce_product_loop_start';
    $replace='//woocommerce_product_loop_start';
    $comppare_result01=is_string_exist($full_path, $search);
    
    $search='//woocommerce_product_loop_end';
    $replace='//woocommerce_product_loop_end';
    $comppare_result02=is_string_exist($full_path, $search);
    
    $search='//wc_get_template_part';
    $replace='//wc_get_template_part';
    $comppare_result03=is_string_exist($full_path, $search);
   
   
    if($comppare_result01==$comppare_result02)
    {
        if($comppare_result02==TRUE)
        {
            if($comppare_result03==TRUE)
            {
                  $compare_final_result=TRUE;
            }
        }
    }
    
    //debug
    /*
    echo 'result 01: '.$comppare_result01;
     echo 'result 02: '.$comppare_result02;
      echo 'result 03: '.$comppare_result03;
     echo 'compare_final_result: : '.$compare_final_result;
     */
    //debug end
     
    return $compare_final_result;
}

function prepare_woocommerce_template()
{
    //- check if the theme already have this file ? if already have, backup that file
    $theme_path = get_stylesheet_directory();
    $woo_archive_product_file='archive-product.php';
    $path=$woo_archive_product_file;
    $full_path = $theme_path.'/woocommerce/'.$path;
   if(dir_exist_file($full_path)==FALSE)
    {
        //- copy template to theme
        save_template($woo_archive_product_file);
    }
    
    //- check if the needed comment already has been done
    if(!check_if_comment_is_already_done())
    {
        //- if not done, comment the code
        comment_archive_product_loop_code();
    }
}

function original_product_display()
{
    woocommerce_product_loop_start();
    if ( wc_get_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();
             // Hook: woocommerce_shop_loop.
            do_action( 'woocommerce_shop_loop' );
            wc_get_template_part( 'content', 'product' );
        }
    }
    woocommerce_product_loop_end();
}

function make_prodcut_to_the_top()
{
    
    
    woocommerce_product_loop_start();
    
    //get sku from URL
    $inputSku=$_GET["sku"];
    $orderBy=$_GET["orderby"];
    
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
    //    'posts_per_page' => 55
        );
    $loop = new WP_Query( $args01 );
     
    if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) : $loop->the_post();
            wc_get_template_part( 'content', 'product' );
        endwhile;
    } else {
    //    echo __( 'No products found' );
    }
 
    //2nd display product
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
    $args = array(
        'post_type' => 'product',
       // 'post__not_in'=>array($product_id),
        'post__not_in'=>$exclude_ids_array,
    //    'posts_per_page' => 55
       // 'posts_per_page' => '-1',  //parameter "-1" means fetch all the post
        // 'posts_per_archive_page'=>'16',
        // 'paged' => 10,
      //  'orderby' => $orderBy,
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
        //    echo __( 'No products found' );
    }
 
    wp_reset_postdata();
  
    woocommerce_product_loop_end();
}

function is_string_exist($filename, $search)
{
    $string = file_get_contents($filename);
    
    $num01=strpos($string, $search);
    
    if($num01==FALSE)
        return FALSE;
     
    //debug
   // echo 'is string exist03= '.$num01;
    
    if($num01>0)
    {
            //debug
    //echo 'is string exist01\n';
        return TRUE;
    }
    else
    {
            //debug
    //echo 'is string exist02\n';
        return FALSE;
    }

}

function file_content_compare($filename, $search, $compare){
    $string = file_get_contents($filename);
    
    $num01=strpos($string, $search);
    $num02=strpos($string, $compare);
    
    if($num01 !=$num02)
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function file_content_replace($filename, $search, $replace){
    $string = file_get_contents($filename);
    $new_string = str_replace($search, $replace, $string);
    if($string !=$new_string) file_put_contents($filename, $new_string);
}


function get_full_path( $path, $in_theme )
{
    if ( $in_theme ) {
        $template_dir = get_stylesheet_directory() . '/woocommerce';
    } else {
        $template_dir = WC()->plugin_path() . '/templates';
    }
    $full_path = $template_dir . '/' . $path;
    if ( ! $in_theme && ! file_exists( $full_path ) && function_exists( 'WC_PB' ) ) {
        $full_path = WC_PB()->plugin_path() . '/templates/' . $path;
    }
    return apply_filters( 'woo-edit-template-full_template_path', $full_path, $in_theme );
}


/**
 * Display form for input FTP\SHH credentials and error message
 *
 * @param string $message
 */
function filesystem_error( $message ) {
    $error = new WP_Error("filesystem_error", $message );
    echo $error->get_error_message();
    exit;
}


function connect_fs( $url, $method, $context, $fields = null ) {
    global $wp_filesystem;

    $method = 'direct';

    if ( false === ($credentials = request_filesystem_credentials( $url, $method, false, $context, $fields )) ) {
        filesystem_error( __( "Cannot initialize filesystem" ) );
    }

    //check if credentials are correct or not.
    if ( !WP_Filesystem( $credentials ) ) {
        request_filesystem_credentials( $url, $method, true, $context, $fields );
        filesystem_error( __( "Cannot initialize filesystem" ) );
    }

    return true;
}


/**
 * Get template content
 *
 * @param string $path
 * @param bool $in_theme
 * @param bool $hide_error
 * @return string|bool
 */
function get_template_content( $path, $in_theme, $hide_error = false ) {
    $full_path = get_full_path( $path, $in_theme );

    global $wp_filesystem;

    $url = '';

    $fs_path = preg_replace( '/\/[^\/]+$/', '', $full_path );

    if ( connect_fs( $url, "", $fs_path ) ) {

        if( $wp_filesystem->exists( $full_path ) ) {
            $text = $wp_filesystem->get_contents( $full_path );
            if( !$text ) {
                return "";
            } else {
                return $text;
            }
        } elseif ( !$hide_error ) {
            filesystem_error( __( "File doesn't exist" ) );
        }
    }
}


 
function save_template( $path, $content = null )
{
    $theme_path = get_stylesheet_directory();
    $full_path = $theme_path . '/woocommerce/' . $path;

    if ( is_null( $content ) )
    {
        $content = get_template_content( $path, true, true );
        if ( is_null( $content ) )
        {
            $content = get_template_content( $path, false );
        }
        else
        {
            return 'tsu';
        }
    }

    global $wp_filesystem;

    create_folder( $theme_path, '/woocommerce/' . $path );

    $result = $wp_filesystem->put_contents( $full_path, $content, FS_CHMOD_FILE);

    //Template saved to Theme successfully
    return $result ? 'tss' : 'tsu';
}


function dir_exist_file($path) {
 
    if (!is_dir($path)) {
        return FALSE;
    }

    $files = scandir($path);

    //  delete "." and ".."
    unset($files[0]);
    unset($files[1]);

    if (!empty($files[2])) {
       return TRUE;
    }

    return FALSE;
}


function create_folder( $theme_path, $path )
{
    global $wp_filesystem;

    $url = '';
    $fs_path = get_stylesheet_directory();
    $form_fields = array("data");
    if( connect_fs( $url, "", $fs_path, $form_fields ) )
    {

        $folders = explode( '/', $path );
        $dir = $theme_path;
        foreach( $folders as $folder )
        {
            if ( !empty( $folder ) && false === strpos( $folder, '.php' ) )
            {
                $dir .= '/' . $folder;
                if ( $wp_filesystem->is_dir( $dir ) )
                {
                    continue;
                } else {
                    $wp_filesystem->mkdir( $dir, FS_CHMOD_DIR );
                }
            }
        }
    }
}



