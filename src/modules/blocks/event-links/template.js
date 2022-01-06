/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import AutosizeInput from 'react-input-autosize';

/**
 * WordPress dependencies
 */
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Link as LinkIcon } from '@moderntribe/events/icons';
import { wpEditor } from '@moderntribe/common/utils/globals';
import './style.pcss';
const { InspectorControls } = wpEditor;

/**
 * Module Code
 */

const googleCalendarPlaceholder = __( 'Add to Google Calendar', 'the-events-calendar' );
const iCalExportPlaceholder = __( 'Add to iCalendar', 'the-events-calendar' );
const icsExportPlaceholder = __( 'Export via ics', 'the-events-calendar' );

const renderPlaceholder = ( label ) => (
	<button className="tribe-editor__btn--link tribe-editor__btn--placeholder" disabled>
		<LinkIcon />
		{ label }
	</button>
);

const renderGoogleCalendar = ( {
	attributes,
	setGoogleCalendarLabel,
} ) => {
	const { hasIcs, hasiCal, hasGoogleCalendar, googleCalendarLabel } = attributes;

	if ( ! hasGoogleCalendar && ! hasiCal&& ! hasIcs ) {
		return renderPlaceholder( googleCalendarPlaceholder );
	}

	return hasGoogleCalendar && (
		<div className="tribe-editor__btn--link tribe-events-gcal">
			<LinkIcon />
			<AutosizeInput
				name="google-calendar-label"
				className="tribe-editor__btn-input"
				value={ googleCalendarLabel }
				placeholder={ googleCalendarPlaceholder }
				onChange={ setGoogleCalendarLabel }
			/>
		</div>
	);
};

const renderiCal = ( {
	attributes,
	setiCalLabel,
} ) => {
	const { hasIcs, hasiCal, hasGoogleCalendar, iCalLabel } = attributes;

	if ( ! hasGoogleCalendar && ! hasiCal && ! hasIcs ) {
		return renderPlaceholder( iCalExportPlaceholder );
	}

	return hasiCal && (
		<div className="tribe-editor__btn--link tribe-events-ical">
			<LinkIcon />
			<AutosizeInput
				id="tribe-event-ical"
				name="tribe-event-ical"
				className="tribe-editor__btn-input"
				value={ iCalLabel }
				placeholder={ iCalExportPlaceholder }
				onChange={ setiCalLabel }
			/>
		</div>
	);
};

const renderIcs = ( {
	attributes,
	setIcsLabel,
} ) => {
	const { hasIcs, hasiCal, hasGoogleCalendar, icsLabel } = attributes;

	if ( ! hasGoogleCalendar && ! hasiCal && ! hasIcs ) {
		return renderPlaceholder( icsExportPlaceholder );
	}

	return hasIcs && (
		<div className="tribe-editor__btn--link tribe-events-ical">
			<LinkIcon />
			<AutosizeInput
				id="tribe-event-ical"
				name="tribe-event-ical"
				className="tribe-editor__btn-input"
				value={ icsLabel }
				placeholder={ icsExportPlaceholder }
				onChange={ setIcsLabel }
			/>
		</div>
	);
};

const renderButtons = ( props ) => (
	<div key="event-links" className="tribe-editor__block tribe-editor__events-link">
		{ renderGoogleCalendar( props ) }
		{ renderiCal( props ) }
		{ renderIcs( props ) }
	</div>
);

const renderControls = ( {
	attributes,
	isSelected,
	toggleIcalLabel,
	toggleIcsLabel,
	toggleGoogleCalendar,
} ) => {
	const { hasGoogleCalendar, hasiCal, hasIcs } = attributes;

	return (
		isSelected && (
			<InspectorControls key="inspector">
				<PanelBody title={ __( 'Share Settings', 'the-events-calendar' ) }>
					<ToggleControl
						label={ __( 'Google Calendar', 'the-events-calendar' ) }
						checked={ hasGoogleCalendar }
						onChange={ toggleGoogleCalendar }
					/>
					<ToggleControl
						label={ __( 'iCalendar', 'the-events-calendar' ) }
						checked={ hasiCal }
						onChange={ toggleIcalLabel }
					/>
					<ToggleControl
						label={ __( 'ics Export', 'the-events-calendar' ) }
						checked={ hasIcs }
						onChange={ toggleIcsLabel }
					/>
				</PanelBody>
			</InspectorControls>
		)
	);
};

const EventLinks = ( props ) => {
	const { setAttributes } = props;

	const setiCalLabel = e => setAttributes( { iCalLabel: e.target.value } );
	const setIcsLabel = e => setAttributes( { icsLabel: e.target.value } );
	const setGoogleCalendarLabel = e => setAttributes( { googleCalendarLabel: e.target.value } );
	const toggleIcalLabel = value => setAttributes( { hasiCal: value } );
	const toggleIcsLabel = value => setAttributes( { hasIcs: value } );
	const toggleGoogleCalendar = value => setAttributes( { hasGoogleCalendar: value } );

	const combinedProps = {
		...props,
		setiCalLabel,
		setIcsLabel,
		setGoogleCalendarLabel,
		toggleIcalLabel,
		toggleIcsLabel,
		toggleGoogleCalendar,
	};

	return [
		renderButtons( combinedProps ),
		renderControls( combinedProps ),
	];
};

EventLinks.propTypes = {
	hasGoogleCalendar: PropTypes.bool,
	hasiCal: PropTypes.bool,
	hasIcs: PropTypes.bool,
	isSelected: PropTypes.bool,
	googleCalendarLabel: PropTypes.string,
	setGoogleCalendarLabel: PropTypes.func,
	iCalLabel: PropTypes.string,
	setiCalLabel: PropTypes.func,
	icsLabel: PropTypes.string,
	setIcsLabel: PropTypes.func,
	toggleIcalLabel: PropTypes.func,
	toggleIcsLabel: PropTypes.func,
	toggleGoogleCalendar: PropTypes.func,
};

export default EventLinks;
