SELECT
	company.id AS company_id , 
	post.ID AS post_id , 
	post.post_date , 
	FROM_UNIXTIME(company.added_on_date) AS 'new_post_date' , 
	CONVERT_TZ(FROM_UNIXTIME(company.added_on_date), 'Europe/Amsterdam', 'GMT') AS 'new_post_date_gmt'
FROM
	wp_posts AS post
		LEFT JOIN
	orbis_companies AS company
			ON company.post_id = post.ID
WHERE
	post.post_date = '1970-01-01 00:00:00'
		AND
	company.added_on_date != 0
		AND
	post_type = 'orbis_company'
;