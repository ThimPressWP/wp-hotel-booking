export const FLATPICKR_RANGE_SEPARATOR = '-';

const FLATPICKR_BROWSER_LOCALE_ALIASES = {
	// flatpickr uses `vn` for Vietnamese instead of `vi`.
	vi: 'vn',
	'vi-vn': 'vn',
	vi_vn: 'vn',
};

/**
 * Normalize locale keys so matching works across `vi_VN`, `vi-vn`, `VI-vn`, etc.
 */
const normalizeLocaleKey = ( localeKey ) =>
	String( localeKey || '' )
		.trim()
		.replace( /_/g, '-' )
		.toLowerCase();

/**
 * Detect a single locale config object (has month/week labels) rather than a locale map.
 */
const isFlatpickrSingleLocaleConfig = ( localeConfig ) =>
	Boolean(
		localeConfig &&
			typeof localeConfig === 'object' &&
			( localeConfig.weekdays || localeConfig.months )
	);

/**
 * Detect a locale map object where keys are locale ids (`en`, `fr`, `vn`, ...).
 */
const isFlatpickrLocaleMap = ( localeMap ) =>
	Boolean(
		localeMap &&
			typeof localeMap === 'object' &&
			! isFlatpickrSingleLocaleConfig( localeMap ) &&
			Object.keys( localeMap ).length > 0
	);

/**
 * Unwrap locale modules that may be nested by bundler interop (`default.default`).
 */
const unwrapFlatpickrLocaleModule = ( flatpickrLocales = {} ) => {
	let localeSource = flatpickrLocales;
	let maxDepth = 5;

	// Support nested UMD/ESM interop wrappers such as `default.default`.
	while (
		maxDepth > 0 &&
		localeSource &&
		typeof localeSource === 'object' &&
		localeSource.default &&
		typeof localeSource.default === 'object'
	) {
		const rootKeys = Object.keys( localeSource ).filter(
			( key ) => key !== '__esModule'
		);
		const defaultLocaleSource = localeSource.default;
		const defaultLooksLikeLocaleSource =
			isFlatpickrLocaleMap( defaultLocaleSource ) ||
			isFlatpickrSingleLocaleConfig( defaultLocaleSource );
		const shouldUnwrapBySingleDefaultKey =
			rootKeys.length === 1 && rootKeys[ 0 ] === 'default';
		const shouldUnwrapByNonLocaleWrapper =
			! isFlatpickrLocaleMap( localeSource ) &&
			defaultLooksLikeLocaleSource;

		if ( ! shouldUnwrapBySingleDefaultKey && ! shouldUnwrapByNonLocaleWrapper ) {
			break;
		}

		localeSource = defaultLocaleSource;
		maxDepth--;
	}

	return localeSource;
};

/**
 * Return a normalized locale map regardless of module export shape.
 */
const extractFlatpickrLocalesMap = ( flatpickrLocales = {} ) => {
	let localeSource = unwrapFlatpickrLocaleModule( flatpickrLocales );

	if (
		localeSource &&
		typeof localeSource === 'object' &&
		localeSource.default &&
		typeof localeSource.default === 'object'
	) {
		const rootLooksLikeLocaleMap = isFlatpickrLocaleMap( localeSource );
		const defaultLooksLikeLocaleMap = isFlatpickrLocaleMap(
			localeSource.default
		);

		// Use default export only when the root object is a module wrapper.
		if ( ! rootLooksLikeLocaleMap && defaultLooksLikeLocaleMap ) {
			localeSource = localeSource.default;
		}
	}

	if ( isFlatpickrSingleLocaleConfig( localeSource ) ) {
		return {
			default: localeSource,
			en: localeSource,
		};
	}

	if ( isFlatpickrLocaleMap( localeSource ) ) {
		return localeSource;
	}

	return {};
};

/**
 * Preserve insertion order while removing duplicates.
 */
const uniq = ( values ) =>
	values.filter( ( value, index ) => values.indexOf( value ) === index );

/**
 * Convert and validate first day of week from admin settings (0..6).
 */
export const getFlatpickrFirstDayOfWeekFromObject = (
	firstDayOfWeek,
	fallback = 1
) => {
	const parsed = Number.parseInt( firstDayOfWeek, 10 );
	if ( Number.isNaN( parsed ) || parsed < 0 || parsed > 6 ) {
		return fallback;
	}

	return parsed;
};

/**
 * Read browser locale preferences in priority order.
 */
