INSERT INTO orbis_subscriptions ( company_id, type_id, name, activation_date, expiration_date, cancel_date )
SELECT
    client_id,
    '50' AS type_id,
    domain_name,
    order_date,
    order_date + INTERVAL 1 YEAR,
    cancel_date
FROM
    orbis_domain_names
WHERE
    package_id = 22
    	AND
    order_date IS NOT NULL
;

UPDATE
	orbis_domain_names
SET
	package_id_delete = package_id
WHERE
	package_id = 22
;

UPDATE
	orbis_domain_names
SET
	package_id = NULL
WHERE
	package_id_delete = 22
;