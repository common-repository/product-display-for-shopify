<?php
/*
Plugin Name: Product Display for Shopify
Plugin URI:  https://www.thatsoftwareguy.com/wp_product_display_for_shopify.html
Description: Shows off a product from your Shopify based store on your blog.
Version:     1.0
Author:      That Software Guy 
Author URI:  https://www.thatsoftwareguy.com 
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: shop_product_display
Domain Path: /languages
*/

function shop_product_display_shortcode($atts = [], $content = null, $tag = '')
{
   // normalize attribute keys, lowercase
   $atts = array_change_key_case((array)$atts, CASE_LOWER);
   $id = $atts['id'];

   $shoppd_settings = get_option('shoppd_settings');
   $url = $shoppd_settings['shoppd_url'];
   $api_key = $shoppd_settings['shoppd_api_key'];
   $password = $shoppd_settings['shoppd_password'];

   //API URL to get product by ID
   $requestURL = "https://" . $api_key . ":" . $password . "@" . $url;
   $requestURL .= "/admin/products/" . $id . ".json";

   $response = wp_remote_get($requestURL, array(
      'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
      'body' => null,
   ));
   if (is_wp_error($response)) {
      $o = shop_product_display_get_error("Product query failure: " . $response->get_error_message());
      return $o;
   }  else if (wp_remote_retrieve_response_code( $response ) != 200) {
      $o = shop_product_display_get_error("Product query unexpected return: " . wp_remote_retrieve_response_message( $response )); 
      return $o;
   }
   $response = json_decode(wp_remote_retrieve_body($response));

   // Initialize
   $data['name'] = ' ';
   $data['price'] = ' ';
   $data['special'] = ' ';
   $data['link'] = ' ';
   $data['image'] = ' ';
   $data['description'] = ' ';

   // Fill from response
   $data['name'] = sanitize_text_field($response->product->title);

   // show lowest price
   foreach($response->product->variants as $variant) { 
      if (!isset($price)) {
         $price = $variant->price; 
      } else {
         if ($variant->price < $price) $price = $variant->price; 
      }
   }
   $data['price'] = shop_product_display_price(sanitize_text_field($price));
   // field contains HTML markup
   $data['description'] = wp_kses_post($response->product->body_html);
   $image_url = esc_url($response->product->images[0]->src); 
   $data['image'] = '<img src="' . sanitize_text_field($image_url) . '" />';
   $data['link'] = "https://" . $url . "/products/" . sanitize_text_field($response->product->handle);

   // start output
   $o = '';

   // start box
   $o .= '<div class="shop_product_display-box">';

   $o .= '<div id="prod-left">' . '<a href="' . $data['link'] . '">' . $data['image'] . '</a>' . '</div>';
   $o .= '<div id="prod-right">' . '<a href="' . $data['link'] . '">' . $data['name'] . '</a>' . '<br />';
   $o .= $data['price'];
   $o .= '</div>';
   $o .= '<div class="prod-clear"></div>';
   $o .= '<div id="prod-desc">' . $data['description'] . '</div>';

   // enclosing tags
   if (!is_null($content)) {
      // secure output by executing the_content filter hook on $content
      $o .= apply_filters('the_content', $content);

      // run shortcode parser recursively
      $o .= do_shortcode($content);
   }

   // end box
   $o .= '</div>';

   // return output
   return $o;
}

function shop_product_display_price($price)
{
   setlocale(LC_MONETARY, 'en_US');
   return money_format('%.2n', $price);
}

function shop_product_display_get_error($msg)
{

   $o = '<div class="shop_product_display-box">';
   $o .= $msg;
   $o .= '</div>';
   return $o;
}

function shop_product_display_shortcodes_init()
{
   wp_register_style('shop_product_display', plugins_url('style.css', __FILE__));
   wp_enqueue_style('shop_product_display');

   add_shortcode('shop_product_display', 'shop_product_display_shortcode');
}

add_action('init', 'shop_product_display_shortcodes_init');

add_action('admin_menu', 'shoppd_add_admin_menu');
add_action('admin_init', 'shoppd_settings_init');


function shoppd_add_admin_menu()
{

   add_options_page('Product Display for Shopify', 'Product Display for Shopify', 'manage_options', 'shop_product_display_', 'shoppd_options_page');

}


function shoppd_settings_init()
{

   register_setting('shoppd_pluginPage', 'shoppd_settings');

   add_settings_section(
      'shoppd_pluginPage_section',
      __('Settings', 'wordpress'),
      'shoppd_settings_section_callback',
      'shoppd_pluginPage'
   );

   $args = array('size' => '80');
   add_settings_field(
      'shoppd_url',
      __('Shopify Domain (no http://)', 'wordpress'),
      'shoppd_url_render',
      'shoppd_pluginPage',
      'shoppd_pluginPage_section',
      $args
   );
   add_settings_field(
      'shoppd_api_key',
      __('Shopify Private app API key', 'wordpress'),
      'shoppd_api_key_render',
      'shoppd_pluginPage',
      'shoppd_pluginPage_section',
      $args
   );
   add_settings_field(
      'shoppd_password',
      __('Shopify Private app Password', 'wordpress'),
      'shoppd_password_render',
      'shoppd_pluginPage',
      'shoppd_pluginPage_section',
      $args
   );


}


function shoppd_url_render($args)
{

   $options = get_option('shoppd_settings');
   ?>
    <input type='text' name='shoppd_settings[shoppd_url]' value='<?php echo $options['shoppd_url']; ?>'
       <?php
       if (is_array($args) && sizeof($args) > 0) {
          foreach ($args as $key => $value) {
             echo $key . "=" . $value . " ";
          }
       }
       ?>>
   <?php

}

function shoppd_api_key_render($args)
{

   $options = get_option('shoppd_settings');
   ?>
    <input type='text' name='shoppd_settings[shoppd_api_key]' value='<?php echo $options['shoppd_api_key']; ?>'
       <?php
       if (is_array($args) && sizeof($args) > 0) {
          foreach ($args as $key => $value) {
             echo $key . "=" . $value . " ";
          }
       }
       ?>>
   <?php

}

function shoppd_password_render($args)
{

   $options = get_option('shoppd_settings');
   ?>
    <input type='text' name='shoppd_settings[shoppd_password]' value='<?php echo $options['shoppd_password']; ?>'
       <?php
       if (is_array($args) && sizeof($args) > 0) {
          foreach ($args as $key => $value) {
             echo $key . "=" . $value . " ";
          }
       }
       ?>>
   <?php

}


function shoppd_settings_section_callback()
{

   echo __('Settings required by this plugin', 'wordpress');

}


function shoppd_options_page()
{

   ?>
    <form action='options.php' method='post'>

        <h2>Product Display for Shopify</h2>

       <?php
       settings_fields('shoppd_pluginPage');
       do_settings_sections('shoppd_pluginPage');
       submit_button();
       ?>

    </form>
   <?php

}