export const getBrowserLocales = () => {
	if ( typeof navigator === 'undefined' ) {
		return [];
	}

	if ( Array.isArray( navigator.languages ) && navigator.languages.length ) {
		return navigator.languages;
	}

	if ( navigator.language ) {
		return [ navigator.language ];
	}

	return [];
};

/**
 * Build locale lookup candidates from browser locales:
 * full locale -> alias -> base locale -> base alias -> `en`.
 */
export const buildLocaleCandidates = ( browserLocales = [] ) => {
	const localeList = Array.isArray( browserLocales )
		? browserLocales
		: [ browserLocales ];
	const candidates = [];

	localeList.forEach( ( localeItem ) => {
		const normalized = normalizeLocaleKey( localeItem );
		if ( ! normalized ) {
			return;
		}

		candidates.push( normalized );

		const alias = FLATPICKR_BROWSER_LOCALE_ALIASES[ normalized ];
		if ( alias ) {
			candidates.push( alias );
		}

		const baseLocale = normalized.split( '-' )[ 0 ];
		if ( baseLocale && baseLocale !== normalized ) {
			// Example: `fr-CA` -> `fr` fallback when region-specific key is missing.
			candidates.push( baseLocale );
			const baseAlias = FLATPICKR_BROWSER_LOCALE_ALIASES[ baseLocale ];
			if ( baseAlias ) {
				candidates.push( baseAlias );
			}
		}
	} );

	candidates.push( 'en' );

	return uniq( candidates );
};

/**
 * Resolve the best matching locale config from browser locales.
 */
export const resolveBrowserLocaleConfig = (
	flatpickrLocales = {},
	browserLocales = []
) => {
	const rawLocales = extractFlatpickrLocalesMap( flatpickrLocales );
	const localeConfigMap = {};

	Object.keys( rawLocales ).forEach( ( localeKey ) => {
		localeConfigMap[ normalizeLocaleKey( localeKey ) ] =
			rawLocales[ localeKey ];
	} );

	// Pick the first matched browser locale, then fallback to English.
	const candidates = buildLocaleCandidates( browserLocales );
	const matchedLocaleKey =
		candidates.find( ( candidate ) => localeConfigMap[ candidate ] ) || 'en';
	const matchedLocaleConfig =
		localeConfigMap[ matchedLocaleKey ] ||
		localeConfigMap.en ||
		rawLocales.en ||
		{};

	return {
		localeKey: matchedLocaleKey,
		localeConfig: {
			...matchedLocaleConfig,
		},
	};
};

/**
 * Create final flatpickr locale config merged with admin-controlled values.
 */
export const createFlatpickrLocaleConfig = (
	flatpickrLocales = {},
	firstDayOfWeek = 1
) => {
	const { localeConfig } = resolveBrowserLocaleConfig(
		flatpickrLocales,
		getBrowserLocales()
	);
	const parsedFirstDayOfWeek = getFlatpickrFirstDayOfWeekFromObject(
		firstDayOfWeek,
		1
	);

	return {
		...localeConfig,
		firstDayOfWeek: parsedFirstDayOfWeek,
		rangeSeparator: FLATPICKR_RANGE_SEPARATOR,
	};
};

/**
 * Apply locale config globally and ensure current defaults keep week start/separator.
 */
export const applyFlatpickrLocaleConfig = (
	flatpickrInstance,
	localeConfig = {}
) => {
	if ( ! flatpickrInstance || ! localeConfig ) {
		return;
	}

	flatpickrInstance.localize( localeConfig );
	if ( flatpickrInstance.l10ns && flatpickrInstance.l10ns.default ) {
		flatpickrInstance.l10ns.default.firstDayOfWeek =
			localeConfig.firstDayOfWeek;
		flatpickrInstance.l10ns.default.rangeSeparator =
			localeConfig.rangeSeparator;
	}
};

/**
 * Strictly parse date by format and verify by format round-trip.
 */
export const parseDateStrict = ( flatpickrInstance, value, format ) => {
	if ( ! flatpickrInstance || ! value || ! format ) {
		return null;
	}

	const parsed = flatpickrInstance.parseDate( value, format, true );
	if ( ! parsed ) {
		return null;
	}

	// Round-trip validation prevents ambiguous dates like 03/04/2026 from being misread.
	return flatpickrInstance.formatDate( parsed, format ) === value
		? parsed
		: null;
};

/**
 * Strictly parse supported booking date formats without depending on flatpickr runtime.
 */
