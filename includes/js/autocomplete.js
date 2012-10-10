jQuery(document).ready(function($) {   
	$( ".orbis_company_id_field" ).select2({
        placeholder: "Search for a company",
        minimumInputLength: 2,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: ajaxurl,
            dataType: 'json',
            data: function (term, page) {
                return {
                	action: "company_id_suggest",
					term: term
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return {results: data};
            }
        }
    });
});