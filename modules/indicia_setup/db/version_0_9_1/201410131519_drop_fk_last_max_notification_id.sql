-- this constraint is unnecessary, because notifications can get deleted (e.g. when clearing notifications from previous automated check runs).
ALTER TABLE user_email_notification_frequency_last_runs DROP CONSTRAINT fk_last_max_notification_id;