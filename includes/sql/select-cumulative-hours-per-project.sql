SELECT 
	id,
	project_id,
	description,
	date,
	number_seconds,
	number_seconds_cumulative
FROM (
    SELECT
        id,
        project_id,
        description,
        date,
        number_seconds,
        @cs := IF(@prev_groupId = project_id, @cs+number_seconds, number_seconds) AS number_seconds_cumulative,
        @prev_groupId := project_id AS prev_groupId
    FROM
    	orbis_hours_registration, 
    	(SELECT @prev_groupId := 0, @cs := 0) AS vars
    WHERE 
    	project_id = 1
    ORDER BY 
    	project_id
) AS tmp;

-- 
-- http://stackoverflow.com/questions/839704/sum-until-certain-point-mysql
-- http://stackoverflow.com/questions/4713989/mysql-sum-until-value-is-reached
-- 
-- http://stackoverflow.com/questions/2563918/create-a-cumulative-sum-column-in-mysql
-- http://stackoverflow.com/questions/3144766/optimal-query-to-fetch-a-cumulative-sum-in-mysql
--
-- https://www.google.nl/search?q=mysql+create+progressive+sums&ie=utf-8&oe=utf-8&aq=t&rls=org.mozilla:nl:official&client=firefox-a&channel=fflb
-- 