<?php

include_once (dirname(__DIR__)."/../Modules/DatabaseValueManager.inc.php");
include_once (dirname(__DIR__)."/../Modules/TicketManagerCustom.inc.php");

class TimeDependentOpeningCronjobController extends HttpViewController
{

  public function actionDailyCronjob()
  {
    $dbManager = new DatabaseValueManager();

    // check if plugin is active
    if($dbManager->getActive())
    {
      $ticketManager = new TicketManagerCustom();
      // get time for current date

      if($dbManager->getDailyDbDeletionSwitchActive())
      {
        // delete old tickets
        $deleteTime = $dbManager->getDeleteTimeForTickets();
        $ticketManager->deleteOldTickets($deleteTime);
      }

      if($dbManager->getDailyCronjobSwitchActive())
      {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'time_dependent_opening');

        // monday = 1 and sunday = 7
        $nextWeek = date('Y-m-d H:i:s', strtotime('+0 day'));
        $nextWeek = new DateTime($nextWeek);

        $timeString = $dbManager->getTimeFrom(date('l'), 0);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentRuhetag(), $nextWeekString,
          '(A) Ruhetag ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 1);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentVorbestellen(), $nextWeekString,
          '(A) Vorbestellen ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 2);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentMittagszeit(), $nextWeekString,
          '(A) Mittagszeit ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }


        $timeString = $dbManager->getTimeFrom(date('l'), 4);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentGeschlossen(), $nextWeekString,
          '(A) Geschlossen ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 5);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentVorbestellen(), $nextWeekString,
          '(A) Vorbestellen ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 6);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentBestellen(), $nextWeekString,
          '(A) Bestellen ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 7);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentKeinInternet(), $nextWeekString,
          '(A) KeinInternet ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }

        $timeString = $dbManager->getTimeFrom(date('l'), 8);
        if($timeString != "")
        {
          $nextWeek = $nextWeek->setTime(explode(':', $timeString)[0], explode(':', $timeString)[1], 0);
          $nextWeekString = $nextWeek->format('Y-m-d H:i:s');
          $ticketManager->createTicket($dbManager->getContentVorbestellen(), $nextWeekString,
          '(A) Vorbestellen ' . $languageTextManager->get_text('section_title_' . strtolower(date('l'))));
        }
      }
    }

    return MainFactory::create('JsonHttpControllerResponse', ['active' => $dbManager->getActive(), 'today' => date('l H:i'), 'execute' => 'actionDailyCronjob']);
  }


  public function actionCheckForJobs()
  {

    $dbManager = new DatabaseValueManager();

    $executedCommand = "Did nothing";

    // check if plugin is active
    if($dbManager->getActive() && $dbManager->getBuyButtonSwitchActive())
    {
      $today = date("Y-m-d");

      $events = array();
      $eventsMonday = $dbManager->getEvents('monday');
      $eventsTuesday = $dbManager->getEvents('tuesday');
      $eventsWednesday = $dbManager->getEvents('wednesday');
      $eventsThursday = $dbManager->getEvents('thursday');
      $eventsFriday = $dbManager->getEvents('friday');
      $eventsSaturday = $dbManager->getEvents('saturday');
      $eventsSunday = $dbManager->getEvents('sunday');

      $events = array_merge($eventsMonday, $eventsTuesday, $eventsWednesday, $eventsThursday, $eventsFriday, $eventsSaturday, $eventsSunday);

      $marker = count($events);

      $i = 0;
      foreach($events as $name => $time)
      {

        $timeDay = strtotime(explode("Time", $name)[0]);

        if(date('N') < date('N', $timeDay))
        {
          $marker = $i;
          break;
        }
        elseif(date('N') == date('N', $timeDay))
        {
          if(date('H:i') < date('H:i', strtotime($time)))
          {
            $marker = $i;
            break;
          }
        }

        $i++;
      }

      $eventKeys = array_keys($events);

      if($marker == 0)
      {
        // execute(events[count($events)-1]);
        $fieldNumber = explode("Time", $eventKeys[count($events)-1])[1];

      }
      else
      {
        // execute(events[$marker-1]);
        $fieldNumber = explode("Time", $eventKeys[$marker-1])[1];
      }

      $executedCommand = $fieldNumber . " " . $this->executeEvent($fieldNumber);
    }

    return MainFactory::create('JsonHttpControllerResponse', ['active' => $dbManager->getActive(), 'today' => date('l H:i'), 'execute' => $executedCommand]);
  }

  private function executeEvent($fieldNumber)
  {
    if($fieldNumber == "01"
    || $fieldNumber == "02"
    || $fieldNumber == "05"
    || $fieldNumber == "06"
    || $fieldNumber == "08")
    {
      $this->enableShop();
      $executedCommand = "enabled";
    }
    else
    {
      $this->disableShop();
      $executedCommand = "disabled";
    }

    /*
        Disable monday 0:00
        Disable week 21:30
        Disable weekend 22:30

        enable monday 23:59
        enable week 22:00
        enable weekend 23:00

        11:30 text schlie-t gleich contentMittagszeit
        disable lunch 13:00
        13:30 text contentGeschlossen
        enable lunch 14:00

        vorbestellen = enable
        bestellung mittagszeit = enable
        geschlossen = disable
        keine bestellung mittagszeit = disable
    */

    return $executedCommand;
  }

  private function enableShop()
  {
    $sqlDisableEnable = "UPDATE customers_status SET customers_status_show_price=1";

    if (xtc_db_query($sqlDisableEnable) === TRUE) {
      error_log("UPDATE customers_status SET customers_status_show_price=1");
    } else {
      error_log("Error: unknown SQL Error");
    }
  }

  private function disableShop()
  {
    $sqlDisableEnable = "UPDATE customers_status SET customers_status_show_price=0";

    if (xtc_db_query($sqlDisableEnable) === TRUE) {
      error_log("UPDATE customers_status SET customers_status_show_price=0");
    } else {
      error_log("Error: unknown SQL Error");
    }
  }


}