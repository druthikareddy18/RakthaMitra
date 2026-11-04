<?php
session_start(); require_once "db.php"; header("Content-Type: application/json; charset=utf-8");
if(!isset($_SESSION["user_id"])){http_response_code(401);echo json_encode(["success"=>false,"message"=>"Please log in first."]);exit;}
$stats=["donors"=>0,"requests"=>0,"fulfilled"=>0,"pending"=>0];
$r=$conn->query("SELECT COUNT(*) AS total FROM donors WHERE availability='Available'");$stats["donors"]=(int)$r->fetch_assoc()["total"];
$r=$conn->query("SELECT COUNT(*) AS total, SUM(status='Request Fulfilled') AS fulfilled FROM blood_requests");$row=$r->fetch_assoc();$stats["requests"]=(int)$row["total"];$stats["fulfilled"]=(int)($row["fulfilled"]??0);$stats["pending"]=$stats["requests"]-$stats["fulfilled"];
echo json_encode(["success"=>true,"stats"=>$stats]);
?>