SELECT
	domain_name,
	order_date,
	notes
FROM
	orbis_domain_names
WHERE
	MONTH(order_date) IN (1, 2, 3)
		AND
	cancel_date IS NULL
;