export const parseDateStringStrict = ( value, format ) => {
	const dateValue = String( value || '' ).trim();
	const dateFormat = String( format || '' ).trim();

	if ( ! dateValue || ! dateFormat ) {
		return null;
	}

	let regexPattern = '';
	const escapedRegexChar = /[.*+?^${}()|[\]\\]/g;

	for ( let i = 0; i < dateFormat.length; i++ ) {
		const token = dateFormat[ i ];

		if ( token === 'Y' ) {
			regexPattern += '(?<year>\\d{4})';
		} else if ( token === 'm' ) {
			regexPattern += '(?<month>\\d{1,2})';
		} else if ( token === 'd' ) {
			regexPattern += '(?<day>\\d{1,2})';
		} else {
			regexPattern += token.replace( escapedRegexChar, '\\$&' );
		}
	}

	const matched = dateValue.match( new RegExp( `^${ regexPattern }$` ) );
	if ( ! matched || ! matched.groups ) {
		return null;
	}

	const year = Number.parseInt( matched.groups.year || '', 10 );
	const month = Number.parseInt( matched.groups.month || '', 10 );
	const day = Number.parseInt( matched.groups.day || '', 10 );
	if ( ! year || ! month || ! day ) {
		return null;
	}

	const parsed = new Date( year, month - 1, day );
	if (
		parsed.getFullYear() !== year ||
		parsed.getMonth() !== month - 1 ||
		parsed.getDate() !== day
	) {
		return null;
	}

	return parsed;
};

/**
 * Normalize a raw booking date string into internal submit format.
 */
export const normalizeBookingDateInputValue = (
	value,
	frontendDateFormat,
	internalDateFormat = 'Y/m/d'
) => {
	const dateValue = String( value || '' ).trim();
	if ( ! dateValue ) {
		return '';
	}

	const formats = [ frontendDateFormat, internalDateFormat ].filter(
		( format, index, arr ) => format && arr.indexOf( format ) === index
	);

	for ( let i = 0; i < formats.length; i++ ) {
		const parsed = parseDateStringStrict( dateValue, formats[ i ] );
		if ( parsed ) {
			const year = parsed.getFullYear();
			const month = String( parsed.getMonth() + 1 ).padStart( 2, '0' );
			const day = String( parsed.getDate() ).padStart( 2, '0' );

			return `${ year }/${ month }/${ day }`;
		}
	}

	return '';
};

const getBookingDateFieldSelector = ( selector, fallback ) =>
	String( selector || fallback || '' ).trim() || fallback;

/**
 * Read check-in/check-out inputs from a form or any container node.
 */
export const getBookingDateFields = (
	container,
	{
		checkInSelector = 'input[name="check_in_date"]',
		checkOutSelector = 'input[name="check_out_date"]',
	} = {}
) => {
	if ( ! container || typeof container.querySelector !== 'function' ) {
		return {
			checkInField: null,
			checkOutField: null,
		};
	}

	return {
		checkInField: container.querySelector(
			getBookingDateFieldSelector(
				checkInSelector,
				'input[name="check_in_date"]'
			)
		),
		checkOutField: container.querySelector(
			getBookingDateFieldSelector(
				checkOutSelector,
				'input[name="check_out_date"]'
			)
		),
	};
};

/**
 * Normalize booking inputs into canonical submit format and write back to the fields.
 */
export const normalizeBookingDateFieldsForSubmit = (
	container,
	frontendDateFormat,
	internalDateFormat = 'Y/m/d',
	selectors = {},
	{
		writeBack = true,
	} = {}
) => {
	const { checkInField, checkOutField } = getBookingDateFields(
		container,
		selectors
	);
	const checkInDate = normalizeBookingDateInputValue(
		checkInField?.value,
		frontendDateFormat,
		internalDateFormat
	);
	const checkOutDate = normalizeBookingDateInputValue(
		checkOutField?.value,
		frontendDateFormat,
		internalDateFormat
	);

	if ( writeBack && checkInField && checkInDate ) {
		checkInField.value = checkInDate;
	}

	if ( writeBack && checkOutField && checkOutDate ) {
		checkOutField.value = checkOutDate;
	}

	return {
		checkInField,
		checkOutField,
		checkInDate,
		checkOutDate,
	};
};

/**
 * Build parse/format helpers for booking fields and submit payloads.
 */
