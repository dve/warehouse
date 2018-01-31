-- Table: comment_quick_reply_page_auths

-- DROP TABLE comment_quick_reply_page_auths;

CREATE TABLE comment_quick_reply_page_auths
(
  id serial NOT NULL,
  occurrence_id int not null,
  token varchar not null,
  deleted boolean NOT NULL DEFAULT false, 

  CONSTRAINT pk_comment_quick_reply_page_auth PRIMARY KEY (id),
  CONSTRAINT fk_comment_quick_reply_page_auth_occurrence_id FOREIGN KEY (occurrence_id)
      REFERENCES occurrences (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);

COMMENT ON TABLE comment_quick_reply_page_auths
  IS 'List of valid authorisation tokens for use on the record comments page on the warehouse.';

COMMENT ON COLUMN comment_quick_reply_page_auths.occurrence_id IS 'Occurrence ID token is linked to.';
COMMENT ON COLUMN comment_quick_reply_page_auths.token IS 'Token to indicate that the record comments page is valid for use.';
COMMENT ON COLUMN comment_quick_reply_page_auths.deleted IS 'Has this record been deleted?';

DROP VIEW IF EXISTS list_occurrences;

CREATE OR REPLACE VIEW list_comment_quick_reply_page_auths AS 
SELECT rcpat.id, rcpat.occurrence_id, rcpat.token
FROM comment_quick_reply_page_auths rcpat
WHERE rcpat.deleted = false