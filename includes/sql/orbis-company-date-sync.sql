UPDATE
	wp_posts AS post
		LEFT JOIN
	orbis_companies AS company
			ON company.post_id = post.ID
SET
	post.post_date = FROM_UNIXTIME(company.added_on_date) , 
	post.post_date_gmt = CONVERT_TZ(FROM_UNIXTIME(company.added_on_date), 'Europe/Amsterdam', 'GMT')
WHERE
	post.post_date = '1970-01-01 00:00:00'
		AND
	company.added_on_date != 0
		AND
	post_type = 'orbis_company'
;