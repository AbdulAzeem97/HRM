-- Manual salary calculations for verification
-- Using corrected business rules: 26 working days, overtime after 5:15 PM, 50% deduction for half days

USE u902429527_ttphrm;

-- Employee 61: MUHAMMAD UZAIR SIDDIQUI
-- Basic: 50,000, Days: 24, Late: 1 (35min), Half: 0, OT: 245min = 4.08h
-- Daily: 50,000/26 = 1,923.08
-- Hourly: 1,923.08/9 = 213.68, OT Rate: 427.35
-- Late deduction: 0 (only half days get deductions)
-- OT Pay: 4.08 * 427.35 = 1,743.59
-- Net: 50,000 + 1,743.59 = 51,743.59

SELECT 'Employee 61 - Expected' as calculation,
    50000 as basic_salary,
    0 as half_day_deductions,
    ROUND((245/60) * ((50000/26)/9) * 2, 2) as overtime_pay,
    ROUND(50000 + ((245/60) * ((50000/26)/9) * 2), 2) as expected_total;

-- Employee 62: M.SAEED KHAN
-- Basic: 41,440, Days: 24, Late: 1 (45min), Half: 0, OT: 230min = 3.83h
-- Daily: 41,440/26 = 1,594.62
-- OT Rate: (1,594.62/9) * 2 = 354.36
-- OT Pay: 3.83 * 354.36 = 1,357.20
-- Net: 41,440 + 1,357.20 = 42,797.20

SELECT 'Employee 62 - Expected' as calculation,
    41440 as basic_salary,
    0 as half_day_deductions,
    ROUND((230/60) * ((41440/26)/9) * 2, 2) as overtime_pay,
    ROUND(41440 + ((230/60) * ((41440/26)/9) * 2), 2) as expected_total;

-- Employee 63: M.ASIF (our reference)
-- Basic: 50,000, Days: 23, Late: 0, Half: 0, OT: 275min = 4.58h
-- OT Pay: 4.58 * 427.35 = 1,957.28
-- Net: 50,000 + 1,957.28 = 51,957.28

SELECT 'Employee 63 - Expected' as calculation,
    50000 as basic_salary,
    0 as half_day_deductions,
    ROUND((275/60) * ((50000/26)/9) * 2, 2) as overtime_pay,
    ROUND(50000 + ((275/60) * ((50000/26)/9) * 2), 2) as expected_total;

-- Employee 64: M.TASLEEM
-- Basic: 37,000, Days: 24, Late: 2 (25min+30min), Half: 0, OT: 350min = 5.83h
-- OT Pay: 5.83 * ((37,000/26)/9) * 2 = 5.83 * 316.24 = 1,843.68
-- Net: 37,000 + 1,843.68 = 38,843.68

SELECT 'Employee 64 - Expected' as calculation,
    37000 as basic_salary,
    0 as half_day_deductions,
    ROUND((350/60) * ((37000/26)/9) * 2, 2) as overtime_pay,
    ROUND(37000 + ((350/60) * ((37000/26)/9) * 2), 2) as expected_total;

-- Employee 65: HASEEB AHMED
-- Basic: 42,750, Days: 23, Late: 0, Half: 1 (150min), OT: 110min = 1.83h
-- Daily: 42,750/26 = 1,644.23
-- Half day deduction: 1 * (1,644.23 * 0.5) = 822.12
-- OT Pay: 1.83 * ((42,750/26)/9) * 2 = 1.83 * 365.61 = 669.07
-- Net: 42,750 - 822.12 + 669.07 = 42,596.95

SELECT 'Employee 65 - Expected' as calculation,
    42750 as basic_salary,
    ROUND(1 * ((42750/26) * 0.5), 2) as half_day_deductions,
    ROUND((110/60) * ((42750/26)/9) * 2, 2) as overtime_pay,
    ROUND(42750 - (1 * ((42750/26) * 0.5)) + ((110/60) * ((42750/26)/9) * 2), 2) as expected_total;