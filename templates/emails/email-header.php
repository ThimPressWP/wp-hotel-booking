<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-14 10:27:20
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-15 16:55:06
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
		body, table.body{ margin: 0 auto; }
		body *{ font-size: 13px; color: #666; font-weight: 400 }
		table.body{ max-width: 500px; margin: 0 auto; }
		h1, h2, h3, h4, h5, h6{ margin: 5px 0; }
		h1{ text-align: center; font-size: 25px; }
		h2{ font-size: 18px; }
		.width-100{
			width: 100%;
		}
		.width-50{
			width: 50%;
		}
		.width-30{
			width: 33%;
		}
		.desc{font-weight: 600; font-size: 15px;}
		table th{
			font-weight: 600;
			text-align: left;
		}
		table.container.text-center *{color: #666;}
		table.row table{ margin: 10px 0; vertical-align: top }
		table th{ font-weight: 600; font-size: 15px;  }
		table td{ font-weight: 400; font-size: 13px;  }
		table h1{ text-align: center }
		p{
			margin: 0;
		}
	</style>
</head>
<body>
	<table class="body" data-made-with-foundation="">
		<tr>
			<td class="center" align="center" valign="top">
				<center data-parsed="">
					<table class="container text-center">
						<tbody>
							<tr>
								<td>
									<table class="row">
										<tbody>
											<tr>
												<th class="width-50">
													<table>
														<tr>
															<th>
																<h1><?php echo esc_html( $email_heading ) ?></h1>
																<p class="desc"><?php echo esc_html( $email_heading_desc ) ?></p>