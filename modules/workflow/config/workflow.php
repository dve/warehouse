<?php
/**
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
 * @package    Modules
 * @subpackage Workflow
 * @author     Indicia Team
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link       https://github.com/Indicia-Team/
 */

// following assumes that any update to locations/samples/occurrences leads to a change in the updated_on fields
// this is not necessarily the case for direct DB access. Direct DB access may lead to
// a situation where a full rebuild of the cache is required.

$config['entities'] =
  array(
    array('id'=>'occurrence',
          'title'=>'Occurrence',
          'event_types'=> array(
              array('code'=>'C',
                    'title'=>'Create'),
              array('code'=>'U',
                  'title'=>'Update'),
              array('code'=>'V',
                  'title'=>'Verification'),
              array('code'=>'R',
                  'title'=>'Rejection')
              ),
          'keys'=> array(
              array('table'=>'cctl',
                  'column'=>'taxa_taxon_list_external_key',
                  'title'=>'Taxon External Key')
              )
    )
  );

