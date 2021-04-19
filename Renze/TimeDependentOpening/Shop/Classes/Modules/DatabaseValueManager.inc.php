<?php

class DatabaseValueManager extends GXModuleController
{
  public function getActive()
  {
    return $this->config->get('active');
  }

  public function getBuyButtonSwitchActive()
  {
    return $this->config->get('buyButtonSwitchActive');
  }

  public function getDailyCronjobSwitchActive()
  {
    return $this->config->get('dailyCronjobSwitchActive');
  }

  public function getDailyDbDeletionSwitchActive()
  {
    return $this->config->get('dailyDbDeletionSwitchActive');
  }

  public function getDeleteTimeForTickets()
  {
    return $this->config->get('deleteTimeForTickets');
  }

  public function getEvents($weekday)
  {
    $events = array();

    for($i = 0; $i < 8; $i++)
    {
      // check if value was not set
      if($this->config->get($weekday . 'Time0'. $i) != "")
      {
        $event = array($weekday . 'Time0'. $i => $this->config->get($weekday . 'Time0'. $i));
        $events = array_merge($events, $event);
      }
    }

    return $events;
  }

  public function getTimeFrom($weekday, $i)
  {
    $weekday = strtolower($weekday);
    $time = $this->config->get($weekday . 'Time0'. $i);

    return $time;
  }

  public function getContentVorbestellen()
  {
    return $this->config->get('contentVorbestellen');
  }

  public function getContentMittagszeit()
  {
    return $this->config->get('contentMittagszeit');
  }

  public function getContentGeschlossen()
  {
    return $this->config->get('contentGeschlossen');
  }

  public function getContentBestellen()
  {
    return $this->config->get('contentBestellen');
  }

  public function getContentKeinInternet()
  {
    return $this->config->get('contentKeinInternet');
  }

  public function getContentRuhetag()
  {
    return $this->config->get('contentRuhetag');
  }
}