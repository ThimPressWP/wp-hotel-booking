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
