<?php
$title = 'mr';
$first_name = 'Nguyễn Ngọc';
$last_name = 'Tú';
$address = 'Thắng Trí - Minh Trí - Sóc Sơn - Hà Nội';
$city = 'Hà Nội';
$state = '';
$postal_code = 10000;
$country = 'Vietnamese';
$phone = '0123456789';
$fax = '';
$email = 'iamacustomer@gmail.com';
$addition_information = 'I want to ...';
?>
<div class="hb-order-new-customer">
    <div class="hb-col-padding hb-col-border">
        <h4><?php _e( 'New Customer', 'tp-hotel-booking' );?></h4>
        <ul class="hb-form-table">
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Title', 'tp-hotel-booking' );?><span class="hb-required">*</span> </label>
                <div class="hb-form-field-input">
                    <?php hb_dropdown_titles( array( 'selected' => $title ) );?>
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Name', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="first_name" value="<?php echo $first_name;?>" placeholder="<?php _e( 'First name', 'tp-hotel-booking' );?>" size="30" />
                    <input type="text" name="last_name" value="<?php echo $last_name;?>" placeholder="<?php _e( 'Last name', 'tp-hotel-booking' );?>" size="30" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Address', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="address" value="<?php echo $address;?>" placeholder="<?php _e( 'Address', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'City', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="city" value="<?php echo $city;?>" placeholder="<?php _e( 'City', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'State', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="state" value="<?php echo $state;?>" placeholder="<?php _e( 'State', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Postal Code', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="postal_code" value="<?php echo $postal_code;?>" placeholder="<?php _e( 'Postal code', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Country', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="country" value="<?php echo $country;?>" placeholder="<?php _e( 'Country', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Phone', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="text" name="phone" value="<?php echo $phone;?>" placeholder="<?php _e( 'Phone Number', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Email', 'tp-hotel-booking' );?><span class="hb-required">*</span></label>
                <div class="hb-form-field-input">
                    <input type="email" name="email" value="<?php echo $email;?>" placeholder="<?php _e( 'Email address', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Fax', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <input type="text" name="fax" value="<?php echo $fax;?>" placeholder="<?php _e( 'Fax', 'tp-hotel-booking' );?>" />
                </div>
            </li>
            <li class="hb-form-field">
                <label class="hb-form-field-label"><?php _e( 'Addition Information', 'tp-hotel-booking' );?></label>
                <div class="hb-form-field-input">
                    <textarea name="addition_information"><?php echo $addition_information;?></textarea>
                </div>
            </li>
        </ul>
    </div>
</div>