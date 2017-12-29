CREATE OR REPLACE VIEW gv_workflow_events AS
 SELECT DISTINCT ON (we.id) we.id, we.entity,
   case we.event_type
     when 'S' THEN 'Initially set as workflow tracked record'
     when 'V' THEN 'Verification'
     when 'R' THEN 'Rejection'
     when 'U' THEN 'Unreleased'
     when 'P' THEN 'Pending review'
     when 'F' THEN 'Fully released'
     else we.event_type
   end as event_type,
   we.key, we.key_value,
   replace(replace(replace(replace(we.values, '{
  "', ''), '"
}', ''), '":"', '='), '",
  "', '; ') as values,
   we.group_code,
   cttl.preferred_taxon
  FROM workflow_events we
  LEFT JOIN cache_taxa_taxon_lists cttl ON cttl.external_key = key_value
  WHERE we.deleted = false;