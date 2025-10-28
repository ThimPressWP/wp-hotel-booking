let mediaUploader;

const externalLinkSetting = () => {
    const externalLinkTable = document.querySelector( '.wphb-external-link-table' ),
        addRowButton = document.querySelector( '.wphb-external-link-add-new' ),
        sampleRow = externalLinkTable.querySelector( '.wphb-sample-row' ),
        hbSettingForm = document.querySelector('[name="hb-admin-settings-form"]'),
        externalLinkSetting = document.querySelector('input[name="tp_hotel_booking_external_link_settings"]');

    const uniqueid = function(){
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }
    const handleAddIcon = ( e, target ) => {
        e.preventDefault();
                    
        const button = e.target;
        const row = button.closest('tr.wphb-single-external-link'),
        iconId = row.querySelector( 'input[name="icon-id"]' ),
        iconUrl = row.querySelector( 'input[name="icon-url"]' ),
        image = row.querySelector('.wphb-select-icon');
        
        // Create media uploader with multiple selection
        mediaUploader = wp.media({
            title: wphbIconExternalLinkSettings.uploader_title,
            button: {
                text: wphbIconExternalLinkSettings.uploader_button_text
            },
            library: {
                type: 'image'
            },
            multiple: false
        });
        
        mediaUploader.on('open', function() {
            // Pre-select existing image
            const selection = mediaUploader.state().get('selection');
            selection.reset();
            
            if ( iconId.value ) {
                const attachmentid = iconId.value;
                const attachment = wp.media.attachment(attachmentid);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }
        });
        // On image select
        mediaUploader.on('select', function() {
            const selection = mediaUploader.state().get('selection');
            
            selection.forEach(function(attachment) {
                attachment = attachment.toJSON();
                iconId.value = attachment.id;
                iconUrl.value = attachment.url;
                image.src = attachment.url;
            });
        });
        
        mediaUploader.open();
    }

    document.addEventListener('click', (e) => {
        let target = e.target;
        if ( target === addRowButton ) {
            externalLinkTable.insertAdjacentHTML('beforeend', `<tr class="wphb-single-external-link" data-id=${uniqueid()}>${sampleRow.innerHTML}</tr>`);
        } else if ( target.classList.contains( 'delete-external-link' ) ) {
            const row = target.closest( '.wphb-single-external-link' );
            if ( row ) {
                row.remove();
            }
        } else if ( target.classList.contains('wphb-select-icon') ) {
            handleAddIcon( e, target );
        }
    });
    document.addEventListener( 'submit', (e) => {
        
        if ( e.target === hbSettingForm ) {
            e.preventDefault();
            const externalLinks = externalLinkTable.querySelectorAll( '.wphb-single-external-link' );
            const data = {};
            if ( externalLinks.length > 0 ) {
                externalLinks.forEach( (ele, idx) => {
                    let icon_id = ele.querySelector( 'input[name="icon-id"]' ).value,
                    icon_url = ele.querySelector( 'input[name="icon-url"]' ).value,
                    external_link = ele.querySelector( 'input[name="url"]' ).value,
                    title = ele.querySelector( 'input[name="title"]' ).value,
                    field_id = ele.dataset.id;

                    data[field_id] = { icon_id, icon_url, external_link, title };
                } );
            }
            externalLinkSetting.value = Object.keys(data).length > 0 ? JSON.stringify( data ) : '';
            
            hbSettingForm.submit();
        }
    } );
};

document.addEventListener('DOMContentLoaded', function() {
    externalLinkSetting();
});
