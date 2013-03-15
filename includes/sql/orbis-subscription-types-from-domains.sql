INSERT INTO orbis_subscription_types ( name, price, cost_price, notes, `default` )
SELECT
    name,
    price,
   	openprovider_price,
   	notes,
   	`default`
FROM
    orbis_domain_names_prices
;