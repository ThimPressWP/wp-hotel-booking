const getFirstDayOfMonth = (year, month) => {
    return new Date(year, month, 1);
}

/**
 * It adds a click event listener to each link in the tabs, and when clicked, it removes the active
 * class from all tabs and tab content, and then adds the active class to the clicked tab and its
 * corresponding tab content
 */
const MetaboxRoomTabs = () => {
    const roomID = hotel_settings.room_id;
    if ( roomID == 0 ) return;

    const url = new URL(document.URL);
    const linkTabs = document.querySelectorAll('.wphb-meta-box__room-tab__tabs li a');
    const elemTabs = document.querySelectorAll('.wphb-meta-box__room-tab__tabs li');
    const elemContent = document.querySelectorAll('.wphb-meta-box-room-panels');


    // show tab active when update post;
    const tabActive = window.localStorage.getItem('tabActive');
    url.searchParams.set('tab', tabActive);
    window.history.pushState( '', '', url.href );
    // end show tab active when update post;

    if ( linkTabs.length == 0 || elemTabs.length == 0 || elemContent.length == 0 ) return ;
    //hide first load
    elemTabs[0].classList.add('active');
    elemContent[0].classList.add('active');

    linkTabs.forEach( function( link ) {
        const tabID = link.getAttribute('href');
        const tabContent = document.querySelector( '#' + tabID );
        //active tab when reload page

        if ( url.searchParams.get('tab') == tabActive ) {
            tabContent.classList.remove('active');
            link.parentNode.classList.remove('active');
            if ( tabContent.getAttribute('id') == tabActive ) {
                tabContent.classList.add('active');
                link.parentNode.classList.add('active');
                if( tabContent.getAttribute('id') == 'block_room_data' ) {
                    roomBlockDate();
                }
            }
        }

        link.addEventListener('click', function (e) {
            e.preventDefault();
            clear();
            url.searchParams.set('tab', tabID);
            link.parentNode.classList.add('active');
            window.history.pushState( '', '', url.href );
            window.localStorage.setItem('tabActive', tabID );
            if ( tabContent !== null || url.searchParams.get('tab') == tabContent.getAttribute('id') ) {
                tabContent.classList.add('active');
                if( tabContent.getAttribute('id') == 'block_room_data' ) {
                    roomBlockDate();
                }
            }
        })
    })
    const clear = () => {
        linkTabs.forEach( function( link ) {
            link.parentNode.classList.remove('active');
            const tabID = link.getAttribute('href');
            const tabContent = document.querySelector( '#' + tabID );
            if ( tabContent !== null ) {
                tabContent.classList.remove('active');
            }
        })
    }
};

/**
 * It fetches the pricing plans for the current month and renders them on the calendar
 */
const viewAllPlanSingleRoom = () => {

    const getPrices = async ( dayElem ) => {
        const events = [];
        const roomID = hotel_settings.room_id;

        if ( roomID == null ) return events;

		try {
			const response = await wp.apiFetch( {
				path: 'wphb/v1/admin/rooms/pricing-plans',
				method: 'POST',
				data: { date:dayElem , roomID },
			} );

			const {status , data } = response;
			if ( 'success' === status ) {
				const bookings = JSON.parse(data)
        		for (var i = 0; i < bookings.length; ++i) {
        			const booking = bookings[i],
        				day = new Date(booking.d),
        				today = new Date();
                    const MyDateString = (day.getFullYear() + '-' + ('0' + (day.getMonth() + 1 )).slice(-2) + '-' + ('0' + day.getDate()).slice(-2));
        			if ( booking.price > 0 ) {
        				events.push({
        					title: booking.price + '$',
                            start: MyDateString,
                            textColor: '#d04a61',
                            backgroundColor: '#FFFFFF',
                            borderColor: '#FFFFFF',
                            classNames: ['wphb-pricing-plan-event'],
        				});
        			}
        		}
                return events;
			}

		} catch ( error ) {
			alert( error.message && error.message );
		}
    }

    const calendarEl = document.getElementById('calendar_room_pricing');
    if ( calendarEl == null ) return;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        events: function(info, successCallback, failureCallback) {
            const date = new Date(info.end);
            const firstDayCurrentMonth = getFirstDayOfMonth(
                date.getFullYear(),
                date.getMonth(),
            );

            const events = getPrices( firstDayCurrentMonth );

            (async () => {
                successCallback( await events )
            })()
        },
        selectable: true,
        height: 500,
    });

    const btn = document.querySelector('button.show-all-plan');

    if( btn == null ) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        if ( calendarEl.hasChildNodes() ) {
            btn.textContent = btn.dataset.textView;
            calendar.destroy();
        } else {
            btn.textContent = btn.dataset.textClose;
            calendar.render();
        }

    });
}

