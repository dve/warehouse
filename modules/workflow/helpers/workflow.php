<?php

/**
 * @file
 * Helper class for workflow code.
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
 * @package Modules
 * @subpackage Summary_builder
 * @author Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://code.google.com/p/indicia/
 */

/**
 * Helper class for workflow functionality.
 */
class workflow {

  /**
   * Applies undo data to rewind records to their originally posted state.
   *
   * This occurs when a record has been modified by the workflow system because of a particular key value linking it to
   * a workflow event record, then the key value is changed so the workflow event is no longer relevant.
   *
   * @param object $db
   *   Database connection.
   * @param array $config
   *   Workflow module configuration for the entity.
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param object $oldRecord
   *   ORM object containing the old record details.
   * @param object $newRecord
   *   ORM Validation object containing the new record details.
   */
  public static function applyRewindsIfRequired($db, array $config, $entity, $oldRecord, &$newRecord) {
    // Can't rewind a new record, or if the config does not define keys to filter on.
    if (!isset($config['keys']) || empty($oldRecord->id)) {
      return;
    }
    $eventTypes = [];
    foreach ($config['keys'] as $keyDef) {
      $keyChanged = false;
      // We need to know if the key has changed to decide whether to wind back.
      // If the key is in the main entity, we can directly compare the old and new keys.
      if ($keyDef['table'] === $entity) {
        $keyCol = $keyDef['column'];
        $keyChanged = (string) $oldRecord->$column !== $newRecord->column;
      }
      else {
        // Find the definintion of the extra data table that contains the column we need to look for changes in. We can
        // then look to see if the foreign key pointing to that table has changed.
        foreach ($config['extraData'] as $extraDataDef) {
          if ($extraDataDef['table'] === $keyDef['table']) {
            $column = $extraDataDef['originating_table_column'];
            $keyChanged = (string) $oldRecord->$column !== $newRecord->$column;
          }
        }
      }
      if ($keyChanged) {
        $eventTypes[] = 'S';
      }
    }
    if ($entity === 'occurrence'
        && $oldRecord->record_status !== $newRecord->record_status) {
      // Remove previuos verification and rejection workflow changes as the record status is changing.
      $eventTypes[] = 'V';
      $eventTypes[] = 'R';
    }
    if (count($eventTypes) > 0) {
      // Must rewind as the field value which triggered a rule has been changed.
      $fieldRewinds = self::getRewindChangesForRecords($db, $entity, [$oldRecord->id], $eventTypes);
      kohana::log('debug', 'Rewinds: ' . var_export($fieldRewinds, true));
      foreach ($fieldRewinds["$entity.$oldRecord->id"] as $field => $value) {
        $newRecord->$field = $value;
      }
      kohana::log('debug', var_export($newRecord->as_array(), true));
    }
  }

  /**
   * Retrieves the record changes required when rewinding a set of event types against a set of records.
   *
   * @param object $db
   *   Database connection.
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param array $entityIdList
   *   List of primary keys for records in the table identied by entity.
   * @param array $eventTypes
   *   List of event types to rewind ('S', 'V', 'R').
   *
   * @return array
   *   Associatie array keyed by entity.entity_id, containing an array of the fields with undo values to apply.
   */
  public static function getRewindChangesForRecords($db, $entity, array $entityIdList, array $eventTypes) {
    $r = [];
    $undoRecords = $db
      ->select('DISTINCT workflow_undo.id, workflow_undo.entity_id, workflow_undo.original_values')
      ->from('workflow_undo')
      ->where(array(
        'workflow_undo.entity' => $entity,
        'workflow_undo.active' => 't',
      ))
      ->in('event_type', $eventTypes)
      ->in('entity_id', $entityIdList)
      ->orderby('workflow_undo.id', 'DESC')
      ->get();
    foreach ($undoRecords as $undoRecord) {
      kohana::log('debug', "Applying rewind to $entity.$undoRecord->entity_id for undo $undoRecord->id");
      $unsetColumns = json_decode($undoRecord->original_values, TRUE);
      if (!isset($r["$entity.$undoRecord->entity_id"])) {
        $r["$entity.$undoRecord->entity_id"] = $unsetColumns;
      }
      else {
        $r["$entity.$undoRecord->entity_id"] = array_merge($r["$entity.$undoRecord->entity_id"], $unsetColumns);
      }
      // As this is a hard rewind, disable the undo data.
      $db->update('workflow_undo', array('active' => 'f'), array('id' => $undoRecord->id));
    }
    return $r;
  }

  /**
   * Applies workflow event record value changes applicable to this record.
   *
   * @param object $db
   *   Database connection.
   * @param array $config
   *   Workflow module configuration for the entity.
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param object $oldRecord
   *   ORM object containing the old record details.
   * @param object $newRecord
   *   ORM Validation object containing the new record details.
   */
  public static function applyEvents($db, array $config, $entity, $oldRecord, &$newRecord) {
    $state = [];
    foreach ($config['keys'] as $keyDef) {
      $qry = self::buildEventQueryForKey($db, $config, $entity, $oldRecord, $newRecord, $keyDef);
      self::applyEventsQueryToRecord($qry, $config, $entity, $oldRecord, $newRecord, $state);
    }
    return $state;
  }

