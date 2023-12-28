<?php

namespace Elementor;

use WPHB\HBGroupControlTrait;
use Thim_EL_Kit\GroupControlTrait;
use WPHB_Room;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Thim_Ekit_Widget_Room_Review extends Widget_Base
{
    use HBGroupControlTrait;
    use GroupControlTrait;

    public function get_name()
    {
        return 'room-review';
    }

    public function get_title()
    {
        return esc_html__('Room Review', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-review';
    }

    public function get_categories()
    {
        return array(\WPHB\Elementor::CATEGORY_SINGLE_ROOM);
    }

    public function get_base()
    {
        return basename(__FILE__, '.php');
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_tabs',
            [
                'label' => __('Reviews', 'wp-hotel-booking'),
            ]
        );

        $this->add_control(
            'layout',
            array(
                'label'   => esc_html__('Select Price', 'wp-hotel-booking'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'base',
                'options' => array(
                    'base'            => esc_html__('Base', 'wp-hotel-booking'),
                    'review_list'     => esc_html__('Review List', 'wp-hotel-booking'),
                    'review_form'     => esc_html__('Review Form', 'wp-hotel-booking'),
                ),
            )
        );

        $this->add_control(
			'show_avatar',
			[
				'label'        => esc_html__( 'Avatar', 'wp-hotel-booking' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'wp-hotel-booking' ),
				'label_off'    => esc_html__( 'Hide', 'wp-hotel-booking' ),
				'return_value' => 'yes',
			]
		);

        $this->add_control(
			'button_popup_review_text',
			[
				'label'         => esc_html__( 'Button', 'wp-hotel-booking' ),
				'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Add your text button here', 'wp-hotel-booking' ),
                'default'       => esc_html__( 'Write a review', 'wp-hotel-booking' ),
				'condition'     => [
					'layout' => 'review_form',
				]
			]
		);

        $this->end_controls_section();

        $this->_register_style_review();
        $this->_register_style_button_popup();
    }

    protected function _register_style_review()
    {
        $this->start_controls_section(
            'section_review',
            array(
                'label' => esc_html__('Content', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
			'section_review_color_star',
			array(
				'label'     => esc_html__( 'Star Color', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					"{{WRAPPER}} .hb-room-single__review" => '--room-single-rating-star-color: {{VALUE}};',
				),
			)
		);

        $this->add_responsive_control(
			'radius_item',
			[
				'label'      => esc_html__( 'Radius Item', 'wp-hotel-booking' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .hb-room-single__review' => '--border-radius-item: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'user_title', [
				'label'     => esc_html__( 'User Title', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_user_title_review', '.hb-room-single__review #reviews .commentlist .meta strong');

        $this->add_control(
			'user_comment', [
				'label'     => esc_html__( 'User Comment', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->register_style_typo_color_margin('room_user_comment_review', '.hb-room-single__review #reviews .commentlist .meta time, .hb-room-single__review .commentlist p');

        $this->end_controls_section();
    }

    protected function _register_style_button_popup(){
        $this->start_controls_section(
            'section_button',
            array(
                'label' => esc_html__('Button', 'wp-hotel-booking'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'     => [
					'layout' => 'review_form',
				]
            )
        );

        $this->register_button_style( 'button_popup_review', '.hb-room-single__review__button, .hb-room-single__review .form-submit .submit' );

        $this->end_controls_section();
    }

    protected function render()
    {
        do_action('WPHB/modules/single-room/before-preview-query');

        $settings        = $this->get_settings_for_display();
        global $hb_room;
        $hb_room = \WPHB_Room::instance(get_the_ID());
        $extra_class = '';

        if ( $settings['show_avatar'] != 'yes' ) {
            $extra_class = ' hide-avatar';
        }
        ?>
            <div class="hb-room-single__review <?php echo esc_attr($extra_class) ?>">
                <?php 
                if ( $settings['layout'] == 'base' ) {
                   echo comments_template(); 
                }elseif ( $settings['layout'] == 'review_list' ) {
                    $this->_render_comment_review_list($hb_room);
                }elseif ( $settings['layout'] == 'review_form' ) {
                    $this->_render_comment_review_form($settings);
                }
                ?>
            </div>
        <?php

        do_action('WPHB/modules/single-room/after-preview-query');
    }

    protected function _render_comment_review_list($hb_room)
    {
        $rating_total   = round($hb_room->average_rating(), 1);
        $total_count    = $hb_room->get_review_count();
        $comments       = $hb_room->get_review_details();

        if ( comments_open() ) {
            ?>
            <div class="hb-room-single__review__header">
                <div class="hb-room-single__review__header__left">
                    <div class="average-value">
                        <?php printf( '%s/5', $rating_total ); ?> 
                    </div>
                    <div class="review-star">
                        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating"
                        title="<?php echo esc_html( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating_total ) ); ?>">
                        <span style="width:<?php echo ( ( $rating_total / 5 ) * 100 ); ?>%"></span>
                        </div>
                    </div>
                    <div class="review-amount">
                        <?php  printf( __('%s rating','wp-hotel-booking'), $total_count ); ?>
                    </div>
                </div>
                <div class="hb-room-single__review__header__right">
                    <div class="detailed-rating">
                    <?php
                    for ( $i = 5; $i >= 1; $i-- ) {
                        $total = 0;
                        ?>
                        <div class="stars">
                            <span class="key"><?php printf( esc_html( _n( '%s star', '%s stars', $i, 'wp-hotel-booking' ) ), $i ); ?></span>
                            <?php 
                                foreach ( $comments as $comment ) {
                                    $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
                                    if ( $rating == $i ) {
                                        $total++;
                                    }
                                }
                            ?>
                            <div class="bar">
                                <div class="full_bar" style="width:<?php echo round( $total / $total_count, 2 ) * 100; ?>% "></div>
                            </div>
                            <span class="count"><?php echo $total; ?></span>
                        </div>
                        <?php
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="hb-room-single__review__content" id="reviews">
                <div id="comments">
                <?php if ( !empty($comments) ) { ?>
                    <ol class="commentlist">
                        <?php
                        wp_list_comments( apply_filters( 'hb_room_review_list_args', array( 'callback' => 'hb_comments' )), $comments ); 
                        ?>
                    </ol>
                    <div class="clear"></div>
                    <?php if ( get_comment_pages_count($comments) > 1 && get_option( 'page_comments' ) ) { ?>
                        <nav class="hb-pagination">
                            <?php paginate_comments_links(
                                apply_filters(
                                    'hb_comment_pagination_args',
                                    array(
                                        'prev_text' => '&larr;',
                                        'next_text' => '&rarr;',
                                        'type'      => 'list',
                                    )
                                )
                            ); ?>
                        </nav>
                    <?php } 
                    }
                    ?>
                </div>
            <?php
        }
    }

    protected function _render_comment_review_form($settings)
    {
        global $hb_settings;
        $button = !empty($settings['button_popup_review_text']) ? $settings['button_popup_review_text'] : esc_html__( 'Write a review', 'wp-hotel-booking' );
        
        if ( comments_open() && hb_customer_booked_room(get_the_ID()) ) {
            ?>
            <button class="hb-room-single__review__button"><?php echo $button ?></button>
            <div class="hb-room-single__review__form">
                <div id="review_form_wrapper">
                    <div id="review_form">
                        <?php
                        $commenter    = wp_get_current_commenter();
                        $comment_form = array(
                            'title_reply'          =>  __( 'Write a review', 'wp-hotel-booking' ),
                            'comment_notes_before' => '',
                            'comment_notes_after'  => '',
                            'fields'               => array(
                                'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
                                            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
                                'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
                                            '<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
                            ),
                            'label_submit'         => __( 'Submit', 'wp-hotel-booking' ),
                            'logged_in_as'         => '',
                            'comment_field'        => '',
                        );

                        if ( $hb_settings->get( 'enable_review_rating' ) ) {
                            $comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your Rating', 'wp-hotel-booking' ) . '</label>
                                </p><div class="hb-rating-input"></div>';
                        }

                        $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'wp-hotel-booking' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
                        comment_form( apply_filters( 'hb_product_review_comment_form_args', $comment_form ) );
                        ?>
                    </div>
                </div>
            </div>
            <?php
        } else { ?>
            <p class="hb-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'wp-hotel-booking' ); ?></p>
        <?php }
        $this->add_js_popup();
    }

    protected function add_js_popup()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.hb-room-single__review__button').on('click',function (e) {
                    e.preventDefault();
                    var form = $(this).parent('.hb-room-single__review').find('.hb-room-single__review__form');
                    form.addClass('active');
                });
                $('.hb-room-single__review__form, .close-popup').on('click',function (e) {
                    $('.hb-room-single__review__form').removeClass('active');
                });
                $('#review_form_wrapper').on('click',function (e) {
                    e.stopPropagation()
                });
            });
        </script>
        <?php
    }

}