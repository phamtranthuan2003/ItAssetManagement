-- Run once on existing databases to support individually tracked PC/monitor pairing.
ALTER TABLE `devices`
    ADD COLUMN `paired_pc_id` INT DEFAULT NULL COMMENT 'PC đang ghép với màn hình khi được cấp phát' AFTER `current_department_id`,
    ADD KEY `idx_dev_paired_pc` (`paired_pc_id`),
    ADD CONSTRAINT `fk_dev_paired_pc`
        FOREIGN KEY (`paired_pc_id`) REFERENCES `devices` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;
