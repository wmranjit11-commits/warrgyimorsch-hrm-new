ALTER TABLE `employees` ADD `employee_id` INT(6) NOT NULL AFTER `id`;
ALTER TABLE `employees` ADD `employee_code` INT(6) NOT NULL AFTER `id`;
UPDATE attendances SET total_hours = ABS(total_hours);
