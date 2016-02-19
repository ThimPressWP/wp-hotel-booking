=== WP Hotel Booking ===
Contributors: thimpress
Donate link: https://thimpress.com/
Tags: hotel, booking, hotel booking, reservation, reservations
Requires at least: 3.0.1
Tested up to: 4.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Hotel Booking - A complete hotel booking plugin for WordPress.

== Description ==

A complete hotel booking plugin for WordPress.

**Features**

* Complete Hotel Booking System
* Room Management
* Bookings Management
* Customer Management
* Coupon Management
* Pricing Plans Management
* Extra Packages Management
* Block Special Date Management
* Reports + Multilingual Ready
* Multi Currencies
* 4 Payment Gateways (Offline Payment, Authorize.net, Paypal, Stripe)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-hotel-booking` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)


== Frequently Asked Questions ==

= How many payment gateways does this hotel booking plugin offer? =

Paypal, Stripe, Offline payment, Authorize.net

= Can it has add-ons for hotel booking such as Pool, Airport? =

Yes.

== Screenshots ==

1. WP Hotel Booking Dashboard
2. Online Hotel Booking form

== Changelog ==

= 1.0.1 =
* Fix load text-domain.
* Add language file en_US
* Add Extra Package
* Update, Fix Query search ( customer's feedback: http://thimpress.com/forums/topic/calendar-availabality-room/#post-42595 )
* Query search booking_pricing( Admin panel )
* Woocommerce Payment( Addon )

= 1.0.2 =
* Update Woocommerce addon
* Search rooms. Import demo data more than once. post_meta not unique.

= 1.0.3 =
* Update, Add hook ajax search url result. 'hotel_booking_parse_search_param', 'hotel_booking_ajax_remove_cart_item'
* Addon Block Special Date( Angularjs, ajax )
* Update Rating

= 1.1 =
* Add Sessions Class, Update Cart Class
* Optimize process checkout
* Remove data saving booking if it not usesful( postmeta )
* Optimize Payment Stripe process( remove stripe sdk, use REST API )
* Admin booking details
* Update hook processs addon

= 1.1.1 =
* Update Resize image, gallery, archive rooms
* Update Session class
* Support Loco Translation & Mulilanguages

= 1.1.2 =
* Add template taxonomy room type
* Translate calendar search widget
* Update calendar format
* Update capacity( customer feedback ) + remove option + add term meta + search query + admin update capacity

== Upgrade Notice ==

= 1.0 =
* An amazing Hotel Booking plugin for WordPress was born
