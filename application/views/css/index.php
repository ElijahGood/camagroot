<?php header("Content-type: text/css");?>

.main-container {
	background-image: none;
}

.no-content-container {
	background-image: url(groot.png);
	background-size: 20%;
	background-repeat: no-repeat;
	background-position: center;
}

.photo_container {
	display: grid;
	grid-template-rows: 1fr;
	text-align: center;
	justify-content: center;
}

.pagination {
	display: inline-block;
}

.pagination a {
	color: black;
	float: left;
	padding: 8px 16px;
	text-decoration: none;
}

.page_number {
	cursor: pointer;
}

.center {
	text-align: center;
}

.left_page {
	cursor: pointer;
}

.right_page {
	cursor: pointer;
}