export const createBookingDateHelpers = (
	flatpickrInstance,
	frontendDateFormat,
	internalDateFormat
) => {
	const parseBookingDateValue = ( value ) => {
		const dateValue = String( value || '' ).trim();
		if ( ! dateValue ) {
			return null;
		}

		const formats = [ frontendDateFormat, internalDateFormat ].filter(
			( format, index, arr ) => format && arr.indexOf( format ) === index
		);

		for ( let i = 0; i < formats.length; i++ ) {
			const parsed = parseDateStrict(
				flatpickrInstance,
				dateValue,
				formats[ i ]
			);
			if ( parsed ) {
				return parsed;
			}
		}

		return null;
	};

	// Reuse strict parser first, then fallback to flatpickr parser for UI operations.
	const parseFlatpickrDate = ( value, format ) => {
		if ( value instanceof Date ) {
			return value;
		}

		const parsed = parseBookingDateValue( value );
		if ( parsed ) {
			return parsed;
		}

		return flatpickrInstance.parseDate(
			value,
			format || frontendDateFormat,
			true
		);
	};

	const formatBookingDate = ( date, format = frontendDateFormat ) =>
		flatpickrInstance.formatDate( date, format );
	const formatBookingDateForUi = ( date ) =>
		flatpickrInstance.formatDate( date, frontendDateFormat );
	const formatBookingDateForSubmit = ( date ) =>
		flatpickrInstance.formatDate( date, internalDateFormat );

	return {
		parseBookingDateValue,
		parseFlatpickrDate,
		formatBookingDate,
		formatBookingDateForUi,
		formatBookingDateForSubmit,
	};
};

/**
 * Normalize prefilled booking inputs into the active UI format before flatpickr binds.
 */
export const syncBookingDateFieldsForUi = (
	{
		checkInField = null,
		checkOutField = null,
		rangeField = null,
		rangeSeparator = FLATPICKR_RANGE_SEPARATOR,
	},
	{
		parseBookingDateValue,
		formatBookingDateForUi,
	}
) => {
	const parsedCheckInDate = parseBookingDateValue?.( checkInField?.value );
	const parsedCheckOutDate = parseBookingDateValue?.( checkOutField?.value );

	if ( parsedCheckInDate && checkInField ) {
		checkInField.value = formatBookingDateForUi( parsedCheckInDate );
	}

	if ( parsedCheckOutDate && checkOutField ) {
		checkOutField.value = formatBookingDateForUi( parsedCheckOutDate );
	}

	if ( rangeField && parsedCheckInDate && parsedCheckOutDate ) {
		rangeField.value = formatDateRangeValue(
			formatBookingDateForUi( parsedCheckInDate ),
			formatBookingDateForUi( parsedCheckOutDate ),
			rangeSeparator
		);
	}

	return {
		parsedCheckInDate,
		parsedCheckOutDate,
	};
};

/**
 * Validate booking dates with strict parsing and optionally rewrite inputs for submit.
 */
export const validateBookingDateRange = (
	container,
	{
		parseBookingDateValue,
		formatBookingDateForSubmit,
		checkInSelector = 'input[name="check_in_date"]',
		checkOutSelector = 'input[name="check_out_date"]',
		emptyCheckInMessage = 'Please select check in date.',
		emptyCheckOutMessage = 'Please select check out date.',
		invalidRangeMessage = 'Check out date must be greater than the check in.',
		toggleErrorClass = false,
		normalizeFieldValues = false,
	} = {}
) => {
	const { checkInField, checkOutField } = getBookingDateFields( container, {
		checkInSelector,
		checkOutSelector,
	} );

	if ( ! checkInField || ! checkOutField ) {
		return {
			ok: false,
			error: invalidRangeMessage,
			checkInField,
			checkOutField,
			checkInDate: '',
			checkOutDate: '',
		};
	}

	if ( toggleErrorClass ) {
		checkInField.classList.remove( 'error' );
		checkOutField.classList.remove( 'error' );
	}

	const checkInDateObject = parseBookingDateValue?.( checkInField.value );
	if ( ! checkInDateObject ) {
		if ( toggleErrorClass ) {
			checkInField.classList.add( 'error' );
		}

		return {
			ok: false,
			error: emptyCheckInMessage,
			checkInField,
			checkOutField,
			checkInDate: '',
			checkOutDate: '',
		};
	}

	const checkOutDateObject = parseBookingDateValue?.( checkOutField.value );
	if ( ! checkOutDateObject ) {
		if ( toggleErrorClass ) {
			checkOutField.classList.add( 'error' );
		}

		return {
			ok: false,
			error: emptyCheckOutMessage,
			checkInField,
			checkOutField,
			checkInDate: '',
			checkOutDate: '',
		};
	}

	const checkInDate = formatBookingDateForSubmit?.( checkInDateObject ) || '';
	const checkOutDate =
		formatBookingDateForSubmit?.( checkOutDateObject ) || '';

	if ( ! checkInDate || ! checkOutDate || checkOutDate <= checkInDate ) {
		if ( toggleErrorClass ) {
			checkInField.classList.add( 'error' );
			checkOutField.classList.add( 'error' );
		}

		return {
			ok: false,
			error: invalidRangeMessage,
			checkInField,
			checkOutField,
			checkInDate,
			checkOutDate,
		};
	}

	if ( normalizeFieldValues ) {
		checkInField.value = checkInDate;
		checkOutField.value = checkOutDate;
	}

	return {
		ok: true,
		error: '',
		checkInField,
		checkOutField,
		checkInDate,
		checkOutDate,
	};
};

