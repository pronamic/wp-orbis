CREATE TEMPORARY TABLE orbis_subscriptions_persons AS (
	SELECT
		post.ID AS subscription_id,
		post.post_title AS subscription_title,
		subscription.name AS subscription_domain_name,
		MAX(IF(meta.meta_key = "_orbis_subscription_person_id", meta.meta_value, NULL)) AS person_id,
		subscription.cancel_date IS NULL AS active
	FROM
		orbis_subscriptions AS subscription
			LEFT JOIN
		wp_posts AS post
				ON subscription.post_id = post.ID
			LEFT JOIN
		wp_postmeta AS meta
				ON post.ID = meta.post_id
	WHERE
		subscription.type_id IN ( 4, 16 )
			AND
		post.post_type = "orbis_subscription"
	GROUP BY
		post.ID
);

SELECT
	sp.subscription_id,
	sp.subscription_title,
	sp.subscription_domain_name,
	sp.person_id AS person_id,
	post.post_title AS person_name,
	MAX(IF(meta.meta_key = "_orbis_person_email_address", meta.meta_value, NULL)) AS person_email,
	sp.active AS active
FROM
	orbis_subscriptions_persons AS sp
		LEFT JOIN
	wp_posts AS post
			ON post.ID = sp.person_id
		LEFT JOIN
	wp_postmeta AS meta
			ON post.ID = meta.post_id
GROUP BY
	post.ID
;


-- 
-- http://stackoverflow.com/questions/8058670/mysql-rows-to-columns-join-statement-problems
-- http://www.artfulsoftware.com/infotree/queries.php#77
-- 