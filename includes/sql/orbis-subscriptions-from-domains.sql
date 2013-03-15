INSERT INTO orbis_subscriptions ( company_id, type_id, name, activation_date, expiration_date, cancel_date )
SELECT
    client_id,
    '39' AS type_id,
    domain_name,
    order_date,
    order_date + INTERVAL 1 YEAR,
    cancel_date
FROM
    orbis_domain_names
WHERE
    price_id = 16
    	AND
    order_date IS NOT NULL
;

UPDATE
	orbis_domain_names
SET
	price_id_delete = price_id
WHERE
	price_id = 16
;

UPDATE
	orbis_domain_names
SET
	price_id = NULL
WHERE
	price_id_delete = 16
;