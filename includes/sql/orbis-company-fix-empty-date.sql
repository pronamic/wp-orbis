UPDATE
	wp_posts AS post
		LEFT JOIN
	orbis_companies AS company
			ON company.post_id = post.ID
SET
	post.post_date = DATE_ADD('2012-01-01', INTERVAL company.id HOUR) , 
	post.post_date_gmt = DATE_ADD('2012-01-01', INTERVAL company.id HOUR) - INTERVAL 2 HOUR
WHERE
	post.post_date = '1970-01-01 00:00:00'
		AND
	company.added_on_date = 0
		AND
	post_type = 'orbis_company'
;