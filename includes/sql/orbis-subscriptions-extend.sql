UPDATE
	orbis_subscriptions AS subscription
SET
	subscription.expiration_date = subscription.expiration_date + INTERVAL 2 WEEK
WHERE 
	expiration_date < NOW( ) + INTERVAL 1 WEEK
AND 
	type_id IN ( 11, 12 )
;