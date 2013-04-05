SELECT
	st.id,
	st.name,
	st.price,
	COUNT(s.id) AS number,
	st.price * COUNT(s.id) AS total_price,
	( st.price * COUNT(s.id) ) / 75 AS hours_per_year,
	( ( st.price * COUNT(s.id) ) / 4 ) / 75 AS hours_per_quarter
FROM
	orbis_subscription_types AS st
		LEFT JOIN
	orbis_subscriptions AS s
			ON st.id = s.type_id
WHERE
	s.cancel_date IS NULL
GROUP BY
	st.id
;