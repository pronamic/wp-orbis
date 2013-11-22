INSERT
	INTO	
		wp_postmeta ( post_id, meta_key, meta_value )
	SELECT
		post.ID AS post_id,
		'_orbis_company_ebilling' AS meta_key,
		'1' AS meta_value
	FROM
		wp_posts AS post
			LEFT JOIN
		wp_postmeta AS meta
				ON post.ID = meta.post_id
	WHERE
		post_type = 'orbis_company'
			AND
		ID NOT IN (
			SELECT post_id FROM wp_postmeta WHERE meta_key = '_orbis_company_ebilling'
		)
	GROUP BY
		post.ID
	;