/**
 * It creates a calendar that allows you to select dates and then save them to the database
 * @returns A function.
 */
const roomBlockDate = () => {
    //submit block room
    const roomID = hotel_settings.room_id;
    let removeMonth = false;
    let argsBlock = hotel_settings.block_dates ?? [];

    if ( roomID == null ) return;

    const submit = async () => {

        try {
			const response = await wp.apiFetch( {
				path: 'wphb/v1/admin/rooms/block-date',
				method: 'POST',
				data: { argsBlock , roomID , removeMonth },
			} );

			const {status , message } = response;
			if ( 'success' === status && message ) {
                alert( message );
			}
            removeMonth = false;
		} catch ( error ) {
			alert( error.message && error.message );
		}
    }

    /**
     * It checks if the date is already selected, if it is, it removes the selected class and removes
     * the date from the array, if it isn't, it adds the selected class and adds the date to the array
     * @param date - The date of the day you clicked on.
     * @param ele - The element that was clicked.
     * @param args - an array of dates that have been selected.
     * @returns the array of dates that have been selected.
     */
    const checkedBlock = ( date, ele , args ) => {
        const eleHighLight = ele.querySelector('.fc-highlight');
        if ( ele.classList.contains( 'selected') ) {
            ele.classList.remove('selected');
            ele.style.backgroundColor = '#fff';
            const childNodeEvent = ele.querySelector('._hb_event_selected');
            if ( childNodeEvent != null ) {
                childNodeEvent.style.background = '#fff';
                childNodeEvent.classList.remove('_hb_event_selected');
                if ( eleHighLight != null) {
                    eleHighLight.style.background = '#fff';
                }
            }
            const index = args.indexOf( String( date.valueOf() / 1000) );
            if ( index != -1 ) {
                args.splice( index, 1 );
            }
        } else {
            if ( eleHighLight != null) {
                eleHighLight.style.backgroundColor = '#27262240';
            }
            ele.style.backgroundColor = '#27262240';
            ele.className += ' selected';
            const index = args.indexOf( String( date.valueOf() / 1000) );
            if ( index == -1 ) {
                args.push( String( date.valueOf() / 1000 ) );
            }
        }
        argsBlock = args;
        return argsBlock;
    }


    /**
     * It removes all the block dates from the calendar.
     * @param ele - The element that is clicked
     * @param date - The date you want to check
     * @param argsBlock - an array of dates that are blocked
     */
    const removeAllBlockDate = async ( ele , date, argsBlock ) => {
        if ( ele === null ) return;
        const yearCurrent = date.getFullYear();
        const monthCurrent = date.getMonth() + 1;
        const daysOfMonth = document.querySelectorAll('#calender_block .fc-daygrid-day:not(.fc-day-other)');

        for ( var i = 0; i < daysOfMonth.length; ++i ) {
            const elem = daysOfMonth[i];
            const dateEle = new Date(elem.dataset.date);
            const index = ( dateEle.valueOf() / 1000).toString();
            if ( argsBlock.includes( index ) ) {
                const bgBanner = elem.querySelector('.fc-daygrid-bg-harness');
                if( bgBanner != null){
                    bgBanner.remove();
                }
                if ( dateEle.getFullYear() == yearCurrent && ( dateEle.getMonth()  + 1 ) == monthCurrent ) {
                    checkedBlock( dateEle, elem, argsBlock );
                }
            }
        }

        removeMonth = true;
    }

    /**
     * It takes a date, and checks if it's in the block list. If it is, it adds a class to the date
     * element
     * @param ele - The element that was clicked.
     * @param argsBlock - an object that contains the following properties:
     */
    const addAllMonthBlockDate = async ( ele , argsBlock ) => {
        if ( ele === null ) return;
        const daysOfMonth = document.querySelectorAll('#calender_block .fc-daygrid-day:not(.fc-day-other):not(.selected)');
        if ( daysOfMonth.length == 0 ) return;

        for ( var i = 0; i < daysOfMonth.length; ++i ) {
            const elem = daysOfMonth[i];
            const date = new Date(elem.getAttribute('data-date'));
            checkedBlock( date, elem, argsBlock );
        }

    }

    /* The above code is a function that is used to sync the calendar with the selected dates. */
    syncCalendar = () => {
        const daysOfMonth = document.querySelectorAll('#calender_block .fc-daygrid-day:not(.fc-day-other):not(.selected)');
        if ( daysOfMonth.length > 0 ) {
            daysOfMonth.forEach( function( el ) {
                el.classList.remove('selected');
                const eleBg = el.querySelector('.fc-daygrid-bg-harness');
                if( eleBg != null){
                    eleBg.remove();
                }
            });
        }
        argsBlock.forEach( function( date ) {
            const day = new Date( date * 1000 );
            const MyDateString = (day.getFullYear() + '-' + ('0' + (day.getMonth() + 1 )).slice(-2) + '-' + ('0' + day.getDate()).slice(-2));
            const daySelected = document.querySelector('.fc-daygrid-day[data-date="'+MyDateString+'"]');
            if(daySelected != null){
                daySelected.classList.add('selected');
                daySelected.style.backgroundColor = '#27262261';
            }
        });
        const selectedEvents = document.querySelectorAll('._hb_event_selected');
        if ( selectedEvents.length > 0 ) {
            selectedEvents.forEach( function( el ) {
                const parentBg = el.closest('.fc-daygrid-day');
                parentBg.classList.add('selected');
            });
        };
        const daysOther = document.querySelectorAll('#calender_block .fc-daygrid-day.fc-day-other');
        if ( daysOther.length > 0 ) {
            daysOther.forEach( function( el ) {
                el.classList.remove('selected');
                const eleBg = el.querySelector('.fc-daygrid-bg-harness');
                if( eleBg != null){
                    eleBg.remove();
                }
            });
        }
    }

    const events = [];
    argsBlock.map( function( date ) {
        const day = new Date( date * 1000 );
        const MyDateString = (day.getFullYear() + '-' + ('0' + (day.getMonth() + 1 )).slice(-2) + '-' + ('0' + day.getDate()).slice(-2));
        events.push({
            start: MyDateString,
            backgroundColor : '#ffdc2926',
            display: 'background',
            classNames:['_hb_event_selected'],
        });
    });

    const eleShowBlock = document.getElementById('calender_block');
    if ( eleShowBlock == null ) return;

	const calendarBlock = new FullCalendar.Calendar(eleShowBlock, {
        headerToolbar: {
            right: 'add_month disable_all prev,next'
        },
        initialView: 'dayGridMonth',
		selectable: true,
		height: 500,
        events: events,
        dateClick: function(info) {
            const date = new Date(info.dateStr);
            checkedBlock( date, info.dayEl, argsBlock );
        },
        customButtons: {
            prev: {
                click: function(e) {
                    calendarBlock.prev();
                    syncCalendar();
                },
            },
            next: {
                click: function() {
                    calendarBlock.next();
                    syncCalendar();
                },
            },
            disable_all: {
                text: 'Open This Month',
                click: function() {
                    const date = calendarBlock.getDate();
                    removeAllBlockDate( this , date, argsBlock );
                }
            },
            add_month: {
                text: 'Block This Month',
                click: function() {
                    addAllMonthBlockDate( this , argsBlock );
                }
            }
        }
	});

    const btnUpdate = document.querySelector('#tp_hotel_booking_block_date button.update_block');

    if( btnUpdate === null ) return;
    btnUpdate.addEventListener('click', function (e) {
        e.preventDefault();
        submit( argsBlock );
    });

    // render when active tab;
    calendarBlock.render();
    const selectedEvents = document.querySelectorAll('._hb_event_selected');
    if ( selectedEvents.length > 0 ) {
        selectedEvents.forEach( function( el ) {
            const parentBg = el.closest('.fc-daygrid-day');
            parentBg.classList.add('selected');
        });
    };
}
/**
 * It creates a new FullCalendar instance, and then renders it
 * @returns A function that returns a function.
 */
