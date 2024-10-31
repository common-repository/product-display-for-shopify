=== Product Display for Shopify ===
Contributors: scottcwilson
Donate link: http://donate.thatsoftwareguy.com/
Tags: Shopify
Requires at least: 4.3 
Tested up to: 4.8
Stable tag: 1.0 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to easily display products from your Shopify store
on your WordPress blog using a shortcode.

== Description ==

Product Display for Shopify takes a product sku, and pulls in the product name, price, image, description and link, and displays it in a post. 

== Installation ==

Note: This is a two-part install; you have to do some configuration on your Shopify store admin, then you must install code on your WordPress site. 

In your Shopify admin, do the following: 

1. Login to the Shopify 2 Admin Panel.
1. Go to Apps, then click the "Manage private apps" link at the bottom of the page. 
1. Click the "Generate API credentials" button.  
1. Set the Private app name to "Product Display" and be sure you have Read access to Products, variants and collections.  
1. Click the save button, and note your Private app API key and Password.

Install the WordPress part of this mod as usual (using the Install button 
on the mod page on WordPress.org).  The follow these steps: 

1. In your WordPress admin, do the following: 
- In Plugins->Installed Plugins, click the "Activate" link under Product Display for Shopify.
- In Settings->Product Display for Shopify, set your Shopify URL, API Key and password.  

To show a specific product on your blog, use the shortcode 
[shop_product_display] with parameter "id" as a self closing tag.  
So showing product 417935405 would be done as follows: 

[shop_product_display id="417935405"]

The id is shown in the URL when you edit a product in your admin.

== Frequently Asked Questions ==
= Are there any requirements for products I'd like to display? =

The product should be visible in the Online Store (set in the Sales Channels box on the Product Editing screen). 

= I use a currency other than dollars - how do I change the price display? = 

Modify `product_display_for_shopify.php` and change the function `shop_product_display_price`.

== Screenshots ==

1. What the product information in your post will look like. 

== Changelog ==
First version

== Upgrade Notice ==
First version

