SELECT
	s.id,
	s.type_id,
	st.name,
	st.price,
	s.name,
	s.activation_date,
	DAYOFYEAR( s.activation_date ) AS activation_dayofyear,
	si.invoice_number,
	si.start_date
FROM
	orbis_subscriptions AS s
		LEFT JOIN
	orbis_subscription_types AS st
			ON s.type_id = st.id
		LEFT JOIN
	orbis_subscriptions_invoices AS si
			ON
				s.id = si.subscription_id
					AND
				YEAR( si.start_date ) = YEAR( NOW() )
WHERE 
	cancel_date IS NULL
		AND
	MONTH( s.activation_date ) < ( MONTH( NOW() ) + 2 )
AND 
	s.type_id NOT IN ( 11, 12 )
;