const CalendarManager = () => {
    const eleBooking = document.getElementById( 'manager_booking' );
    if ( eleBooking == null || typeof FullCalendar === 'undefined' ) return;

    const DAY_MS = 24 * 60 * 60 * 1000;
    const statusLabels = {
        'hb-processing': 'Processing',
        'hb-completed': 'Completed',
        'hb-pending': 'Pending',
        'hb-cancelled': 'Cancelled',
    };

    const escapeHtml = ( str ) => String( str ?? '' ).replace( /[&<>"']/g, ( c ) => ( {
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[ c ] ) );

    // Decode HTML entities (e.g. currency symbols) then escape again for safe output.
    const decodeEntities = ( str ) => {
        const doc = new DOMParser().parseFromString( String( str ?? '' ), 'text/html' );
        return doc.documentElement.textContent || '';
    };

    const toYmd = ( date ) => {
        const d = new Date( date );
        return d.getFullYear() + '-' + ( '0' + ( d.getMonth() + 1 ) ).slice( -2 ) + '-' + ( '0' + d.getDate() ).slice( -2 );
    };

    const getBookings = async ( info ) => {
        try {
            const response = await wp.apiFetch( {
                path: 'wphb/v1/admin/rooms/manager-bookings?startDay=' + toYmd( info.start ) + '&endDay=' + toYmd( info.end ),
                method: 'GET',
            } );
            const { status, data } = response;
            return 'success' === status && Array.isArray( data ) ? data : [];
        } catch ( error ) {
            alert( error.message && error.message );
            return [];
        }
    };

    const getStatus = ( event ) => event.extendedProps?.status || 'hb-completed';

    const getNights = ( event ) => {
        if ( ! event.start || ! event.end ) return 0;
        // The end date is sent as check-out + 1 day (exclusive end for the calendar).
        return Math.max( Math.round( ( event.end - event.start ) / DAY_MS ) - 1, 0 );
    };

    /* ---------- Booking detail modal ---------- */
    const onKeydown = ( e ) => {
        if ( 'Escape' === e.key ) closeModal();
    };

    const closeModal = () => {
        const modal = document.getElementById( 'wphb-cm-modal' );
        if ( modal ) modal.remove();
        document.body.classList.remove( 'wphb-cm-modal-open' );
        document.removeEventListener( 'keydown', onKeydown );
    };

    const openModal = ( event ) => {
        closeModal();

        const order = event.extendedProps?.data_order || {};
        const status = getStatus( event );
        const statusLabel = statusLabels[ status ] || status.replace( 'hb-', '' );
        const nights = getNights( event );
        const nightsText = nights > 0 ? nights + ( nights > 1 ? ' nights' : ' night' ) : '';
        const link = escapeHtml( order.link_edit );

        const html =
            '<div id="wphb-cm-modal" class="wphb-cm-modal" role="dialog" aria-modal="true" aria-labelledby="wphb-cm-modal-title">' +
                '<div class="wphb-cm-modal__dialog wphb-cm-status--' + escapeHtml( status ) + '">' +
                    '<div class="wphb-cm-modal__header">' +
                        '<div class="wphb-cm-modal__heading">' +
                            '<span class="wphb-cm-badge">' + escapeHtml( statusLabel ) + '</span>' +
                            '<h2 id="wphb-cm-modal-title">' + escapeHtml( order.title || event.title ) + '</h2>' +
                        '</div>' +
                        '<button type="button" class="wphb-cm-modal__close" aria-label="Close">' +
                            '<span class="dashicons dashicons-no-alt"></span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="wphb-cm-modal__body">' +
                        '<div class="wphb-cm-stay">' +
                            '<div class="wphb-cm-stay__item">' +
                                '<span class="wphb-cm-label">Check-in</span>' +
                                '<strong>' + escapeHtml( order.start_date_popup ) + '</strong>' +
                            '</div>' +
                            '<div class="wphb-cm-stay__arrow">' +
                                '<span class="dashicons dashicons-arrow-right-alt"></span>' +
                                ( nightsText ? '<small>' + nightsText + '</small>' : '' ) +
                            '</div>' +
                            '<div class="wphb-cm-stay__item">' +
                                '<span class="wphb-cm-label">Check-out</span>' +
                                '<strong>' + escapeHtml( order.end_date_popup ) + '</strong>' +
                            '</div>' +
                        '</div>' +
                        '<dl class="wphb-cm-meta">' +
                            '<div><dt>Booking No.</dt>' +
                                '<dd>' + ( link
                                    ? '<a href="' + link + '" target="_blank" rel="noopener noreferrer">#' + escapeHtml( order.id ) + '</a>'
                                    : '#' + escapeHtml( order.id ) ) + '</dd></div>' +
                            '<div><dt>Created</dt>' +
                                '<dd>' + escapeHtml( order.order_date ) + '</dd></div>' +
                        '</dl>' +
                        '<div class="wphb-cm-total">' +
                            '<span>Total</span>' +
                            '<strong>' + escapeHtml( decodeEntities( order.total ) ) + '</strong>' +
                        '</div>' +
                    '</div>' +
                    ( link
                        ? '<div class="wphb-cm-modal__footer">' +
                            '<a class="button button-primary" href="' + link + '" target="_blank" rel="noopener noreferrer">View booking</a>' +
                          '</div>'
                        : '' ) +
                '</div>' +
            '</div>';

        document.body.insertAdjacentHTML( 'beforeend', html );
        document.body.classList.add( 'wphb-cm-modal-open' );

        const modal = document.getElementById( 'wphb-cm-modal' );
        modal.addEventListener( 'click', ( e ) => {
            if ( e.target === modal || e.target.closest( '.wphb-cm-modal__close' ) ) {
                closeModal();
            }
        } );
        document.addEventListener( 'keydown', onKeydown );
        modal.querySelector( '.wphb-cm-modal__close' ).focus();
    };

    /* ---------- Calendar ---------- */
    const calendarBooking = new FullCalendar.Calendar( eleBooking, {
        height: 'auto',
        headerToolbar: {
            left: 'title',
            center: '',
            right: 'today prev,next dayGridMonth,listMonth',
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            list: 'List',
        },
        noEventsContent: 'No bookings in this period.',
        dayMaxEvents: 3,
        eventDisplay: 'block',
        displayEventTime: false,
        events: ( info, successCallback, failureCallback ) => {
            getBookings( info ).then( successCallback ).catch( failureCallback );
        },
        loading: ( isLoading ) => {
            eleBooking.classList.toggle( 'is-loading', isLoading );
        },
        eventDidMount: ( info ) => {
            const order = info.event.extendedProps?.data_order || {};
            const parts = [
                info.event.title,
                ( order.start_date_popup || '' ) + ' → ' + ( order.end_date_popup || '' ),
                statusLabels[ getStatus( info.event ) ] || '',
            ];
            info.el.setAttribute( 'title', parts.filter( Boolean ).join( '\n' ) );
        },
        eventClick: ( info ) => {
            info.jsEvent.preventDefault();
            openModal( info.event );
        },
    } );
    calendarBooking.render();
}

