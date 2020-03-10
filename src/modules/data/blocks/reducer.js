/**
 * External dependencies
 */
import { combineReducers } from 'redux';

/**
 * Internal dependencies
 */
import datetime from './datetime';
import organizers from './organizers';
import price from './price';
import website from './website';
import venue from './venue';
import classic from './classic';
import sharing from './sharing';

export const setInitialState = ( entityRecord ) => {

};

export default combineReducers( {
	datetime,
	classic,
	venue,
	organizers,
	price,
	website,
	sharing,
} );
