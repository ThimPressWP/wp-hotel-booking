<?php
/**
 * gallery lightbox
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */
global $hb_room;
$gallery = $hb_room->gallery;
?>
<div class="hb-room-type-gallery">
    <?php if( $gallery ): foreach( $gallery as $image ){?>
        <a  class="hb-room-gallery" rel="hb-room-gallery-<?php echo $hb_room->post->ID;?>" data-lightbox="hb-room-gallery[<?php echo $hb_room->post->ID;?>]" data-title="<?php echo $image['alt'];?>" href="<?php echo $image['src'];?>">
            <img src="<?php echo $image['thumb'];?>" alt="<?php echo $image['alt'];?>" data-id="<?php echo $image['id'];?>" />
        </a>
    <?php } endif;?>
</div>