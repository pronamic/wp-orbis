jQuery( document ).ready( function( $ ) {
	$( '.orbis-datepicker' ).datepicker( {
		numberOfMonths: 3,
		showButtonPanel: true,
	} );
	
	$( '.orbis-confirm' ).click( function() {
		var result = confirm( 'Are you sure?' );

		return result;
	} );
} );