SELECT
	d.id AS did,
	d.domain_name,
	d.cancel_date AS cancel_date_1,
	s.id AS sid,
	s.name,
	s.cancel_date AS cancel_date_2
FROM
	orbis_domain_names AS d
		LEFT JOIN
	orbis_subscriptions AS s
			ON 
				d.domain_name = s.name
					AND
				d.price_id_delete = 1
					AND
				s.type_id = 25
;

UPDATE
	orbis_domain_names AS d
		LEFT JOIN
	orbis_subscriptions AS s
			ON 
				d.domain_name = s.name
					AND
				d.price_id_delete = 1
					AND
				s.type_id = 25
SET
	s.cancel_date = d.cancel_date
;