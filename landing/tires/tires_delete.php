<?php
require_once '../../inc/headers.php';
require_once '../../inc/functions.php';

// Get raw posted data
$input = json_decode(file_get_contents('php://input'));
$id = filter_var($input->id, FILTER_SANITIZE_NUMBER_INT);

try {
  $db = openDb();
  $db->beginTransaction();
  
  $updateSlot = $db->prepare("UPDATE slot_order SET orderline_id = null WHERE slot_order.orderline_id = orderline.id AND orderline.tires_id = :id");
  $updateSlot->bindValue(":id", $id, PDO::PARAM_INT);
  $updateSlot->execute();

  $del = $db->prepare("DELETE FROM tires WHERE id = :id");
  $del->bindValue(":id", $id, PDO::PARAM_INT);
  $del->execute();

  $db->commit();
  header('HTTP/1.1 200 OK');
  $data = array('id' => $id);
  echo json_encode($data);
} catch (PDOException $pdoex) {
  returnError($pdoex);
}
