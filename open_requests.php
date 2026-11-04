<?php
session_start(); require_once "db.php"; header("Content-Type: application/json; charset=utf-8");
if (!isset($_SESSION["user_id"])) { http_response_code(401); echo json_encode(["success"=>false,"message"=>"Please log in first."]); exit; }
$uid=(int)$_SESSION["user_id"];
$stmt=$conn->prepare("SELECT d.blood_group,d.city,d.availability FROM donors d WHERE d.user_id=? LIMIT 1"); $stmt->bind_param("i",$uid); $stmt->execute(); $donor=$stmt->get_result()->fetch_assoc(); $stmt->close();
if(!$donor || $donor["availability"]!=="Available"){ echo json_encode(["success"=>true,"requests"=>[]]); exit; }
$stmt=$conn->prepare("SELECT br.id,br.blood_group,br.units,br.hospital,br.city,br.urgency,br.status FROM blood_requests br WHERE br.user_id<>? AND br.status IN ('Request Created','Donor Response Received') AND br.blood_group=? AND LOWER(br.city)=LOWER(?) AND NOT EXISTS (SELECT 1 FROM donor_responses dr WHERE dr.request_id=br.id AND dr.donor_user_id=?) ORDER BY CASE br.urgency WHEN 'Urgent' THEN 1 WHEN 'Within 24 hours' THEN 2 ELSE 3 END, br.created_at DESC LIMIT 50");
$stmt->bind_param("issi",$uid,$donor["blood_group"],$donor["city"],$uid);$stmt->execute();$res=$stmt->get_result();$rows=[];while($row=$res->fetch_assoc())$rows[]=$row;$stmt->close();echo json_encode(["success"=>true,"requests"=>$rows]);
?>