<?php
session_start(); require_once "db.php"; header("Content-Type: application/json; charset=utf-8");
if($_SERVER["REQUEST_METHOD"]!=="POST"){http_response_code(405);echo json_encode(["success"=>false,"message"=>"Invalid request method."]);exit;}
if(!isset($_SESSION["user_id"])){http_response_code(401);echo json_encode(["success"=>false,"message"=>"Please log in first."]);exit;}
$uid=(int)$_SESSION["user_id"];$rid=filter_input(INPUT_POST,"request_id",FILTER_VALIDATE_INT);
if(!$rid){http_response_code(400);echo json_encode(["success"=>false,"message"=>"Invalid request."]);exit;}
try{
 $conn->begin_transaction();
 $d=$conn->prepare("SELECT blood_group,city,availability FROM donors WHERE user_id=? LIMIT 1");$d->bind_param("i",$uid);$d->execute();$donor=$d->get_result()->fetch_assoc();$d->close();
 if(!$donor||$donor["availability"]!=="Available")throw new Exception("You must have an available donor profile to respond.");
 $q=$conn->prepare("SELECT user_id,blood_group,city,status FROM blood_requests WHERE id=? AND user_id<>? FOR UPDATE");$q->bind_param("ii",$rid,$uid);$q->execute();$req=$q->get_result()->fetch_assoc();$q->close();
 if(!$req||$req["blood_group"]!==$donor["blood_group"]||strcasecmp($req["city"],$donor["city"])!==0||!in_array($req["status"],["Request Created","Donor Response Received"],true))throw new Exception("This request is no longer available or does not match your registered details.");
 $ins=$conn->prepare("INSERT INTO donor_responses(request_id,donor_user_id) VALUES(?,?)");$ins->bind_param("ii",$rid,$uid);$ins->execute();$ins->close();
 $msg="A registered donor has responded to your blood request #".$rid.". Please coordinate through the platform and confirm details with the hospital.";
 $n=$conn->prepare("INSERT INTO notifications(user_id,request_id,message) VALUES(?,?,?)");$n->bind_param("iis",$req["user_id"],$rid,$msg);$n->execute();$n->close();
 if($req["status"]==="Request Created"){$s="Donor Response Received";$u=$conn->prepare("UPDATE blood_requests SET status=? WHERE id=?");$u->bind_param("si",$s,$rid);$u->execute();$u->close();}
 $conn->commit();echo json_encode(["success"=>true,"message"=>"Response submitted successfully."]);
}catch(Throwable $e){$conn->rollback();error_log($e->getMessage());http_response_code(400);echo json_encode(["success"=>false,"message"=>$e instanceof mysqli_sql_exception && $e->getCode()==1062?"You have already responded to this request.":$e->getMessage()]);}
?>