(
	SELECT
		`absence_employee_detail`.`id_absence_employee`,
		`absence_employee_detail`.`date`,
		`absence_employee_detail`.`shift_in`,
		`absence_employee_detail`.`shift_out`,
		`absence_employee_detail`.`check_in`,
		`absence_employee_detail`.`check_out`,
		`absence_employee_detail`.`fine_additional`,
	    `holiday`.`type` AS `type_holiday`,
	    `holiday`.`name` AS `name_holiday`,
	    `dayoff`.`type` AS `type_dayoff`,
	    `overtime`.`end_overtime`,
	    (
			CASE 
				WHEN COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0) > 0
				THEN @late := COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0)
				ELSE @late := 0 
			END
		) AS `minute_late`,
		@overtime := COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`), 0) AS `minute_overtime`,
		(
			CASE 
				WHEN `absence_employee_detail`.`check_in` IS NOT NULL OR `absence_employee_detail`.`check_out` IS NOT NULL 
				THEN 1 
				ELSE 0 
			END
		) AS `point_lunch`,
		(
			CASE 
				WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
					AND `absence_employee_detail`.`shift_out` IS NOT NULL
					AND `absence_employee_detail`.`check_in` IS NULL
					AND `absence_employee_detail`.`check_out` IS NULL
					AND `holiday`.`type` IS NULL
					AND `dayoff`.`type` IS NULL
				THEN 1 
				ELSE 0 
			END
		) AS `point_alpa`,
		(
			CASE 
				WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
					AND `absence_employee_detail`.`shift_out` IS NOT NULL
					AND (`absence_employee_detail`.`check_in` IS NULL XOR `absence_employee_detail`.`check_out` IS NULL)
					AND `holiday`.`type` IS NULL
					AND `dayoff`.`type` IS NULL
				THEN 1 
				ELSE 0 
			END
		) AS `point_pending`,
		(
			CASE 
				WHEN @late > 0
				THEN FLOOR(@late / `absence_employee`.`uang_telat_permenit`) + 1
				ELSE 0 
			END
		) AS `point_late`,
		(
			CASE 
				WHEN `employee`.`need_book_overtime` = 1
				THEN (
					CASE
						WHEN `overtime`.`check_leader` = 1
						THEN
							@least_overtime := STR_TO_DATE(LEAST(CONCAT(`absence_employee_detail`.`date`,' ',`absence_employee_detail`.`check_out`), `overtime`.`end_overtime`), '%Y-%m-%d %H:%i:%s')
						ELSE
							@least_overtime := NULL
					END
				)
				ELSE (
					CASE
						WHEN TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`) > 0
						THEN
							@least_overtime := STR_TO_DATE(CONCAT(`absence_employee_detail`.`date`,' ',`absence_employee_detail`.`check_out`), '%Y-%m-%d %H:%i:%s')
						ELSE
							@least_overtime := NULL
					END
				)
			END
		) AS `least_overtime`,
		(
			CASE 
				WHEN `absence_employee_detail`.`shift_in` IS NULL AND `absence_employee_detail`.`shift_out` IS NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL
				THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
				WHEN `absence_employee_detail`.`shift_in` IS NOT NULL AND `absence_employee_detail`.`shift_out` IS NOT NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND `holiday`.`type` IS NOT NULL
				THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
				WHEN @least_overtime IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
				THEN (FLOOR(TIMESTAMPDIFF(MINUTE,CONCAT(`absence_employee_detail`.`date`,' ',`absence_employee_detail`.`shift_out`), @least_overtime) / `absence_employee`.`uang_lembur_permenit`) / 4)
				ELSE 0
			END
		) AS `point_overtime`
	FROM `absence_employee_detail`
	INNER JOIN `absence_employee` ON `absence_employee`.`id` = `absence_employee_detail`.`id_absence_employee`
	INNER JOIN `employee` ON `employee`.`id` = `absence_employee`.`id_employee`
	LEFT JOIN `holiday` ON `holiday`.`date` = `absence_employee_detail`.`date`
	LEFT JOIN `overtime` ON `overtime`.`date` = `absence_employee_detail`.`date` 
		AND `overtime`.`id_employee` = `absence_employee`.`id_employee` 
		AND `overtime`.`check_leader` = 1
	LEFT JOIN `dayoff` ON `absence_employee_detail`.`date` >= `dayoff`.`start_dayoff`
		AND `absence_employee_detail`.`date` <= `dayoff`.`end_dayoff`
		AND `dayoff`.`id_employee` = `absence_employee`.`id_employee`
		AND `dayoff`.`check_leader` = 1
	ORDER BY `absence_employee_detail`.`date` ASC
) `absence_point`
	