  /**
   * Construct a query to retrieve workflow events.
   *
   * Constructs a query object which will find all the events applicable to the current record for a given key in the
   * entity's configuration.
   *
   * @param object $db
   *   Database connection.
   * @param array $config
   *   Workflow module configuration for the entity.
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param object $oldRecord
   *   ORM object containing the old record details.
   * @param object $newRecord
   *   ORM Validation object containing the new record details.
   * @param array $keyDef
   *   Configuration for the key we are building the query for.
   *
   * @return object
   *   Query object.
   */
  private static function buildEventQueryForKey($db, array $config, $entity, $oldRecord, $newRecord, array $keyDef) {
    $eventTypes = [];
    $qry = $db
      ->select('workflow_events.event_type, workflow_events.mimic_rewind_first, workflow_events.values')
      ->from('workflow_events')
      ->where(array(
        'workflow_events.deleted' => 'f',
        'key' => $keyDef['db_store_value'],
      ));
    if ($keyDef['table'] === $entity) {
      $column = $keyDef['column'];
      $qry->where('workflow_events.key_value', $newRecord->$column);
      // It's a set event if the key is changing in the main entity table.
      if ($newRecord->$column !== (string) $oldRecord->$column) {
        $eventTypes[] = 'S';
      }
    }
    else {
      $qry->join($keyDef['table'], "$keyDef[table].$keyDef[column]", 'workflow_events.key_value');
      // Cross reference to the extraData for the same table to find the field name which matches $newRecord->column.
      foreach ($config['extraData'] as $extraDataDef) {
        if ($extraDataDef['table'] === $keyDef['table']) {
          $originatingColumn = $extraDataDef['originating_table_column'];
          $qry->where(
            "$extraDataDef[table].$extraDataDef[target_table_column]",
            $newRecord->$originatingColumn
          );
          // It's a set event if the foreign key in the main data table which points to the extraData record holding
          // the key is changing.
          if ($newRecord->$originatingColumn !== (string) $oldRecord->$originatingColumn) {
            $eventTypes[] = 'S';
          }
        }
      }

      // It's a set event if the record is being inserted.
      if (empty($newRecord->id)) {
        $eventTypes[] = 'S';
      }
      // Occurrence specific record status change events.
      if ($entity === 'occurrence' && $newRecord->record_status !== $oldRecord->record_status) {
        if ($newRecord->record_status === 'V') {
          $eventTypes[] = 'V';
        }
        elseif ($newRecord->record_status === 'R') {
          $eventTypes[] = 'R';
        }
        // @todo Consider unverifying? Should rewind just the verification?
      }
      $qry->in('workflow_events.event_type', $eventTypes);
    }
    return $qry;
  }

  /**
   * Applies the events query results to a record.
   *
   * Applies the field value changes determined by a query against the workflow_events table to the contents of a record
   * that is about to be saved.
   *
   * @param object $qry
   *   Query object set up to retrieve the events to apply.
   * @param array $config
   *   Workflow module configuration for the entity.
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param object $oldRecord
   *   ORM object containing the old record details.
   * @param object $newRecord
   *   ORM Validation object containing the new record details.
   * @param array $state
   *   State data to pass through to the post-process hook, containing undo data.
   */
  private static function applyEventsQueryToRecord($qry, array $config, $entity, $oldRecord, &$newRecord, array &$state) {
    $events = $qry->get();
    foreach ($events as $event) {
      $columnDeltaList = array();
      $newUndoRecord = array();
      kohana::log('debug', 'Processing event: ' . var_export($event, true));
      if ($event->mimic_rewind_first === 't' && !empty($oldRecord->id)) {
        self::mimicRewind($entity, $oldRecord->id, $columnDeltaList, $state);
      }
      $setColumns = json_decode($event->values);
      foreach ($setColumns as $setColumn => $setValue) {
        $columnDeltaList[$setColumn] = $setValue;
      }
      foreach ($columnDeltaList as $deltaColumn => $deltaValue) {
        if (isset($newRecord->$deltaColumn)) {
          kohana::log('debug', "New record has " . $newRecord->$deltaColumn);
          $undo_value = $newRecord->$deltaColumn;
        }
        elseif (!empty($oldRecord->id)) {
          $undo_value = $oldRecord->$deltaColumn;
        }
        elseif (isset($config['defaults'][$deltaColumn])) {
          $undo_value = $config['defaults'][$deltaColumn];
        }
        else {
          $undo_value = NULL;
        }
        if ($deltaValue !== $undo_value) {
          $newUndoRecord[$deltaColumn] = $undo_value;
          $newRecord->$deltaColumn = $deltaValue;
        }
      }
      $state[] = array('event_type' => $event->event_type, 'old_data' => $newUndoRecord);
    }
  }

  /**
   * Rewind a record.
   *
   * If an event wants to mimic a rewind to reset data to its original state, then undoes all changes to the record
   * caused by workflow.
   *
   * @param string $entity
   *   Name of the database entity being saved, e.g. occurrence.
   * @param int $entityId
   *   Primary key of the record in the entity table.
   * @param array $columnDeltaList
   *   Array containing the field values that will be changed by the rewind.
   * @param array $state
   *   Undo state change data from events applied to the record on this transaction which may need to be rewound.
   * @return void
   */
  private static function mimicRewind($entity, $entityId, array &$columnDeltaList, array $state) {
    for ($i = count($state) - 1; $i >= 0; $i--) {
      foreach ($state[$i]['old_data'] as $unsetColumn => $unsetValue) {
        $columnDeltaList[$unsetColumn] = $unsetValue;
      }
    }
    $undoRecords = ORM::factory('workflow_undo')
      ->where(array(
        'entity' => $entity,
        'entity_id' => $entityId,
        'active' => 't',
      ))
      ->orderby('id', 'DESC')->find_all();
    foreach ($undoRecords as $undoRecord) {
      kohana::log('debug', 'mimic rewind record: ' . var_export($undoRecord->as_array(), TRUE));
      $unsetColumns = json_decode($undoRecord->original_values, TRUE);
      $columnDeltaList = array_merge($columnDeltaList, $unsetColumns);
    }
  }

}
