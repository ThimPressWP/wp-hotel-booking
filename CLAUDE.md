# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Hotel Booking is a WordPress plugin (v2.3.1-beta-2) by ThimPress for hotel room reservation. It manages rooms (`hb_room` CPT), bookings (`hb_booking` CPT), coupons (`hb_coupon` CPT), pricing plans, cart/checkout, and payment gateways. Requires PHP 7.4+ and WordPress 6.4+.

## Build & Development Commands

### JavaScript (Webpack via wp-scripts)
```bash
npm run start          # Dev build with watch mode
npm run build          # Production build (outputs to assets/dist/js/)
```

### CSS/SCSS (Gulp)
```bash
npm run build-style    # Compile SCSS from assets/scss/ → assets/css/
gulp watch_scss        # Watch SCSS for changes
gulp mincss            # Minify CSS files
```

### PHP Linting & Formatting
```bash
composer run lint       # Run PHPCS (WordPress-Core standard)
composer run format     # Run PHPCBF auto-fixer
```

### Full Release
```bash
npm run build-makepot-zip   # composer update --no-dev → build → makepot → gulp release
```

### Translation
```bash
npm run makepot        # Generate languages/wp-hotel-booking.pot
```

## Architecture

### Bootstrap Flow
`wp-hotel-booking.php` → singleton `WP_Hotel_Booking` → `includes()`:
1. Loads Composer autoloader (`vendor/autoload.php`)
2. Registers `WPHB_Autoloader` (legacy class-based autoload)
3. Loads global files (post types, settings, functions, cart, booking, REST API, etc.)
4. Loads admin (`WPHB_Admin`) or frontend (shortcodes) conditionally
5. Initializes modern `TemplateHooks` singletons (`CheckRoomsTemplate`, `ArchiveRoomTemplate`, etc.)

### Dual Autoloading System
- **PSR-4 (Composer):** `WPHB\` namespace → `includes/` directory. Used by newer code in `includes/TemplateHooks/`, `includes/Helpers/`.
- **Legacy autoloader** (`WPHB_Autoloader`): Maps class names like `WPHB_*` → `class-wphb-*.php`. Has special path resolution for `WPHB_Abstract_*` (abstracts/), `WPHB_Payment_Gateway_*` (gateways/), `HB_Widget_*` (widgets/), `WPHB_Admin_Metabox_*` (admin/metaboxes/).

New classes should use the `WPHB\` namespace and PSR-4 convention.

### Key Constants
- `WPHB_FILE`, `WPHB_PLUGIN_PATH`, `WPHB_PLUGIN_URL`, `WPHB_VERSION`
- `WPHB_ROOM_CT` = `'hb_room'` (room post type slug)
- `WPHB_DEBUG` — when enabled, assets load unminified with cache-busting

### Settings System
`WPHB_Settings` manages all options with prefix `tp_hotel_booking_`. Debug mode check: `WPHB_Settings::is_debug()`.

### Custom Database Tables
Created on activation by `WPHB_Install`:
- `{prefix}hotel_booking_order_items` — booking line items
- `{prefix}hotel_booking_order_itemmeta` — order item metadata
- `{prefix}hotel_booking_plans` — room pricing plans (with JSON `pricing` column)

### REST API
Base namespace: `/wp-json/wphb/v1/`
- `WPHB_Core_API` → frontend controllers (rooms)
- `WPHB_Admin_Core_API` → admin controllers (rooms, updates, reviews)
- Controllers extend `WPHB_Abstract_REST_Controller`

### AJAX
`WPHB_Ajax` registers wp_ajax/wp_ajax_nopriv handlers for: `place_order`, `apply_coupon`, `ajax_add_to_cart`, `check_room_available`, `parse_search_params`, and admin operations.

### Template System
Templates in `templates/` are theme-overridable. `WPHB_Template_Loader` resolves templates by checking `theme/wp-hotel-booking/` first, then the plugin's `templates/` directory.

Two hook systems coexist:
- **Legacy:** `includes/wphb-template-hooks.php` — action/filter hooks like `hotel_booking_loop_room_*`, `hotel_booking_single_room_*`
- **Modern:** `includes/TemplateHooks/` — namespace-based classes using `Singleton` trait with `init()` method

### Payment Gateways
Extend `WPHB_Payment_Gateway_Base` in `includes/gateways/`. Built-in: Offline, PayPal. The autoloader resolves gateway classes from subdirectories of `includes/gateways/`.

### Webpack Entry Points
Frontend: `hotel-booking`, `hotel-booking-v2`, `filter-by`, `sort-by`, `room-review`, `wphb-single-room`
Admin: `room-review` (TSX/React), `room-external-link`

Output: `assets/dist/js/[name].min.js` (production) or `[name].js` (dev)

### Built-in Sub-plugin
`includes/plugins/wp-hotel-booking-extra/` — extra room packages/add-ons. Loaded as part of the main plugin, not a separate plugin.

### Integrations
- **Elementor:** `includes/elementor/` — widgets for archive/single room pages, loaded via `thim_ekit/modules/handle` hook
- **Gutenberg blocks:** `block-templates/` — `archive-room.html`, `single-room.html`

## Code Standards

- PHP: WordPress-Core via PHPCS (Yoda conditions and strict comparisons are relaxed — see `phpcs.xml`)
- JS: `@wordpress/eslint-plugin` with tab indentation
- Text domain: `wp-hotel-booking` — all user-facing strings must use it