/**
 * It adds an event listener to the checkbox with the id of `_hb_room_preview` and when the checkbox is
 * checked, it removes the class `hidden` from the element with the class `room_preview_url`
 */
const RoomPreview = () => {

    const enablePreview = document.getElementById('_hb_room_preview');
    if( enablePreview == null ) return;

    enablePreview.addEventListener('change', function (e) {
        const contentPreview = document.querySelector('.room_preview_url');
        if ( contentPreview == null ) return;
        if ( enablePreview.checked ) {
            contentPreview.classList.remove('hidden');
        } else{
            contentPreview.classList.add('hidden');
        }
    });
}

const adminUpdateField = () => {
    const btn = document.querySelector('._wphb_update_field');
    if ( btn == null ) return;

    const submit = async () => {
		try {
			const response = await wp.apiFetch( {
				path: 'wphb/v1/admin/rooms/update-field',
				method: 'POST',
			} );

			const {status , message } = response;
			if ( 'success' === status ) {
                alert( message );
                window.location.reload(true);
			} else {
                throw new Error( message );
            }
		} catch ( error ) {
			alert( error.message && error.message );
		}
    }
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        btn.classList.add('wphb_loading');
        submit();
    });
}

const toggleDeposit = () => {
    const ele = document.querySelector('#deposit_room');
    if ( ele == null ) return;

    const checked = ele.querySelector('#_hb_enable_deposit');
    if ( checked.checked ) {
        ele.classList.remove('blocked');
    }
    checked.addEventListener('change',function(e) {
        if ( checked.checked ) {
            ele.classList.remove('blocked');
        } else{
            ele.classList.add('blocked');
        }
    });
}

document.addEventListener( 'DOMContentLoaded', () => {
    MetaboxRoomTabs();
    viewAllPlanSingleRoom();
    CalendarManager();
    RoomPreview();
    adminUpdateField();
    toggleDeposit();
} );
