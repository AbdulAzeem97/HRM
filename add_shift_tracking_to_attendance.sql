-- ===============================================
-- ADD SHIFT TRACKING TO ATTENDANCE TABLE
-- Support for mid-month shift changes
-- ===============================================

USE `u902429527_ttphrm`;

-- Add office_shift_id column to attendances table
ALTER TABLE `attendances`
ADD COLUMN `office_shift_id` BIGINT(20) UNSIGNED NULL AFTER `employee_id`,
ADD INDEX `attendances_office_shift_id_index` (`office_shift_id`),
ADD CONSTRAINT `attendances_office_shift_id_foreign`
    FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL;

-- Update existing attendance records with current employee shift
UPDATE attendances a
JOIN employees e ON a.employee_id = e.id
SET a.office_shift_id = e.office_shift_id
WHERE a.office_shift_id IS NULL;

-- Add shift_name column to overtime_calculations for better tracking
ALTER TABLE `overtime_calculations`
MODIFY COLUMN `shift_name` VARCHAR(100) NULL COMMENT 'Shift name for this attendance';

-- Create employee_shift_changes table to track shift change history
CREATE TABLE IF NOT EXISTS `employee_shift_changes` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `employee_id` BIGINT(20) UNSIGNED NOT NULL,
    `old_shift_id` BIGINT(20) UNSIGNED NULL,
    `new_shift_id` BIGINT(20) UNSIGNED NOT NULL,
    `effective_date` DATE NOT NULL COMMENT 'Date when new shift becomes effective',
    `changed_by` BIGINT(20) UNSIGNED NULL COMMENT 'User who made the change',
    `reason` VARCHAR(255) NULL COMMENT 'Reason for shift change',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `employee_shift_changes_employee_id_index` (`employee_id`),
    INDEX `employee_shift_changes_effective_date_index` (`effective_date`),
    CONSTRAINT `employee_shift_changes_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
    CONSTRAINT `employee_shift_changes_old_shift_id_foreign`
        FOREIGN KEY (`old_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `employee_shift_changes_new_shift_id_foreign`
        FOREIGN KEY (`new_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verification queries
SELECT 'Attendance table updated with shift tracking' as message;

-- Show structure
DESCRIBE attendances;

-- Show sample attendance with shift info
SELECT
    a.id,
    a.employee_id,
    a.attendance_date,
    a.office_shift_id,
    os.shift_name,
    CONCAT(os.monday_in, ' TO ', os.monday_out) as shift_timing
FROM attendances a
LEFT JOIN office_shifts os ON a.office_shift_id = os.id
WHERE a.employee_id = 65
LIMIT 3;