<?php
$mysqli = new mysqli("localhost","root","","gsport");

// Check connection
if ($mysqli -> connect_error) {
  echo "Đã xảy ra lỗi kết nối" . $mysqli -> connect_error;
  exit();
}