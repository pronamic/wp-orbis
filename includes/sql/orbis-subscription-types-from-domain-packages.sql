INSERT INTO orbis_subscription_types ( name, price, notes, legacy_id )
SELECT
	name, price, notes, id
FROM
	orbis_hosting_packages
WHERE
	id IN (
		SELECT
			DISTINCT( package_id )
		FROM
			orbis_domain_names
		WHERE
			cancel_date IS NULL
	)
;