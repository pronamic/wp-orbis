CREATE TABLE IF NOT EXISTS orbis_subscriptions_invoices (
	id BIGINT(16) UNSIGNED NOT NULL AUTO_INCREMENT , 
	subscription_id BIGINT(16) UNSIGNED NOT NULL  , 
	invoice_number VARCHAR(8) NOT NULL ,
	start_date DATETIME , 
	end_date DATETIME ,

	PRIMARY KEY (id) 
)
ENGINE = InnoDB 
CHARSET = utf8 
COLLATE = utf8_unicode_ci;