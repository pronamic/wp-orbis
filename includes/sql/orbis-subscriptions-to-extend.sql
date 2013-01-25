SELECT
	*
FROM
	orbis_subscriptions
WHERE 
	expiration_date < NOW( ) + INTERVAL 1 WEEK
AND 
	type_id IN ( 11, 12 )
;