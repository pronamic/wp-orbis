<?php

// Load colours
$base      = '#007CD2';
$base_text = '#FFFFFF';

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline. !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
$wrapper = '
	width:100%;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 70px 0 70px 0;
';
$template_container = '
	-webkit-box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
';
$template_header = '
	background-color: ' . esc_attr( $base ) . ';
	-webkit-border-top-left-radius:6px !important;
	-webkit-border-top-right-radius:6px !important;
	border-top-left-radius:6px !important;
	border-top-right-radius:6px !important;
	border-bottom: 0;
	font-family:Arial;
	font-weight:bold;
	line-height:100%;
	vertical-align:middle;
';
$body_content = '
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
';
$body_content_inner = '
	font-family:Arial;
	font-size:14px;
	line-height:150%;
	text-align:left;
';
$header_content_h1 = '
	color: ' . esc_attr( $base_text ) . ';
	margin:0;
	padding: 28px 24px;
	display:block;
	font-family:Arial;
	font-size:30px;
	font-weight:bold;
	text-align:left;
	line-height: 150%;
';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php orbis_the_email_title(); ?></title>
	</head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<div style="<?php echo esc_attr( $wrapper ); ?>">
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            	<tr>
                	<td align="center" valign="top">

                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="<?php echo esc_attr( $template_container ); ?>">
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Header -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style="<?php echo esc_attr( $template_header ); ?>" bgcolor="<?php echo esc_attr( $base ); ?>">
                                        <tr>
                                            <td>
                                            	<h1 style="<?php echo esc_attr( $header_content_h1 ); ?>"><?php orbis_the_email_title(); ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Body -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                    	<tr>
                                            <td valign="top" style="<?php echo esc_attr( $body_content ); ?>">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top">
                                                            <div style="<?php echo esc_attr( $body_content_inner ); ?>">
