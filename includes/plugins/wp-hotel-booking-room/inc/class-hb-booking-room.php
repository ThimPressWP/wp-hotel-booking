<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-18 15:32:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-21 16:33:24
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'TP_Hotel_Booking_Room_Extenstion' ) ) {

    class TP_Hotel_Booking_Room_Extenstion {
        
        private static $instance = null;

        public function __construct() {
            add_action( 'hb_admin_settings_tab_after', array( $this, 'admin_settings' ) );
            // init
            $this->init();
        }

        // add admin setting
        public function admin_settings( $tab ) {
            if ( $tab !== 'room' ) {
                return;
            }

            $settings = hb_settings();
            ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Enable book in single room', 'wp-hotel-booking-room' ); ?></th>
                    <td>
                        <input type="hidden" name="<?php echo esc_attr( $settings->get_field_name( 'enable_single_book' ) ); ?>" value="0" />
                        <input type="checkbox" name="<?php echo esc_attr( $settings->get_field_name( 'enable_single_book' ) ); ?>" <?php checked( $settings->get( 'enable_single_book' ) ? 1 : 0, 1 ); ?> value="1" />
                    </td>
                </tr>
            </table>
            <?php
        }

        public function init() {
            if ( !hb_settings()->get( 'enable_single_book', 0 ) ) {
                return;
            }

            add_action( 'hotel_booking_single_room_title', array( $this, 'single_add_button' ), 9 );
            add_action( 'wp_footer', array( $this, 'wp_footer' ) );
            // enqueue script
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

            add_action( 'wp_ajax_check_room_availabel', array( $this, 'check_room_availabel' ) );
            add_action( 'wp_ajax_nopriv_check_room_availabel', array( $this, 'check_room_availabel' ) );

            add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_redirect' ), 10, 2 );

            add_action( 'wp_ajax_hotel_booking_single_check_room_available', array( $this, 'hotel_booking_single_check_room_available' ) );
            add_action( 'wp_ajax_nopriv_hotel_booking_single_check_room_available', array( $this, 'hotel_booking_single_check_room_available' ) );
        }

        public function single_add_button() {
            ob_start();
            $this->get_template( 'single-search-button.php' );
            $html = ob_get_clean();
            echo $html;
        }

        public function wp_footer() {
            $html = array();
//            $html[] = '<div id="hotel_booking_room_hidden"></div>';
            ob_start();
            // search form.
            $this->get_template( 'single-search-available.php' );
            // book form.
            $this->get_template( 'single-book-room.php' );
            $html[] = ob_get_clean();
            echo implode( '', $html );
        }

        // enqueue script
        public function enqueue() {
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_register_script( 'magnific-popup', TP_HB_BOOKING_ROOM_URI . 'inc/libraries/magnific-popup/jquery.magnific-popup.min.js', array(), false, true );
            wp_enqueue_script( 'magnific-popup' );

            wp_register_style( 'magnific-popup', TP_HB_BOOKING_ROOM_URI . 'inc/libraries/magnific-popup/magnific-popup.css', array(), false, true );
            wp_enqueue_style( 'magnific-popup' );

            wp_enqueue_style( 'wp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/css/site.css' );
            wp_enqueue_script( 'wp-hotel-booking-room', TP_HB_BOOKING_ROOM_URI . 'assets/js/site.js' );
        }

        public function check_room_availabel() {
            // ajax referer
            if ( !isset( $_POST['check-room-availabel-nonce'] ) || !check_ajax_referer( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ) ) {
                return;
            }

            $room_id = false;
            if ( isset( $_POST['hotel_booking_room_id'] ) && is_numeric( $_POST['hotel_booking_room_id'] ) ) {
                $room_id = absint( $_POST['hotel_booking_room_id'] );
            }

            $check_in_date = isset( $_POST['hotel_booking_room_check_in_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_in_timestamp'] ) : '';
            $check_in_date = $check_in_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

            $check_out_date = isset( $_POST['hotel_booking_room_check_out_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_out_timestamp'] ) : '';
            $check_out_date = $check_out_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

            $args = apply_filters( 'hotel_booking_query_room_available', array( 'room_id' => $room_id, 'check_in_date' => $check_in_date, 'check_out_date' => $check_out_date ) );
            // get available room qty
            $available = hotel_booking_get_qty( $args );

            if ( !is_wp_error( $available ) ) {
                wp_send_json( array( 'status' => true, 'qty' => $available, 'check_in_date' => date( 'm/d/Y', $check_in_date ), 'check_out_date' => date( 'm/d/Y', $check_out_date ) ) );
                die();
            } else {
                wp_send_json( array( 'status' => false, 'message' => $available->get_error_message() ) );
                die();
            }

            wp_send_json( array( 'status' => false, 'message' => __( 'No room found.', 'wp-hotel-booking-room' ) ) );
            die();
        }

        public function add_to_cart_redirect( $param, $room ) {
            if ( isset( $param['status'] ) && $param['status'] === 'success' && isset( $_POST['is_single'] ) && $_POST['is_single'] ) {
                $param['redirect'] = hb_get_cart_url();
            }

            return $param;
        }

        public function template_path() {
            return apply_filters( 'hb_room_addon_template_path', 'tp-hotel-booking' );
        }

        /**
         * get template part
         *
         * @param   string $slug
         * @param   string $name
         *
         * @return  string
         */
        public function get_template_part( $slug, $name = '' ) {
            $template = '';

            // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
            if ( $name ) {
                $template = locate_template( array( "{$slug}-{$name}.php", $this->template_path() . "/{$slug}-{$name}.php" ) );
            }

            // Get default slug-name.php
            if ( !$template && $name && file_exists( TP_HB_BOOKING_ROOM_PATH . "/templates/{$slug}-{$name}.php" ) ) {
                $template = TP_HB_BOOKING_ROOM_PATH . "/templates/{$slug}-{$name}.php";
            }

            // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
            if ( !$template ) {
                $template = locate_template( array( "{$slug}.php", $this->template_path() . "{$slug}.php" ) );
            }

            // Allow 3rd party plugin filter template file from their plugin
            if ( $template ) {
                $template = apply_filters( 'hb_room_addon_get_template_part', $template, $slug, $name );
            }
            if ( $template && file_exists( $template ) ) {
                load_template( $template, false );
            }

            return $template;
        }

        /**
         * Get other templates passing attributes and including the file.
         *
         * @param string $template_name
         * @param array  $args          (default: array())
         * @param string $template_path (default: '')
         * @param string $default_path  (default: '')
         *
         * @return void
         */
        public function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
            if ( $args && is_array( $args ) ) {
                extract( $args );
            }

            $located = $this->locate_template( $template_name, $template_path, $default_path );

            if ( !file_exists( $located ) ) {
                _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
                return;
            }
            // Allow 3rd party plugin filter template file from their plugin
            $located = apply_filters( 'hb_room_addon_get_template', $located, $template_name, $args, $template_path, $default_path );

            do_action( 'hb_room_before_template_part', $template_name, $template_path, $located, $args );

            include( $located );

            do_action( 'hb_room_after_template_part', $template_name, $template_path, $located, $args );
        }

        /**
         * Locate a template and return the path for inclusion.
         *
         * This is the load order:
         *
         *        yourtheme        /    $template_path    /    $template_name
         *        yourtheme        /    $template_name
         *        $default_path    /    $template_name
         *
         * @access public
         *
         * @param string $template_name
         * @param string $template_path (default: '')
         * @param string $default_path  (default: '')
         *
         * @return string
         */
        public function locate_template( $template_name, $template_path = '', $default_path = '' ) {

            if ( !$template_path ) {
                $template_path = $this->template_path();
            }

            if ( !$default_path ) {
                $default_path = TP_HB_BOOKING_ROOM_PATH . '/templates/';
            }

            $template = null;
            // Look within passed path within the theme - this is priority
            $template = locate_template(
                    array(
                        trailingslashit( $template_path ) . $template_name,
                        $template_name
                    )
            );
            // Get default template
            if ( !$template ) {
                $template = $default_path . $template_name;
            }

            // Return what we found
            return apply_filters( 'hb_room_locate_template', $template, $template_name, $template_path );
        }

        public function hotel_booking_single_check_room_available() {
            if ( !isset( $_POST['hb-booking-single-room-check-nonce-action'] ) || !wp_verify_nonce( $_POST['hb-booking-single-room-check-nonce-action'], 'hb_booking_single_room_check_nonce_action' ) ) {
                return;
            }

            $errors = array();

            if ( !isset( $_POST['room-id'] ) || !is_numeric( $_POST['check_in_date_timestamp'] ) ) {
                $errors[] = __( 'Check in date is required.', 'wp-hotel-booking-room' );
            } else {
                $room_id = absint( $_POST['room-id'] );
            }

            if ( !isset( $_POST['check_in_date'] ) || !isset( $_POST['check_in_date_timestamp'] ) || !is_numeric( $_POST['check_in_date_timestamp'] ) ) {
                $errors[] = __( 'Check in date is required.', 'wp-hotel-booking-room' );
            } else {
                $checkindate_text = sanitize_text_field( $_POST['check_in_date'] );
                $checkindate = absint( $_POST['check_in_date_timestamp'] );
            }

            if ( !isset( $_POST['check_out_date_timestamp'] ) || !is_numeric( $_POST['check_out_date_timestamp'] ) ) {
                $errors[] = __( 'Check out date is required.', 'wp-hotel-booking-room' );
            } else {
                $checkoutdate_text = sanitize_text_field( $_POST['check_out_date'] );
                $checkoutdate = absint( $_POST['check_out_date_timestamp'] );
            }

            // valid request and require field
            if ( empty( $errors ) ) {
                $qty = hotel_booking_get_room_available( $room_id, array(
                    'check_in_date' => $checkindate,
                    'check_out_date' => $checkoutdate
                        ) );

                if ( absint( $qty ) > 0 ) {

                    // room has been found
                    wp_send_json( array(
                        'status' => true,
                        'check_in_date_text' => $checkindate_text,
                        'check_in_date_text' => $checkoutdate_text,
                        'check_in_date' => date( 'm/d/Y', $checkindate ),
                        'check_out_date' => date( 'm/d/Y', $checkoutdate ),
                        'qty' => $qty
                    ) );
                } else {
                    $errors[] = sprintf( __( 'No room found in %s and %s', 'wp-hotel-booking-room' ), $checkindate_text, $checkoutdate_text );
                }
            }

            // input is not pass validate, sanitize
            wp_send_json( array( 'status' => false, 'messages' => $errors ) );
        }
        
        public static function instance(){
            if ( ! self::$instance ) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }
    }

}