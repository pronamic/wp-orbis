'use strict';

angular.module( 'orbisFilters', [] ).filter( 'orbis_time', function() {
	return function( seconds ) {
		if ( angular.isNumber( seconds ) ) {
			var hours   = Math.floor( seconds / 3600 );
			var minutes = Math.floor( ( seconds - ( hours * 3600 ) ) / 60 );
			seconds = Math.floor( seconds % 60 );

			return '' + hours + ':' + ( '00' + minutes ).substr( -2, 2 );
		}

		return seconds;
	};
} );

var orbisApp = angular.module( 'orbisApp', [ 'orbisFilters', 'ui.date' ] );
