import Sortable from 'sortablejs';

const roomExternalLinkSettings = () => {
    let mediaUploader;
    const container = document.querySelector( '#room_external_link' );
    if ( ! container ) {
        return;
    }

    const roomPostForm = document.querySelector( '#post[name="post"]' );

    const externalLinkTable = container.querySelector( '.wphb-room-external-link-table tbody' );

    let roomExternalLinkInput = container.querySelector( '#_hb_room_external_link' );

    const sortExternalLinks = new Sortable(externalLinkTable, {
        animation  : 150,
        handle: '.dashicons-move',
        onEnd : (event) => {
            const row = externalLinkTable.querySelectorAll( 'tr.wphb-single-external-link' );
            if ( row.length > 0 ) {
                row.forEach( ( ele, idx ) => {
                    ele.dataset.order = idx + 1;
                } );
            }
        }
    });

    container.addEventListener( 'click', (e) => {
        let target = e.target;
        if ( target.tagName === 'LABEL' ) {
            if ( ! target.closest( '.wphb-single-external-link' ) ) {
                return;
            }
            let checkbox = target.closest( '.wphb-single-external-link' ).querySelector( 'input[name="enable-link"]' );
            if ( checkbox.checked ) {
                checkbox.checked = false;
            } else {
                checkbox.checked = true;
            }
        }
    } );
    document.addEventListener( 'submit', (e) => {
        if ( e.target === roomPostForm ) {
            e.preventDefault();
            const externalLinks = externalLinkTable.querySelectorAll( '.wphb-single-external-link' );
            const data = {};
            if ( externalLinks.length > 0 ) {
                externalLinks.forEach( (ele, idx) => {
                    let field_id = ele.dataset.id,
                    order = ele.dataset.order,
                    external_link = ele.querySelector( 'input[name="external_link"]' ).value,
                    enabled = ele.querySelector( 'input[name="enable-link"]' ).checked;
                    
                    data[field_id] = { order, external_link, enabled };
                    // data.push(obj);
                } );
            }
            roomExternalLinkInput.value = Object.keys(data).length > 0 ? JSON.stringify( data ) : '';
            roomPostForm.submit();
        }
    } );
}
document.addEventListener( 'DOMContentLoaded', () => {
    roomExternalLinkSettings();
} );