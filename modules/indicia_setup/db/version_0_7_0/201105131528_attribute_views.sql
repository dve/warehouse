-- View: gv_location_attributes 

-- DROP VIEW gv_location_attributes;

CREATE OR REPLACE VIEW gv_location_attributes AS 
         SELECT la.id, law.website_id, law.restrict_to_survey_id AS survey_id, w.title AS website, s.title AS survey, la.caption, 
                CASE la.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE la.data_type
                END AS data_type, la.public, la.created_by_id, la.deleted
           FROM location_attributes la
      JOIN location_attributes_websites law ON la.id = law.location_attribute_id AND law.deleted = false
   LEFT JOIN websites w ON w.id = law.website_id AND w.deleted=false
   LEFT JOIN surveys s ON s.id = law.restrict_to_survey_id AND s.deleted=false
   WHERE la.deleted=false
UNION 
         SELECT la.id, NULL::integer AS website_id, NULL::integer AS survey_id, NULL::text AS website, NULL::text AS survey, la.caption, 
                CASE la.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE la.data_type
                END AS data_type, la.public, la.created_by_id, la.deleted
           FROM location_attributes la
           WHERE la.deleted=false;

CREATE OR REPLACE VIEW gv_sample_attributes AS 
         SELECT sa.id, saw.website_id, saw.restrict_to_survey_id AS survey_id, w.title AS website, s.title AS survey, sa.caption, 
                CASE sa.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE sa.data_type
                END AS data_type, sa.public, sa.created_by_id, sa.deleted
           FROM sample_attributes sa
      JOIN sample_attributes_websites saw ON sa.id = saw.sample_attribute_id AND saw.deleted = false
   LEFT JOIN websites w ON w.id = saw.website_id and w.deleted=false
   LEFT JOIN surveys s ON s.id = saw.restrict_to_survey_id and s.deleted=false
   WHERE sa.deleted=false
UNION 
         SELECT sa.id, NULL::integer AS website_id, NULL::integer AS survey_id, NULL::text AS website, NULL::text AS survey, sa.caption, 
                CASE sa.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE sa.data_type
                END AS data_type, sa.public, sa.created_by_id, sa.deleted
           FROM sample_attributes sa
           WHERE sa.deleted=false;

CREATE OR REPLACE VIEW gv_occurrence_attributes AS 
         SELECT oa.id, oaw.website_id, oaw.restrict_to_survey_id AS survey_id, w.title AS website, s.title AS survey, oa.caption, 
                CASE oa.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE oa.data_type
                END AS data_type, oa.public, oa.created_by_id, oa.deleted
           FROM occurrence_attributes oa
      JOIN occurrence_attributes_websites oaw ON oa.id = oaw.occurrence_attribute_id AND oaw.deleted = false
   LEFT JOIN websites w ON w.id = oaw.website_id and w.deleted=false
   LEFT JOIN surveys s ON s.id = oaw.restrict_to_survey_id and s.deleted=false
   WHERE oa.deleted=false
UNION 
         SELECT oa.id, NULL::integer AS website_id, NULL::integer AS survey_id, NULL::text AS website, NULL::text AS survey, oa.caption, 
                CASE oa.data_type
                    WHEN 'T'::bpchar THEN 'Text'::bpchar
                    WHEN 'L'::bpchar THEN 'Lookup List'::bpchar
                    WHEN 'I'::bpchar THEN 'Integer'::bpchar
                    WHEN 'B'::bpchar THEN 'Boolean'::bpchar
                    WHEN 'F'::bpchar THEN 'Float'::bpchar
                    WHEN 'D'::bpchar THEN 'Specific Date'::bpchar
                    WHEN 'V'::bpchar THEN 'Vague Date'::bpchar
                    ELSE oa.data_type
                END AS data_type, oa.public, oa.created_by_id, oa.deleted
           FROM occurrence_attributes oa
           WHERE oa.deleted=false;