import Sortable from 'sortablejs';

const roomExternalLink = () => {
    let mediaUploader;
    const container = document.querySelector( '#room_external_link' );
    if ( ! container ) {
        return;
    }

    const addButton = container.querySelector('.wphb-add-external-button'),
    sampleRow = container.querySelector( 'tr.wphb-sample-row' ),
    tableBody = container.querySelector( '.wphb-room-external-link-table tbody' );

    const iconList = wphbAdminRoomExternalLink.list_icon_ids;
    let externalLink = container.querySelector( '#wphb_room_external_link' );

    const sortExternalLinks = new Sortable(tableBody, {
        animation  : 150,
        handle: '.dashicons-move',
    });

    const handleSelectIcon = (target) => {
        mediaUploader = wp.media({
            title: wphbAdminRoomExternalLink.uploader_title,
            button: { text: wphbAdminRoomExternalLink.uploader_button_text },
            library: {
                type: 'image',
                post__in: iconList.split(','),   // restrict to these IDs
                orderby: 'post__in'
            },
            multiple: false
        });
        mediaUploader.on('open', function() {
            // Remove upload tab completely
            setTimeout(function() {
                // hide all upload button when icon lists is empty
                const dropzones = mediaUploader.el.querySelectorAll('.upload-ui, .media-menu-item#menu-item-upload');
                dropzones.forEach(el => el.style.display = 'none');
                if( mediaUploader.el.querySelectorAll( '.attachments li' ).length < 1 ) {
                    mediaUploader.el.querySelector( '.upload-message' ).innerHTML = wphbAdminRoomExternalLink.no_icons_found;
                }
            }, 10);
            
            // Pre-select existing image
            const selection = mediaUploader.state().get('selection');
            selection.reset();
            
            if ( target.querySelector('input[name="link-icon-id"]').value ) {
                const attachmentid = target.querySelector('input[name="link-icon-id"]').value;
                const attachment = wp.media.attachment(attachmentid);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }
        });
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            target.querySelector( 'img' ).src = attachment.url;
            target.querySelector( 'input[name="link-icon-id"]' ).value = attachment.id;
            target.querySelector( 'input[name="link-icon-url"]' ).value = attachment.url;
        });
        
        mediaUploader.open();
    }

    container.addEventListener( 'click', (e) => {
        let target = e.target;
        if ( target.classList.contains( 'wphb-add-external-button' ) ) {
            tableBody.insertAdjacentHTML('beforeend', `<tr class="wphb-single-external-link">${sampleRow.innerHTML}</tr>`);
        } else if ( target.classList.contains( 'wphb-select-link-icon' ) ) {
            if ( ! target.closest( '.wphb-single-external-link' ) ) {
                return;
            }
            handleSelectIcon( target.closest( '.wphb-single-external-link' ) );
        } else if ( target.tagName === 'LABEL' ) {
            if ( ! target.closest( '.wphb-single-external-link' ) ) {
                return;
            }
            let checkbox = target.closest( '.wphb-single-external-link' ).querySelector( 'input[name="enable-link"]' );
            if ( checkbox.checked ) {
                checkbox.checked = false;
            } else {
                checkbox.checked = true;
            }
        } else if ( target.classList.contains( 'delete-external-link' ) ) {
            const row = target.closest( '.wphb-single-external-link' );
            if ( row ) {
                row.remove();
            }
        } else if ( target.classList.contains( 'wphb-save-link-button' ) ) {
            // e.preventDefault();
            let linkList = container.querySelectorAll( '.wphb-single-external-link' );
            if ( linkList.length > 0 ) {
                let data = [];
                linkList.forEach( (ele, idx) => {
                    let icon_id = ele.querySelector( 'input[name="link-icon-id"]' ),
                    icon_url = ele.querySelector( 'input[name="link-icon-url"]' ),
                    external_link = ele.querySelector( 'input[name="link-value"]' ),
                    enable_link = ele.querySelector( 'input[name="enable-link"]' );
                    if ( ! icon_id.value || ! icon_url.value || ! external_link.value ) {
                        return;
                    }
                    data.push( { icon_id: icon_id.value, icon_url: icon_url.value, external_link:external_link.value, index:idx+1, enable:enable_link.checked } );
                } );
                externalLink.value = data.length > 0 ? JSON.stringify( data ) : 'test2';
            } else {
                externalLink.value = 'test';
            }
            // console.log( externalLink.value );
            target.closest( 'form' ).submit();
        }
    } );
}
document.addEventListener( 'DOMContentLoaded', () => {
    roomExternalLink();
} );