/**
 * Build a standard flatpickr config so all pickers share parser, locale, and defaults.
 */
export const createFlatpickrConfig = (
	{
		dateFormat,
		parseDate,
		locale,
		disableMobile = true,
	} = {},
	overrides = {}
) => ( {
		dateFormat,
		parseDate,
		disableMobile,
		locale,
		...overrides,
	} );

/**
 * Join two date values into one range string using configured separator.
 */
export const formatDateRangeValue = (
	startDateValue = '',
	endDateValue = '',
	rangeSeparator = FLATPICKR_RANGE_SEPARATOR
) => `${ String( startDateValue || '' ) }${ String( rangeSeparator || FLATPICKR_RANGE_SEPARATOR ) }${ String( endDateValue || '' ) }`;

/**
 * Split a range string into date parts while handling regex-special separators safely.
 */
export const splitDateRangeValue = (
	dateRangeValue = '',
	rangeSeparator = FLATPICKR_RANGE_SEPARATOR
) => {
	const rawValue = String( dateRangeValue || '' ).trim();
	if ( ! rawValue ) {
		return [];
	}

	// Escape separators like `-`, `.`, `|` before building the split regex.
	const escapedSeparator = String( rangeSeparator || FLATPICKR_RANGE_SEPARATOR )
		.replace( /[-/\\^$*+?.()|[\]{}]/g, '\\$&' );
	const splitBySeparatorRegex = new RegExp( `\\s*${ escapedSeparator }\\s*` );

	return rawValue
		.split( splitBySeparatorRegex )
		.map( ( value ) => value.trim() )
		.filter( Boolean );
};

/**
 * Convert Unix timestamps (seconds) into calendar-only Date objects for flatpickr disable lists.
 */
export const mapBookingTimestampsToDates = ( timestamps = [] ) =>
	( Array.isArray( timestamps ) ? timestamps : [] )
		.map( ( timestamp ) => {
			const parsedTimestamp = Number.parseInt( timestamp, 10 );
			if ( Number.isNaN( parsedTimestamp ) ) {
				return null;
			}

			const date = new Date( parsedTimestamp * 1000 );
			return new Date(
				date.getFullYear(),
				date.getMonth(),
				date.getDate()
			);
		} )
		.filter( Boolean );

/**
 * Normalize min-stay settings so `0` or invalid values fall back to one night.
 */
export const getMinBookingDays = ( minBookingDate, fallback = 1 ) => {
	const parsedValue = Number.parseInt( minBookingDate, 10 );
	if ( Number.isNaN( parsedValue ) || parsedValue <= 0 ) {
		return fallback;
	}

	return parsedValue;
};

/**
 * Compute the earliest allowed end date for a selected start date.
 */
export const getMinEndDateFromStartDate = ( startDate, minBookingDays = 1 ) => {
	if ( ! ( startDate instanceof Date ) || Number.isNaN( startDate.getTime() ) ) {
		return null;
	}

	const minEndDate = new Date( startDate );
	minEndDate.setDate( minEndDate.getDate() + getMinBookingDays( minBookingDays ) );

	return minEndDate;
};

/**
 * Disable calendar cells that fall before the current minimum end date.
 */
export const applyMinEndDateToDayElement = ( dayElem, minEndDate ) => {
	if (
		! dayElem ||
		! dayElem.dateObj ||
		! minEndDate ||
		! ( minEndDate instanceof Date ) ||
		Number.isNaN( minEndDate.getTime() )
	) {
		return;
	}

	if ( dayElem.dateObj < minEndDate ) {
		dayElem.classList.add( 'flatpickr-disabled' );
		dayElem.setAttribute( 'aria-disabled', 'true' );
	}
};

/**
 * Create an `onDayCreate` handler that reads min-end-date state from a callback.
 */
export const createMinEndDateDayCreateHandler = ( getMinEndDate ) =>
	( dObj, dStr, fpInstance, dayElem ) => {
		applyMinEndDateToDayElement( dayElem, getMinEndDate?.() || null );
	};
