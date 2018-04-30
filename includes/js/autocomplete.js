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

		if ( item.hasClass( 'orbis-keychain-id-control' ) ) {
			return 'keychain_id_suggest';
		}
	};

	$( '.select2' ).select2( {
		width: '100%',
		selectOnClose: true
	} );

	$( '.orbis-id-control' ).select2( {
		minimumInputLength: 2,
		allowClear: true,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: orbis.ajaxUrl,
			dataType: 'json',
			data: function( params ) {
				return {
					action: wpAction( this ),
					term: params.term
				};
			},
			processResults: function ( data ) { // parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return { results: data };
			}
		},
		formatNoMatches: formatNoMatches,
		formatInputTooShort: formatInputTooShort,
		formatSelectionTooBig: formatSelectionTooBig,
		formatLoadMore: formatLoadMore,
		formatSearching: formatSearching,
		width: '100%',
		selectOnClose: true
	} );

	var keychainURL = window.location.origin + "/wp-json/wp/v2/orbis/keychains/select2";

	$( '.orbis-keychain-rest' ).select2( {
		minimumInputLength: 2,
		allowClear: true,
		ajax: {
			url: keychainURL,
			dataType: 'json',
			data: function( params ) {
				return {
					search: params.term
				}
			},
			processResults: function( data ) {
				return { results: data };
			},
			width: '100%',
			selectOnClose: true,
			formatNoMatches: formatNoMatches,
			formatInputTooShort: formatInputTooShort,
			formatSelectionTooBig: formatSelectionTooBig,
			formatLoadMore: formatLoadMore,
			formatSearching: formatSearching
		},
	} );

	/**
	 * Auto open Select2 on keypress.
	 *
	 * @see https://github.com/select2/select2/issues/3279#issuecomment-366828094
	 * @see https://github.com/select2/select2/blob/4.0.6-rc.1/dist/js/select2.full.js#L5465-L5499
	 */
	$( '[data-select2-id]' ).each( function() {
		var select2 = $( this ).data( 'select2' );

		if ( ! select2 ) {
			return;
		}

		select2.on( 'keypress', function( e ) {
			if ( this.isOpen() ) {
				return;
			}

			if ( e.which < 48 || e.which > 90 ) {
				return;
			}

			this.dropdown.$search.val( e.key );

			this.open();
		} );
	} );
} );
