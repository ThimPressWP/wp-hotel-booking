let mediaUploader;

const externalLinkSetting = () => {
    const externalLinkTable = document.querySelector( '.wphb-external-link-table' ),
        addRowButton = document.querySelector( '.wphb-external-link-add-new' ),
        hbSettingForm = document.querySelector('[name="hb-admin-settings-form"]'),
        externalLinkSettingInput = document.querySelector('input[name="tp_hotel_booking_external_link_settings"]');

    if ( ! externalLinkTable || ! externalLinkSettingInput ) {
        return;
    }

    const sampleRow = externalLinkTable.querySelector( '.wphb-sample-row' );

    const uniqueid = function(){
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    const syncExternalLinkData = () => {
        const externalLinks = externalLinkTable.querySelectorAll( '.wphb-single-external-link' );
        const data = {};
        if ( externalLinks.length > 0 ) {
            externalLinks.forEach( (ele) => {
                const iconIdInput = ele.querySelector( 'input[name="icon-id"]' ),
                    iconUrlInput = ele.querySelector( 'input[name="icon-url"]' ),
                    urlInput = ele.querySelector( 'input[name="url"]' ),
                    titleInput = ele.querySelector( 'input[name="title"]' );

                const icon_id = iconIdInput ? iconIdInput.value : '',
                    icon_url = iconUrlInput ? iconUrlInput.value : '',
                    external_link = urlInput ? urlInput.value : '',
                    title = titleInput ? titleInput.value : '',
                    field_id = ele.dataset.id;

                data[field_id] = { icon_id, icon_url, external_link, title };
            } );
        }
        externalLinkSettingInput.value = Object.keys(data).length > 0 ? JSON.stringify( data ) : '';
    };

    window.wphbSyncExternalLinkSettings = syncExternalLinkData;

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
            syncExternalLinkData();
            externalLinkSettingInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
        });
        
        mediaUploader.open();
    }

    document.addEventListener('click', (e) => {
        let target = e.target;
        if ( target === addRowButton || target.closest( '.wphb-external-link-add-new' ) ) {
            if ( sampleRow ) {
                externalLinkTable.insertAdjacentHTML('beforeend', `<tr class="wphb-single-external-link" data-id="${uniqueid()}">${sampleRow.innerHTML}</tr>`);
                syncExternalLinkData();
                externalLinkSettingInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
            }
        } else if ( target.classList.contains( 'delete-external-link' ) ) {
            const row = target.closest( '.wphb-single-external-link' );
            if ( row ) {
                row.remove();
                syncExternalLinkData();
                externalLinkSettingInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
            }
        } else if ( target.classList.contains('wphb-select-icon') ) {
            handleAddIcon( e, target );
        }
    });

    externalLinkTable.addEventListener( 'input', () => {
        syncExternalLinkData();
    } );

    document.addEventListener( 'submit', (e) => {
        if ( e.target === hbSettingForm ) {
            syncExternalLinkData();
        }
    }, true );

    syncExternalLinkData();
};

if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', externalLinkSetting );
} else {
    externalLinkSetting();
}
