<?php

class TicketManagerCustom
{

  function createTicket($content, $timeToPop, $name) {
      // Insert Time
      $sql = "INSERT INTO job_waiting_tickets (subject, callback, due_date)
      VALUES ('$name', 'ShopNotice', '$timeToPop')";

      if (xtc_db_query($sql) === TRUE) {
        error_log("New record created successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }
      $waiting_ticket_id = xtc_db_insert_id();

      // Insert Notice

      $sqlNotice = "INSERT INTO shop_notice_jobs (waiting_ticket_id, shop_active, shop_offline_content, topbar_color, topbar_mode, popup_active)
      VALUES ($waiting_ticket_id, 1, '', '#ffffff', 'hideable', 1)";

      if (xtc_db_query($sqlNotice) === TRUE) {
        error_log("New record created successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }
      $shop_notice_job_id = xtc_db_insert_id();

      $sqlNoticeContentDE = "INSERT INTO shop_notice_job_contents (shop_notice_job_id, language_id, topbar_content, popup_content)
      VALUES ($shop_notice_job_id, 1, '', '$content')";

      if (xtc_db_query($sqlNoticeContentDE) === TRUE) {
        error_log("New record created successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }

      $sqlNoticeContentEN = "INSERT INTO shop_notice_job_contents (shop_notice_job_id, language_id, topbar_content, popup_content)
      VALUES ($shop_notice_job_id, 2, '', '$content')";

      if (xtc_db_query($sqlNoticeContentEN) === TRUE) {
        error_log("New record created successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }
  }

  public function deleteOldTickets($deleteTime)
  {
    $ids = array();
    $sql = "SELECT waiting_ticket_id from job_waiting_tickets where due_date < now() - interval $deleteTime DAY";
    $result = xtc_db_query($sql);
    while($row = $result->fetch_assoc()) {
      error_log("id: " . $row["waiting_ticket_id"]);
      $ids[] = $row["waiting_ticket_id"];
    }

    $sql = "DELETE from job_waiting_tickets where due_date < now() - interval $deleteTime DAY";

    if (xtc_db_query($sql) === TRUE) {
      error_log("Deleted successfully");
    } else {
        error_log("Error: unknown SQL Error");
    }
    error_log(print_r($ids, TRUE));
    foreach($ids as $id)
    {
      $shop_notice_job_id;
      $sql = "SELECT shop_notice_job_id from shop_notice_jobs where waiting_ticket_id = $id";
      $result = xtc_db_query($sql);
      while($row = $result->fetch_assoc()) {
        error_log("id: " . $row["shop_notice_job_id"]);
        $shop_notice_job_id = $row["shop_notice_job_id"];
      }

      $sql = "DELETE from shop_notice_jobs where waiting_ticket_id = $id";

      if (xtc_db_query($sql) === TRUE) {
        error_log("Deleted successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }


      $sql = "DELETE from shop_notice_job_contents where shop_notice_job_id = $shop_notice_job_id";

      if (xtc_db_query($sql) === TRUE) {
        error_log("Deleted successfully");
      } else {
        error_log("Error: unknown SQL Error");
      }
    }
  }
}