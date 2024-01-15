import Sortable from 'sortablejs';
import { on } from 'delegated-events';

const hbReviewImages = () => {
    const { __ } = wp.i18n;
    const mediaElNodes = document.querySelectorAll( '.hb-image-info' );
    if ( ! mediaElNodes ) {
        return;
    }

    on( 'click', '.hb-gallery-add', function( event ) {
        event.preventDefault();
        const mediaElNode = this.closest( '.hb-image-info' );

        const hbUploader = wp.media( {
            title: __( 'Select Images', 'wp-hotel-booking' ), button: {
                text: __( 'Use these images', 'wp-hotel-booking' ),
            }, multiple:true, library: {
                type: 'image',
            },
        } );

        hbUploader.on( 'select', function() {
            const selection = hbUploader.state().get( 'selection' );

            let attachments = selection.filter( function( item ) {
                return item.toJSON().type === 'image';
            } ).map( function( item ) {
                return item.toJSON();
            } );
            attachments = attachments.filter( ( item ) => {
                return validateFile( item, mediaElNode );
            } );


            let attachmentIds = attachments.map( function( item ) {
                return item.id;
            } );

            const number = mediaElNode.querySelector( 'input[type="hidden"]' ).getAttribute( 'data-number' );
            attachmentIds = attachmentIds.slice( 0, number );
            mediaElNode.querySelector( 'input' ).value = attachmentIds.join();

            //Gallery preview
            let galleryPreviewHtml = '';

            for ( let i = 0; i < attachmentIds.length; i++ ) {
                let src = '';
                let dataId = '';
                if ( !! attachmentIds[ i ] ) {
                    dataId = attachmentIds[ i ];
                    if ( !! attachments[ i ].sizes && !! attachments[ i ].sizes.thumbnail ) {
                        src = attachments[ i ].sizes.thumbnail.url;
                    } else {
                        src = attachments[ i ].url;
                    }
                }
                galleryPreviewHtml += `<div class="hb-gallery-preview" data-id="${ dataId }">
						<div class="hb-gallery-centered"><img src="${ src }" alt="#">
						<span class="hb-gallery-remove dashicons dashicons dashicons-no-alt"></span>
						</div></div>`;
            }

            if ( !! galleryPreviewHtml ) {
                mediaElNode.querySelectorAll( '.hb-gallery-preview' ).forEach( ( e ) => e.remove() );
                mediaElNode.querySelector( '.hb-gallery-add' ).insertAdjacentHTML( 'beforebegin', galleryPreviewHtml );
            }
        } );
        hbUploader.on( 'open', function() {
            const selection = hbUploader.state().get( 'selection' );
            let attachmentIds = mediaElNode.querySelector( 'input' ).value;

            if ( attachmentIds.length > 0 ) {
                attachmentIds = attachmentIds.split( ',' );
                attachmentIds.forEach( function( id ) {
                    const attachment = wp.media.attachment( id );
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                } );
            }
        } );

        hbUploader.open();
    } );

    const sortElNode = document.querySelector( '.hb-image-info .hb-gallery-inner' );
    if ( sortElNode ) {
        Sortable.create( sortElNode, {
            handle: '.hb-gallery-preview',
            draggable: '.hb-gallery-preview',
            animation: 150,
            onEnd() {
                const mediaElNode = this.el.closest( '.hb-image-info' );
                reorderIds( mediaElNode );
            },
        } );
    }

    on( 'click', '.hb-image-info .hb-gallery-remove', function( event ) {
        event.preventDefault();
        const imageInfo = this.closest( '.hb-image-info' );
        this.closest( '.hb-gallery-preview' ).remove();
        reorderIds( imageInfo );
    } );

    //Remove image
    for ( let i = 0; i < mediaElNodes.length; i++ ) {
        const mediaElNode = mediaElNodes[ i ];
        const removeButtonNode = mediaElNode.querySelector( 'button.hb-image-remove' );
        if ( ! removeButtonNode ) {
            return;
        }
        removeButtonNode.addEventListener( 'click', function() {
            mediaElNode.querySelector( 'input[type=text]' ).value = '';
            mediaElNode.querySelector( 'input[type=hidden]' ).value = '';
            mediaElNode.querySelector( '.hb-image-preview img' ).style.display = 'none';
        } );
    }
    //Function
};

const reorderIds = ( mediaElNode ) => {
    const previewGalleries = mediaElNode.querySelectorAll( '.hb-gallery-preview' );
    let dataIds = [];
    for ( let i = 0; i < previewGalleries.length; i++ ) {
        dataIds.push( previewGalleries[ i ].getAttribute( 'data-id' ) );
    }

    dataIds = dataIds.filter( function( el ) {
        return !! el;
    } );
    mediaElNode.querySelector( 'input' ).value = dataIds.join();
};

const validateFile = ( item, mediaElNode ) => {
    const maxFileSize = mediaElNode.getAttribute( 'data-max-file-size' );
    const itemFileSize = ( item.filesizeInBytes ) / 1024;

    if ( maxFileSize && maxFileSize < itemFileSize ) {
        return false;
    }

    return true;
};

export default hbReviewImages;
