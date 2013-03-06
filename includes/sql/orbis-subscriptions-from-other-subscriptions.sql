INSERT INTO orbis_subscriptions ( company_id, type_id, name, activation_date, expiration_date )
SELECT
    company_id,
    '24' AS type_id,
    name,
    NOW(),
    NOW() + INTERVAL 1 YEAR
FROM
    orbis_subscriptions
WHERE
    company_id = 214
        AND
    type_id = 4
        AND
    id NOT IN ( 149, 316, 371, 378, 401, 402 )
;