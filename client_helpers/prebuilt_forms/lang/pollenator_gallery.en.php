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
 * @package	Client
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

global $custom_terms;

/**
 * Language terms for the pollenator insect form.
 *
 * @package	Client
 */
$custom_terms = array(
	'LANG_Invocation_Error' => 'Warning : invalid GET parameters in URL'
	,'LANG_Insufficient_Privileges' => 'You do not have sufficient privileges to access this form.'
	,'LANG_Please_Refresh_Page' => 'An error has occurred. Please refresh the page.'
	,'LANG_Main_Title' => 'The Collections'
	,'LANG_Enter_Filter_Name' => 'Enter a name for this filter'
	,'LANG_Save_Filter_Button' => 'Save'
	,'LANG_Collection' => 'Return to Collection'
	,'LANG_Previous' => 'Previous'
	,'LANG_Next' => 'Next'
	,'LANG_Add_Preferred_Insect' => 'Add to My Preferred Insects'
	,'LANG_Validate' => 'Save'
	,'LANG_Add_Preferred_Collection'  => 'Add to My Preferred Collections'
	,'LANG_List' => 'Return to Filter'
	,'LANG_No_Collection_Results' => 'No Collections were returned by this search'
	,'LANG_No_Insect_Results' => 'No Insects were returned by this search'

	,'LANG_Indentification_Title' => 'Identification'
	,'LANG_Doubt' => "Express doubt about this identification"
	,'LANG_Doubt_Comment' => 'Reason for expression of doubt'
	,'LANG_Default_Doubt_Comment' => "I have expressed doubt about this identification because..."
	,'LANG_New_ID' => 'Propose a new identification'
	,'LANG_Launch_ID_Key' => "Launch the identification tool"
	,'LANG_Cancel_ID' => "Abort the identification tool"
	,'LANG_Taxa_Returned' => "Taxa returned by ID Tool:"
	,'LANG_ID_Unrecognised' => 'The following were returned by the ID tool but are unrecognised: '
	,'LANG_Taxa_Unknown_In_Tool' => 'Taxa not known in ID tool'
	,'LANG_Det_Type_Label' => 'Determination Status'
	,'LANG_Det_Type_C' => 'Correct, Validated'
	,'LANG_Det_Type_X' => 'Unidentified'
	,'LANG_Choose_Taxon' => "Choose a taxon from the list"
	,'LANG_Identify_Insect' => 'Identify this insect:'
	,'LANG_More_Precise' => 'More precise identification'
	,'LANG_ID_Comment' => 'Identification comments'
	,'LANG_Default_ID_Comment' => '...'
	,'LANG_Flower_Species' => "Flower name"
	,'LANG_Flower_Name' => "Flower name"
	,'LANG_Insect_Species' => "Insect name"
	,'LANG_Insect_Name' => "Insect name"
	,'LANG_History_Title' => 'Prior identifications'
	,'LANG_Last_ID' => 'Latest Identification'
	,'LANG_Display' => 'Show'
	,'LANG_No_Determinations' => 'No identifications recorded.'
	,'LANG_No_Comments' => 'No comments recorded.'
	
	,'LANG_Filter_Title' => 'Filters'
	,'LANG_Name_Filter_Title' => 'Name'
	,'LANG_Name' => 'Collection creator Username'
	,'LANG_Date_Filter_Title' => 'Date'
	,'LANG_Flower_Filter_Title' => 'Flower'
	,'LANG_Insect_Filter_Title' => 'Insect'
	,'LANG_Conditions_Filter_Title' => "Observation Conditions"
	,'LANG_Location_Filter_Title' => 'Location'
	,'LANG_Georef_Label' => 'Location'
	,'LANG_Georef_Notes' => '(This may be a village/town/city name, region, department or 5 digit postcode.)'
	,'LANG_INSEE' => 'INSEE No.'
	,'LANG_NO_INSEE' => 'There is no area with this INSEE number (new or old).'
	,'LANG_Search_Insects' => 'Search for Insects'
	,'LANG_Search_Collections' => 'Search for Collections'
	,'LANG_Insects_Search_Results' => 'Insects'
	,'LANG_Collections_Search_Results' => 'Collections'
		
	,'LANG_Additional_Info_Title' => 'ADDITIONAL INFORMATION'
	,'LANG_Date' => 'Date'
	,'LANG_Time' => 'Time'
	,'LANG_To' => ' to '
	,'Temperature Bands' => 'Temperature'
	,'LANG_User_Link' => 'View all collections for this user'
	
	,'LANG_Submit_Location' => 'Submit'
	,'LANG_Comments_Title' => 'COMMENTS'
	,'LANG_New_Comment' => 'Add a comment'
	,'LANG_Username' => 'Username'
	,'LANG_Email' => 'EMAIL'
	,'LANG_Comment' => 'Comment'
	,'LANG_Submit_Comment' => 'Submit'
	,'LANG_Comment_By' => "by : "
	,'LANG_Reset_Filter' => 'Reset Filter'
	,'LANG_General' => 'General'
	,'LANG_Created_Between' => 'Created Between'
	,'LANG_And' => 'and'
	,'LANG_Or' => 'or'
	,'validation_required' => "Please provide a value"
	,'LANG_Unknown' => '?'
	,'LANG_Dubious' => '!'
	,'LANG_Confirm_Express_Doubt' => 'Are you sure you wish to express doubt about this current identification?'
	,'LANG_Doubt_Expressed' => 'This person has expressed doubt about this identification.'
	,'LANG_Determination_Valid' => 'This identification was created by an expert, and is deemed valid'
	,'LANG_Determination_Incorrect' => 'This identification has been flagged as incorrect.'
	,'LANG_Determination_Unconfirmed' => 'This identification has been flagged as unconfirmed.'
	,'LANG_Determination_Unknown' => 'The taxon was not known in the identification tool.'
	,'LANG_Max_Features_Reached' => "Because of the large number of collections recorded on SPIPOLL's website, only the last 1000 recorded collections are displayed. Use the geolocation and/or the filter 'date' to see the whole collection set inside a given area and/or a period of observation."
	,'LANG_Indicia_Warehouse_Error' => 'Error returned from Indicia Warehouse'
	,'LANG_INSEE_Localisation' => 'Locality'
	,'LANG_Localisation' => 'Locality'
	,'LANG_Front Page' => 'Include collection on front page'
	,'LANG_Submit_Front_Page' => 'Save'
	,'LANG_Included_In_Front_Page' => 'This collection has been included in the list to be used on the front page.'
	,'LANG_Removed_From_Front_Page' => 'This collection has been removed from the list to be used on the front page.'
	,'LANG_Number_In_Front_Page' => 'Number of collections in the front page list: '
	,'LANG_Location_Updated' => 'The location for this collection has been updated.'
	
	,'Foraging'=> "The insect was not photo'ed on the flower"
	
	,'LANG_Bad_Collection_ID' => 'You have tried to load a session as a collection: this ID is not a valid collection.'
	,'LANG_Bad_Insect_ID' => 'You have tried to load a flower as an insect: this ID is not a valid insect.'
	,'LANG_Bad_Flower_ID' => 'You have tried to load an insect as a flower: this ID is not a valid flower.'
);