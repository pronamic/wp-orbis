INSERT
	INTO	
		wp_postmeta ( post_id, meta_key, meta_value )
	SELECT
		post.ID AS post_id,
		'_orbis_invoice_email' AS meta_key,
		meta_email.meta_value AS meta_value
	FROM
		wp_posts AS post
			LEFT JOIN
		wp_postmeta AS meta_email
				ON post.ID = meta_email.post_id AND meta_key = '_orbis_company_email'
	WHERE
		post_type = 'orbis_company'
			AND
		meta_email.meta_value IS NOT NULL
			AND
		ID IN (
			SELECT post_id FROM wp_postmeta WHERE meta_key = '_orbis_company_ebilling' AND meta_value = '1'
		)
			AND
		ID NOT IN (
			SELECT post_id FROM wp_postmeta WHERE meta_key = '_orbis_invoice_email'
		)
	GROUP BY
		post.ID
	;
