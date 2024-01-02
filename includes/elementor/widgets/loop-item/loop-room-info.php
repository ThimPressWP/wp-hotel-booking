<?php

namespace Elementor;

use Thim_EL_Kit\Utilities\Widget_Loop_Trait;
use WPHB\HBGroupControlTrait;

defined('ABSPATH') || exit;

class Thim_Ekit_Widget_Loop_Room_Info extends Widget_Icon_List 
{
    use HBGroupControlTrait;
    use Widget_Loop_Trait;

    public function get_name()
    {
        return 'loop-room-info';
    }
   
    public function get_title()
    {
        return esc_html__('Room Info', 'wp-hotel-booking');
    }

    public function get_icon()
    {
        return 'thim-eicon eicon-post-info';
    }

    public function get_inline_css_depends() {
		return array(
			array(
				'name'               => 'icon-list',
				'is_core_dependency' => true,
			),
		);
	}

    public function get_keywords() {
		return array( 'room', 'info' );
	}

    protected function register_controls_repeater( $repeater ) {
        $repeater->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Term', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'adults',
				'options' => array(
					'adults'         => 'Adults',
					'children'       => 'Children',
					'size'           => 'Room Size',
					'types'          => 'Room Type',
                    'beds'           => 'Beds'   
				),
			)
		);

        $repeater->add_control(
			'type_separator',
			array(
				'label'     => esc_html__( 'Seperate', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::TEXT,
				'ai'        => [
					'active' => false,
				],
				'default'   => ', ',
				'condition' => array(
					'type' => 'types',
					'show_one!' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'show_one',
			[
				'label'     => esc_html__( 'Show One Type', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'condition' => array(
					'type' => 'types',
				),
			]
		);

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Custom Text', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'ai'          => [
					'active' => false,
				],
                'condition' => array(
					'type!' => 'types',
				),
			)
        );

        $repeater->add_control(
			'selected_icon',
			array(
				'label'       => esc_html__( 'Choose Icon', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);
    }

    protected function register_controls()
    {
        parent::register_controls();

        $this->update_control( 'view', [
			'default' => 'inline'
		] );

		$repeater_list = new Repeater();
		$this->register_controls_repeater( $repeater_list );

		$this->update_control(
			'icon_list',
			array(
				'fields'      => $repeater_list->get_controls(),
				'default'     => array(
					array(
						'type'          => 'adults',
						'selected_icon' => array(
							'value'   => 'far fa-user-circle',
							'library' => 'fa-regular',
						),
					),
					array(
						'type'          => 'children',
						'selected_icon' => array(
							'value'   => 'fas fa-user',
							'library' => 'fa-solid',
						),
					),
				),
				'title_field' => '{{{ elementor.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <span style="text-transform: capitalize;">{{{ type }}}</span>',
			)
		);
		$this->remove_control( 'link_click' );
		$this->update_control(
			'text_color_hover',
			[
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item .elementor-icon-list-text a:hover,{{WRAPPER}} .elementor-icon-list-item a.elementor-icon-list-text:hover' => 'color: {{VALUE}};',
				],
			]
		);
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'icon_list', 'class', 'elementor-icon-list-items' );
		$this->add_render_attribute( 'list_item', 'class', 'elementor-icon-list-item' );

		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'icon_list', 'class', 'elementor-inline-items' );
			$this->add_render_attribute( 'list_item', 'class', 'elementor-inline-item' );
		}

		if ( ! empty( $settings['icon_list'] ) ) {
			?>
			<ul <?php $this->print_render_attribute_string( 'icon_list' ); ?>>
				<?php
				foreach ( $settings['icon_list'] as $repeater_item ) {
					?>
					<li <?php $this->print_render_attribute_string( 'list_item' ); ?>>

						<?php $this->render_item( $repeater_item ); ?>

					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
    }

    protected function render_item( $repeater_item ) {
		switch ( $repeater_item['type'] ) {
            case 'adults':
                $this->render_adults( $repeater_item );
                break;
            case 'children':  
                $this->render_children( $repeater_item );
                break;
            case 'size':     
                $this->render_size( $repeater_item );
                break;
            case 'types': 
                $this->render_types( $repeater_item );
                break;
            case 'beds':
                $this->render_beds( $repeater_item );
                break;   
        }
    }

    protected function render_adults( $repeater_item ) {
        // check render icon
		$this->render_icon( $repeater_item );
        ?>
        <span class="elementor-icon-list-text">
            <?php 
            if ( ! empty( $repeater_item['text'] ) ) {
                echo $repeater_item['text'];
            }
            printf( esc_html( _n( '%s Adult', '%s Adults', get_post_meta( get_the_ID(),'_hb_room_capacity_adult', true ), 'wp-hotel-booking' ) ), get_post_meta( get_the_ID(),'_hb_room_capacity_adult', true ) ); ?>
        </span>
        <?php
    }

    protected function render_children( $repeater_item ) {
        // check render icon
		$this->render_icon( $repeater_item );
        ?>
        <span class="elementor-icon-list-text">
            <?php 
            if ( ! empty( $repeater_item['text'] ) ) {
                echo $repeater_item['text'];
            }
            printf( esc_html( _n( '%s Child', '%s Children', get_post_meta( get_the_ID(),'_hb_max_child_per_room', true ), 'wp-hotel-booking' ) ), get_post_meta( get_the_ID(),'_hb_max_child_per_room', true ) ); ?>
        </span>
        <?php
    }

    protected function render_size( $repeater_item ) {
        // check render icon
		$this->render_icon( $repeater_item );
        ?>
        <span class="elementor-icon-list-text">
            <?php 
            if ( ! empty( $repeater_item['text'] ) ) {
                echo $repeater_item['text'];
            }
            printf( esc_html( _n( '%s', get_post_meta( get_the_ID(),'_hb_room_area', true ), 'wp-hotel-booking' ) ) ); ?>
        </span>
        <?php
    }

    protected function render_beds( $repeater_item ) {
        // check render icon
		$this->render_icon( $repeater_item );
        ?>
        <span class="elementor-icon-list-text">
            <?php 
            if ( ! empty( $repeater_item['text'] ) ) {
                echo $repeater_item['text'];
            }
            printf( esc_html( _n( '%s Bed', '%s Beds', get_post_meta( get_the_ID(),'_hb_room_beds', true ), 'wp-hotel-booking' ) ), get_post_meta( get_the_ID(),'_hb_room_beds', true ) ); ?>
        </span>
        <?php
    }

    protected function render_types( $repeater_item ) {
        $terms = get_the_terms( get_the_ID(), 'hb_room_type' );

        if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return false;
		}

        $terms_list = [];
        // check render icon
		$this->render_icon( $repeater_item );

        foreach ( $terms as $term ) {
            $terms_list[] = '<a href="' . esc_url( get_term_link( $term ) ) . '" class="loop-item-term elementor-icon-list-text">' . esc_html( $term->name ) . '</a>';
        }

        if ( 'yes' == $repeater_item['show_one'] ) {
			$value = $terms_list[0];
		} else {
			$value = implode( $repeater_item['term_separator'], $terms_list );
		}

		echo wp_kses_post( '<span class="elementor-icon-list-text">'. $value .'</span>' );
    }

    protected function render_icon( $repeater_item ) {
        if ( ! empty( $repeater_item['selected_icon']['value'] ) ) : ?>
			<span class="elementor-icon-list-icon">
				<?php Icons_Manager::render_icon( $repeater_item['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
		<?php endif;
    }

}