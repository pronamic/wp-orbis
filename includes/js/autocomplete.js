jQuery( document ).ready( function( $ ) {
	var formatNoMatches = function() { return orbisl10n.noMatches; };

	var formatInputTooShort = function( input, min ) { 
		return orbisl10n.inputTooShort.replace( '{todo}', ( min - input.length ) ); 
	};

	var formatSelectionTooBig = function( limit ) {
		if ( limit == 1 ) {
			return orbisl10n.selectionTooBigSingle.replace( '{limit}', limit ); 
		} else {
			return orbisl10n.selectionTooBigPlural.replace( '{limit}', limit ); 
		}
	};

	var formatLoadMore = function( pageNumber ) { return orbisl10n.loadMore; };

	var formatSearching = function() { return orbisl10n.searching; };

	var wpAction = function( item ) {
		if ( item.hasClass( 'orbis_company_id_field' ) || item.hasClass( 'orbis-company-id-control' ) ) {
			return 'company_id_suggest';
		}
		
		if ( item.hasClass( 'orbis-project-id-control' ) ) {
			return 'project_id_suggest';
		}
		
		if ( item.hasClass( 'orbis-subscription-id-control' ) ) {
			return 'subscription_id_suggest';
		}
		
		if ( item.hasClass( 'orbis-person-id-control' ) ) {
			return 'person_id_suggest';
		}
	};

	$( '.select2' ).select2();

	$( '.orbis-id-control' ).select2( {
        minimumInputLength: 2,
        initSelection: function ( element, callback ) {
            callback( { id: element.val(), text: element.data( 'text' ) } );
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: orbis.ajaxUrl,
            dataType: 'json',
            data: function( term, page ) {
                return {
                	action: wpAction( this ),
					term: term
                };
            },
            results: function ( data, page ) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return { results: data };
            }
        },
        allowClear: true,
        formatNoMatches: formatNoMatches,
        formatInputTooShort: formatInputTooShort,
        formatSelectionTooBig: formatSelectionTooBig,
        formatLoadMore: formatLoadMore,
        formatSearching: formatSearching
	} );
} );