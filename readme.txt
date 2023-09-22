=== WP Hotel Booking ===
Contributors: thimpress, leehld, phamtungpth, thoakim
Donate link: https://thimpress.com/
Tags: hotel, booking, hotel booking, reservation, reservations
Requires at least: 5.6
Requires PHP: 7.0
Tested up to: 6.3.1
Stable tag: 2.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Hotel Booking Plugin - A complete hotel booking reservation plugin for WordPress.

== Description ==

A complete **hotel booking plugin** for WordPress.

> **One-Time Discount - 10 Days Left**

> ** Announcing:** NEW [Hotel WP Theme](https://themeforest.net/item/travel-booking-hotel-wordpress-theme-hotel-wp/18828322?utm_source=wporg&utm_medium=wphotelbookingDC&ref=thimpress&utm_campaign=wphotelbooking) - 100% Woocommerce compatible, only $64
or [The Ultimate Sailing Hotel](http://themeforest.net/item/hotel-wordpress-theme-sailing/13321455?utm_source=wporg&utm_medium=wphotelbookingDC&ref=thimpress&utm_campaign=wphotelbooking), using by 1500 hotels, $64 Only

###Hotel WordPress Themes###

Take a look at the Premium **[HotelWP - Hotel Wordpress Theme](https://themeforest.net/item/travel-booking-hotel-wordpress-theme-hotel-wp/18828322?utm_source=wporg&utm_medium=wphotelbookingDC&ref=thimpress&utm_campaign=wphotelbooking)** and **[Hotel Sailing Theme](http://themeforest.net/item/hotel-wordpress-theme-sailing/13321455?utm_source=wporg&utm_medium=wphotelbookingDC&ref=thimpress&utm_campaign=wphotelbooking)** we designed to work with WP Hotel Booking, all developers are welcome to develop WordPress Hotel Theme based on this awesome Hotel Booking plugin for WordPress. More WordPress Hotel Themes will come soon, both free WordPress themes as well as premium WordPress themes for this hotel booking / reservation plugin.

###Features###

- Complete Hotel Booking System
- Room Management
- Bookings Management
- Customer Management
- Coupon Management
- Pricing Plans Management
- Extra Packages Management
- Block Special Date Management
- Reports + Multilingual Ready
- Multi Currencies
- 4 Payment Gateways (Offline Payment, Authorize.net, Paypal, Stripe)

**Free add-ons for WP Hotel Booking are available in WordPress:**

- <a href="https://wordpress.org/plugins/wp-hotel-booking-authorize-payment/" target="_blank">Authorize.Net Payment method for WP Hotel Booking</a> - Authorize.Net payment method for WP Hotel Booking Plugin.
- <a href="https://wordpress.org/plugins/wp-hotel-booking-stripe-payment/" target="_blank">Stripe Payment method for WP Hotel Booking</a> - Stripe payment method for WP Hotel Booking Plugin.
- <a href="https://wordpress.org/plugins/wp-hotel-booking-report/" target="_blank">Statistic for WP Hotel Booking</a> - Statistic booking for WP Hotel Booking Plugin.
- <a href="https://wordpress.org/plugins/wp-hotel-booking-woocommerce/" target="_blank">Woocommerce payment method for WP Hotel Booking</a> - Support paying for booking of WP Hotel Booking plugin with the payment system provided by WooCommerce..
- <a href="https://wordpress.org/plugins/wp-hotel-booking-wpml-support/" target="_blank">WPML for WP Hotel Booking</a> - Multilnguage CMS support for WP Hotel Booking Plugin.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-hotel-booking` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin
4. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How many payment gateways does this hotel booking plugin offer? =

Paypal, Stripe, Offline payment, Authorize.net

= Can it has add-ons for hotel booking such as Pool, Airport? =

Yes.

== Screenshots ==

1. WP Hotel Booking Room Management
2. Create A New Room
3. Room Booking Management
4. Room Capacity
5. Addition Packages
6. Room Pricing Plans
7. System settings

== Changelog ==

= 2.0.7 (2023-09-22) =
~ Fixed: bug empty cart when book room on page search.

= 2.0.6 (2023-07-14) =
~ Added: button reset filter

= 2.0.5 (2023-07-07) =
~ Fixed: addition information display HTML.
~ Fixed: error search Room.
~ Remove: library jquery mobile.
~ Change: label "Facilities" to "Extra Options".
~ Added: feature config info Facilities.
~ Added: shortcode [hotel_booking_filter]


= 2.0.4 (2023-02-15) =
~ Fixed: error date checkin is available, but checkout right after (unavailable), can't book.

= 2.0.3 (2022-11-01) =
~ Fixed: error search page with Safari browser.
~ Fixed: error meta_box admin.

= 2.0.2 (2022-10-18) =
~ Fixed: coupon function.
~ Updated: library moment, select2 js to latest.
~ Show dates booked on calendar by Date format.
~ Fixed: styles Archive rooms.
~ Fixed: styles review star on detail room.
~ Fixed: error check available room very slow when change format date.

= 2.0.1 (2022-10-08) =
~ Fixed: the user can book a range of days that includes a disabled date.
~ Fixed: error calendar manage.
~ Fixed: security authorisation and CSRF checks in place when updating its settings.

= 2.0.0 (2022-10-06) =
~ Added: function check date available check booking room.
~ Optimize code.
~ Added: function coupon.
~ Merged: "WP Hotel Booking Coupon", "WP Hotel Booking Room", "WP Hotel Booking Block" plugins.
~ Sanitize, Escape, Validate.
~ Fixed: Security issues.
~ Fixed: styles of meta-boxes.
~ Added: calendar booking manager.
~ Added: page setup wizard.
~ Added: API for booking, check available.
~ Added: option disable dates for each room.
~ Added: option external link.
~ Added: function deposit.

= 1.10.5 =
~ Fix sanitize.

= 1.10.4 =
~ Fix security, use json instead of serialize.

= 1.10.3 =
~ Fix security CVE AC-2020-11-17-2

= 1.10.2 =
~ Fix compatible WP 5.5

= 1.10.1 =
- Fixed error PHP warming in cart and checkout page when empty price in Room.
- Fixed error extra require can remove.
- Fixed security stripe key
- Add filter calculate tax for extra service

= 1.10.0 =
~ Fixed not save Additional Information when using HTML element

= 1.9.10 =
~ Fixed not save Regular price and gallery when edit single room

= 1.9.9 =
~ Fixed some bugs security.
~ Fixed issue: "Check Availability" case choice room hasn't Addition package
~ Fixed minor bugs
~ Add option: Redirect to page after book room
~ Fixed: only role Hotel manager, Booking Editor or Administrator can create manual order

= 1.9.8.7 =
+ Fixed issue: not remove loading icon after the action done.

= 1.9.8.6 =
+ Fixed bug not work with j.n.Y date format.
+ Added loading effect when searching rooms, selecting rooms, checking out.

= 1.9.8.5 =
+ Fixed bug duplicate fields in settings pages.
+ Added more several hooks.

= 1.9.8.4 =
+ Fixed the bug not displaying booking orders in the All tab.

= 1.9.8.3 =
+ Fixed the bug when displaying the max children number in search form.
+ Minified several js and css files.

= 1.9.8.2 =
+ Added couple hooks in Select Extra Options view.

= 1.9.8.1 =
+ Corrected path to override the Extra options add-on.
+ Fixed the conflict issue which causes Content duplication when Yoase SEO On.
+ Fixed warning message when displaying the price with PHP7.
+ Corrected the text in Emails Settings.
+ Not display the Advanced Payment in the cart when it set as 0% or not set.

= 1.9.8 =
+ Fixed bug when searching by room capacity.
+ Added loading effect to the button when choose the extra options.

= 1.9.7.9 =
+ Changed the way of displaying the price when TAX included.
+ Fixed bug in Widget: Show title/label option not work.
+ Fixed bug: Required extra option not auto be added to cart when Custom Process option ON.
+ Fixed a small bug with Menu in Back End.
+ Updated several language texts.

= 1.9.7.8 =
+ Fixed bug when searching rooms with other languages.

= 1.9.7.7 =
+ Update cancel mail template

= 1.9.7.6 =
+ Update filter search room

= 1.9.7.5 =
+ Fix issue show unpublished extra room in search page
+ Update default setting room images size in single room gallery
+ Update process get related rooms
+ Update tool check override templates to check WP Hotel Booking addons

= 1.9.7.4 =
+ Add tool check override templates
+ Update templates instruction

= 1.9.7.3 =
+ Update offline payment instruction in thank you page template

= 1.9.7.2 =
+ Update select extra in search room page with custom process
+ Improve admin booking editor

= 1.9.7.1 =
+ Update maximum extra price

= 1.9.7 =
+ Update compatible with WPML

= 1.9.6.2 =
+ Update search room process

= 1.9.6.1 =
+ Fix search room process

= 1.9.6 =
+ Update search room process
+ Update search form template

= 1.9.5 =
- Update booking checkin checkout same day
- Update admin filter booking
- Add custom booking process option
- Restructure extra option
- GDPR compliance

= 1.9.4 =
- Update email booking details

= 1.9.3 =
* Add default list rooms page

= 1.9.2 =
* Optimize create default database tables process
* Fix issue select extra with Single purchase option

= 1.9.1 =
* Fix create default database tables process

= 1.9 =
* Add option disable select quantity in Hotel Search page

= 1.8.9 =
* Fix js typo error in checkout page
* Update pricing table base on "Week Starts On" option
* Remove admin setting lightbox tab

= 1.8.8 =
* Update custom page body classes

= 1.8.7 =
* Update checkout thank you page
* Update cart templates

= 1.8.6 =
* Update custom field for room and booking type

= 1.8.5 =
* Update custom class to default pages
* Update redirect thank you page after Paypal checkout
* Update cart template

= 1.8.4 =
* Fix duplicate admin settings

= 1.8.3 =
* Update process create database tables

= 1.8.2 =
* Update compatible with Wordpress 4.9.2
* Update admin settings

= 1.8.1 =
* Fix duplicate image in room gallery lightbox

= 1.8 =
* Update single room template

= 1.7.9.9 =
* Disable convert currency via Google Finance and Yahoo Finance

= 1.7.9.8 =
* Add link edit in single room page
* Update sort by capacity in search room page

= 1.7.9.7 =
* Update cart page
* Update room pricing plan template

= 1.7.9.6 =
* Update compatible with Wordpress 4.9

= 1.7.9.5 =
* Update admin booking page

= 1.7.9.4 =
* Add cancel booking email option
* Fix add to cart in single room of booking room add-on
* Update deprecated functions in Woocommerce add-on
* Update select date in booking room add-on

= 1.7.9.3 =
* Fix customer email in admin booking page

= 1.7.9.2 =
* Fix View Cart button in search room page

= 1.7.9.1 =
* Update search room template
* Update extra room package price in Woocommerce payment add-on

= 1.7.9 =
* Update hotel roles

= 1.7.8.9 =
* Update search room when update booking status for WPML Support add-on
* Update Woocommerce booking payment gateway

= 1.7.8.8 =
* Update compatible with PHP 5.5

= 1.7.8.7 =
* Update WP Hotel Booking WPML Support add-on

= 1.7.8.6 =
* Update thank you booking template

= 1.7.8.5 =
* Add thank you booking page

= 1.7.8.4 =
* Update language extra package add-on

= 1.7.8.3 =
* Improve auto update Woocommerce cart for Woocommerce payment add-on

= 1.7.8.2 =
* Fix query with database prefix

= 1.7.8.1 =
* Update customer place booking email

= 1.7.8 =
* Add Hotel Manager role to manage hotel booking
* Add Booking Editor role to manage rooms and bookings
* Update capabilities for users
* Update add-ons

= 1.7.7 =
- Update email from when customer place booking
- Update single search available template of booking room add-on

= 1.7.6.9 =
- Update search room based on timezone
- Update checkout required fields

= 1.7.6.8 =
- Update create pages process
- Add place booking email for admin

= 1.7.6.7 =
- Update datepicker with RTL languages
- Update booking completed email
- Add place booking email for customer

= 1.7.6.6 =
- Add minimum booking day option
- Update email booking notice
- Fix update Woocommrece cart when add room to cart in WP Hotel Booking Woocommerce add-on

= 1.7.6.5 =
- Update email booking notice process

= 1.7.6.4 =
- Fix bug in admin create new bookings process

= 1.7.6.3 =
- Fix add addition package to cart in WP Hotel Booking Woocommerce add-on

= 1.7.6.2 =
- Fix override template bug in WP Hotel Booking Room add-on

= 1.7.6.1 =
- Update file language

= 1.7.6 =
- Update WP Hotel Booking WPML Support add-on

= 1.7.5 =
- Fix deactivate previous version process for multisite
- Update Woocomerce payment add-on for Woocommerce version 3.x

= 1.7.4 =
* Update plugin classes and functions

= 1.7.3 =
* Fix update pricing plan

= 1.7.2 =
* Fix default value for new booking admin email
* Fix admin style make review starts in plugins page broken
* Update assets js
* Remove add plan button

= 1.7.1 =
* Update add-ons

= 1.7 =
* Update add-ons
* Fix notice when init room meta boxes
* Fix bug of timestamp pricing plan

= 1.6.3.2 =
* Fix required plugins path

= 1.6.3.1 =
* Compatibility with Wordpress 4.7.1
* Fix bug undefined get children function

= 1.6.3 =
* Compatibility with Wordpress 4.7
* Update WP Hotel Booking add-ons

= 1.6.2 =
* Fix checkout issue
* Update logic of search room process
* Update latest reviews shortcode
* Update best reviews shortcode
* Add an add-on supports book room without search room

= 1.1.6.1 =
* Update WP Booking WooCommerce add-on

= 1.1.6 =
* Update logic of search room process
* Update multiple choice addition packages
* Update mini cart template
* Update maximum capacity
* Update all templates
* Update to compatible with Wordpress 4.6.1
* Fix block room calender plan

= 1.1.5.1 =
* Add book manually
* User book
* Update Pricing
* Book in room add-on ( Support book room without search room )

= 1.1.4.1 =
* Auto cancel payment
* Booking inside single room detail

= 1.1.4 =
* Coupon update
* Report update
* Select room search
* Pricing plan

= 1.1.3 =
- Update check-in, check-out filter
- Query search
- Update report customer delete post

= 1.1.2 =
- Add template taxonomy room type
- Translate calendar search widget
- Update calendar format
- Update capacity (customer feedback) + remove option + add term meta + search query + admin update capacity

= 1.1.1 =
- Update Resize image, gallery, archive rooms
- Update Session class
- Support Loco Translation & Mulilanguages

= 1.1 =
- Add Sessions Class, Update Cart Class
- Optimize process checkout
- Remove data saving booking if it not usesful (postmeta)
- Optimize Payment Stripe process (remove stripe sdk, use REST API)
- Admin booking details
- Update hook processs addon

= 1.0.3 =
- Update, Add hook ajax search url result. 'hotel_booking_parse_search_param', 'hotel_booking_ajax_remove_cart_item'
- Addon Block Special Date (Angularjs, ajax)
- Update Rating

= 1.0.2 =
- Update Woocommerce addon
- Search rooms. Import demo data more than once. post_meta not unique.

= 1.0.1 =
- Fix load text-domain.
- Add language file en_US
- Add Extra Package
- Update, Fix Query search (customer's feedback: http://thimpress.com/forums/topic/calendar-availabality-room/#post-42595)
- Query search booking_pricing (Admin panel)
- Woocommerce Payment (add-on)

= 1.0 =
- An amazing Hotel Booking plugin for WordPress was born

== Upgrade Notice ==

= 1.0 =
- An amazing Hotel Booking plugin for WordPress was born
