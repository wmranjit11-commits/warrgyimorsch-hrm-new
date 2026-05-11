ALTER TABLE `projects` ADD `slug` VARCHAR(150) NOT NULL AFTER `name`;
ALTER TABLE attendances 
MODIFY status ENUM(
    'present',
    'half_day',
    'absent',
    'missing_punch',
    'leave',
    'wfh',
    'half_day_leave',
    'early_leave',
    'unpaid_leave',
    'unauthorised'
) NOT NULL DEFAULT 'absent';