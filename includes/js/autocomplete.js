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

	if ( orbisl10n.theme ) {
		$.fn.select2.defaults.set( 'theme', orbisl10n.theme );
	}

	$( document ).on( 'select2:open', function() {
		document.querySelector( '.select2-container--open .select2-search__field' ).focus();
	} );

	$( '.select2' ).select2( {
		width: '100%',
		selectOnClose: true
	} );

	$( '.orbis-project-id-control' ).select2( {
		minimumInputLength: 2,
		allowClear: true,
		ajax: {
			url: orbis.restUrlProjects,
			dataType: 'json',
			data: function( params ) {
				return {
					_fields: 'id,select2_text',
					search: params.term
				};
			},
			processResults: function ( data ) {
				return {
					results: data.map( function( item ) {
						return {
							id: item.id,
							text: item.select2_text
						};
					} )
				};
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

	$( '.orbis-id-control' ).not( '.orbis-project-id-control' ).select2( {
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

	var url = window.location.origin + "/wp-json/wp/v2/";
	var exclude;

	$( '[data-post-suggest]' ).select2( {
		minimumInputLength: 2,
		allowClear: true,
		ajax: {
			url: function() {
				return url + $( this ).data( "post-suggest" )
			},
			dataType: 'json',
			data: function( params ) {
				exclude = $( this ).data( "post-suggest-exclude" );
				only_active = $( this ).data( "post-suggest-only-active" );
				return {
					only_active: only_active,
					search: params.term,
					exclude: exclude
				}
			},
			processResults: function( data ) {
				return {
					results: jQuery.map( data, function( obj ) {
						return { id: obj.id, text: decodeHtml( obj.title.rendered ) };
					} )
				}
			}
		},
		width: '100%',
		selectOnClose: true,
		formatNoMatches: formatNoMatches,
		formatInputTooShort: formatInputTooShort,
		formatSelectionTooBig: formatSelectionTooBig,
		formatLoadMore: formatLoadMore,
		formatSearching: formatSearching,
		placeholder: {
			id: "",
			placeholder: "Leave blank to ..."
		}
	} );

	function decodeHtml( html ) {
		var txt = document.createElement( "textarea" );
		txt.innerHTML = html;
		return txt.value;
	}

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

			this.open();
		} );
	} );
} );
