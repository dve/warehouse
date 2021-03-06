<?php

/**
 * @file
 * A helper class for detecting various tip messages related to getting started.
 *
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package Core
 * @subpackage Helpers
 * @author Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://code.google.com/p/indicia/
 */

 defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class to provide tips for getting started with the warehouse.
 */
class gettingStarted {

  /**
   * Main access function to retrieve a list of tips.
   *
   * @param object $db
   *   Kohana database object.
   * @param array|null $authFilter
   *   User's website access filter, if not core admin.
   *
   * @return array
   *   List of tips.
   */
  public static function getTips($db, $authFilter) {
    $tips = array();
    self::checkScheduledTasks($db, $tips);
    self::checkWebsite($db, $authFilter, $tips);
    self::checkSurvey($db, $authFilter, $tips);
    // @todo Implement a check that the user has set up a species checklist and added some species.
    return $tips;
  }

  /**
   * Retrieve tips relating to the operation of scheduled tasks.
   *
   * @param object $db
   *   Kohana database object.
   * @param array $tips
   *   List of tips, which will be amended if any tips identified by this function.
   */
  private static function checkScheduledTasks($db, array &$tips) {
    $query = $db
      ->select(array(
        "sum(case when last_scheduled_task_check > now()-'1 day'::interval then 1 else 0 end) as new",
        "sum(case when last_scheduled_task_check <= now()-'1 day'::interval then 1 else 0 end) as old",
      ))
      ->from('system')
      ->where('last_scheduled_task_check is not null')
      ->get()->current();
    $description = '';
    if (empty($query->old) && empty($query->new)) {
      $description = <<<DESC
The scheduled tasks process has never been called. This means that many background
processes required for the operation of Indicia are not being run, for example species and term lookup
services will return empty results.
See <a href="http://indicia-docs.readthedocs.io/en/latest/administrating/warehouse/scheduled-tasks.html">
the scheduled tasks documentation</a>.
DESC;
    }
    elseif (empty($query->new)) {
      $description = <<<DESC
The scheduled tasks process has not been called recently. This means that many background
processes required for the operation of Indicia are not being run, for example species and term lookup
services may return empty results.
See <a href=@http://indicia-docs.readthedocs.io/en/latest/administrating/warehouse/scheduled-tasks.html">
the scheduled tasks documentation</a>.
DESC;
    }
    elseif (!empty($query->old)) {
      $description = <<<DESC
Some scheduled tasks appear to be not running correctly as their timestamp indicates the
last successful run was more than a day ago.
See <a href=@http://indicia-docs.readthedocs.io/en/latest/administrating/warehouse/scheduled-tasks.html">
the scheduled tasks documentation</a>.
DESC;
    }
    if (!empty($description)) {
      $tips[] = array(
        'title' => 'Scheduled tasks',
        'description' => $description,
      );
    }
  }

  /**
   * Retrieve tips relating to the registration of websites.
   *
   * @param object $db
   *   Kohana database object.
   * @param array|null $authFilter
   *   User's website access filter, if not core admin.
   * @param array $tips
   *   List of tips, which will be amended if any tips identified by this function.
   */
  private static function checkWebsite($db, $authFilter, array &$tips) {
    if (!empty($authFilter) && $authFilter['field'] === 'website_id') {
      // User is already allocated to some websites, so no need to prompt them to set them up.
      return;
    }
    $query = $db
      ->select('count(id) as count')
      ->from('websites')
      ->where('id<>1')
      ->get()->current();
    if ($query->count == 0) {
      $tips[] = array(
        'title' => 'Website registration',
        'description' => 'Before submitting records to this warehouse you need to register a website or app that ' .
          'the records will come from. See ' .
          '<a href="http://indicia-docs.readthedocs.io/en/latest/site-building/warehouse/websites.html">the website ' .
          'registration documentation</a>.'
      );
    }
  }

  /**
   * Retrieve tips relating to the registration of survey datasets.
   *
   * @param object $db
   *   Kohana database object.
   * @param array|null $authFilter
   *   User's website access filter, if not core admin.
   * @param array $tips
   *   List of tips, which will be amended if any tips identified by this function.
   */
  private static function checkSurvey($db, $authFilter, array &$tips) {
    $db
      ->select('count(id) as count')
      ->from('surveys')
      ->where('website_id<>1');
    if (!empty($authFilter) && $authFilter['field'] === 'website_id') {
      $db->in('website_id', $authFilter['values']);
    }
    $query = $db->get()->current();
    if ($query->count == 0) {
      $tips[] = array(
        'title' => 'Survey dataset registration',
        'description' => 'Before submitting records to this warehouse you need to register a survey dataset to add ' .
          'the records to. See ' .
          '<a href="http://indicia-docs.readthedocs.io/en/latest/site-building/warehouse/surveys.html">the survey ' .
          'dataset registration documentation</a>.'
      );
    }
  }

}
