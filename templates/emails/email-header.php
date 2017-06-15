<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:27:20
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 16:55:06
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <style type="text/css">
        h2.section-title {
            color: #7AABF1;
            margin: 15px 0;
            font-size: 18px;
        }

        body, table.body {
            margin: 0 auto;
        }

        body * {
            font-size: 13px;
            color: #666;
            font-weight: 400;
            vertical-align: top
        }

        table.body {
            max-width: 500px;
            margin: 0 auto;
            background-color: #f6f6f6
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 5px 0;
            font-weight: 600
        }

        h1 {
            text-align: center;
            font-size: 25px;
        }

        h2 {
            font-size: 15px;
        }

        table.container.text-center h1 {
            padding: 20px 0;
            background-color: #7AABF1;
            color: #fff;
            margin: -10px -10px 10px -10px;
            display: inline-block;
            width: 100%;
        }

        table.container.text-center > tbody > tr > td {
            padding: 10px;
        }

        strong {
            font-weight: 600
        }

        .desc {
            text-align: center;
            font-size: 13px;
            padding: 10px 0;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        table th {
            font-weight: 600;
            text-align: left;
        }

        table.container.text-center * {
            color: #666;
        }

        table.row table {
            margin: 10px 0;
            vertical-align: top
        }

        table th {
            font-weight: 600;
            font-size: 15px;
        }

        table td {
            font-weight: 400;
            font-size: 13px;
        }

        table h1 {
            text-align: center
        }

        .booking_details th, .booking_details td {
            border: 2px solid #eeeeee;
            padding: 12px;
        }

        p {
            margin: 0 0 10px 0;
        }
    </style>
</head>
<body>
<table class="body" data-made-with-foundation="" cellspacing="0" cellpadding="0">
    <tr>
        <td class="center" align="center" valign="top">
            <table class="container text-center" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <h1><?php echo esc_html( $email_heading ) ?></h1>
                        <p class="desc"><?php echo esc_html( $email_heading_desc ) ?></p>