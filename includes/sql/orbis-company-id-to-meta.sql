INSERT 
	INTO wp_postmeta (post_id, meta_key, meta_value)
	SELECT 
		post_id AS post_id , 
		'_orbis_company_id' AS meta_key , 
		id AS meta_value
	FROM 
		orbis_companies
	WHERE 
		post_id IS NOT NULL
			AND
		post_id NOT IN (
			SELECT 
				post_id 
			FROM 
				wp_postmeta 
			WHERE 
				meta_key = '_orbis_company_id'
		)
;