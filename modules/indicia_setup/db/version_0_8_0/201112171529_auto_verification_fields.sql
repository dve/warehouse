﻿-- Websites have a flag added to allow selection of verification checks on or off.
ALTER TABLE websites ADD COLUMN verification_checks_enabled boolean;
UPDATE websites SET verification_checks_enabled=false;
ALTER TABLE websites ALTER COLUMN verification_checks_enabled SET NOT NULL;
ALTER TABLE websites ALTER COLUMN verification_checks_enabled SET DEFAULT false;
COMMENT ON COLUMN websites.verification_checks_enabled IS 'Are automated verification checks enabled for this website?';

-- occurrences need fields added to track the verification check last run so we can work out if they need to be rechecked.
ALTER TABLE occurrences ADD COLUMN last_verification_check_date timestamp;
COMMENT ON COLUMN occurrences.last_verification_check_date IS 'Date & time that verification checks were last run on this occurrence, if any.';

ALTER TABLE occurrences ADD COLUMN last_verification_check_taxa_taxon_list_id integer;
COMMENT ON COLUMN occurrences.last_verification_check_date IS 'The taxa_taxon_list_id that this occurrence was associated with when verification checks were last run. If this has changed then verification checks should be re-run. Foreign key to taxa_taxon_lists.';
ALTER TABLE occurrences
  ADD CONSTRAINT fk_occurrences_last_verification_check_taxa_taxon_list FOREIGN KEY (last_verification_check_taxa_taxon_list_id)
      REFERENCES taxa_taxon_lists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE occurrences ADD COLUMN last_verification_check_version integer;
COMMENT ON COLUMN occurrences.last_verification_check_version IS 'The version number of the verification rule data associated with the taxon at the time of the last automated verification check. Used to determine if checks need to be re-run.';
UPDATE occurrences SET last_verification_check_version=0;
ALTER TABLE occurrences ALTER COLUMN last_verification_check_version SET NOT NULL;
ALTER TABLE occurrences ALTER COLUMN last_verification_check_version SET DEFAULT 0;

-- taxa in lists need a version stamp for their verification check values, so if they change we can prompt a recheck.
ALTER TABLE taxa_taxon_lists ADD COLUMN verification_check_version integer;
COMMENT ON COLUMN taxa_taxon_lists.verification_check_version IS 'A version number which is incremented each time that a custom attribute attached to a taxon which provides an input value for verification checks is changed.';
UPDATE taxa_taxon_lists SET verification_check_version=0;
ALTER TABLE taxa_taxon_lists ALTER COLUMN verification_check_version SET NOT NULL;
ALTER TABLE taxa_taxon_lists ALTER COLUMN verification_check_version SET DEFAULT 0;

-- occurrence comments used to track verification output, so add flags to distinguish auto from manual comments
ALTER TABLE occurrence_comments ADD COLUMN auto_generated boolean;
UPDATE occurrence_comments SET auto_generated=false;
ALTER TABLE occurrence_comments ALTER COLUMN auto_generated SET NOT NULL;
ALTER TABLE occurrence_comments ALTER COLUMN auto_generated SET DEFAULT false;
COMMENT ON COLUMN occurrence_comments.auto_generated IS 'Was this comment generated by an automated verification check?';

ALTER TABLE occurrence_comments ADD COLUMN generated_by varchar (100);
COMMENT ON COLUMN occurrence_comments.generated_by IS 'When a comment is auto-generated, names the system process (e.g. verification ruleset) that generated the comment.';

ALTER TABLE occurrence_comments ADD COLUMN implies_manual_check_required boolean;
UPDATE occurrence_comments SET implies_manual_check_required=false;
ALTER TABLE occurrence_comments ALTER COLUMN implies_manual_check_required SET NOT NULL;
ALTER TABLE occurrence_comments ALTER COLUMN implies_manual_check_required SET DEFAULT false;
COMMENT ON COLUMN occurrence_comments.implies_manual_check_required IS 'When a comment is generated by a verification rule, if this is true then it implies that the record has been flagged for a manual verification check.';

COMMENT ON TABLE occurrence_comments IS 'List of comments regarding the occurrence. These are either posted by users viewing the occurrence subsequent to initial data entry or can be autop-generated by the system when running verification checks.';

ALTER TABLE taxa_taxon_list_attributes ADD COLUMN for_verification_check boolean;
UPDATE taxa_taxon_list_attributes SET for_verification_check=false;
ALTER TABLE taxa_taxon_list_attributes ALTER COLUMN for_verification_check SET NOT NULL;
ALTER TABLE taxa_taxon_list_attributes ALTER COLUMN for_verification_check SET DEFAULT false;
COMMENT ON COLUMN taxa_taxon_list_attributes.for_verification_check IS 'Set to true when an attribute is used to store a value used for auto-verification. This allows tracking of when the occurrences associated with a species need re-verification.';