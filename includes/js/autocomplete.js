jQuery(document).ready(function($) {
	$( ".orbis_company_id_field" ).each( function() {
		var $field = $( this );

		var $searchField = $( "<input />" );

		$searchField.autocomplete({
			source: function( request, response ) {
				var data = {
					action: "project_id_suggest",
					term: request.term
				};

				$.getJSON( ajaxurl, data, function( data ) {
					response( data );
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				$field.val( ui.item.value );
				
				return false;
			}
		});

		$searchField.insertAfter( $field );
	} );
});