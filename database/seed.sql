-- ============================================================
--  Chanda Collection App — Demo seed data
--  Run AFTER database/schema.sql:
--      mysql -u root < database/seed.sql
--
--  Demo passwords (change in production!):
--      Staff / donors : Chanda@123
--      Settings PIN   : 123456
-- ============================================================

USE `cca`;
SET NAMES utf8mb4;

INSERT IGNORE INTO `users`
(`id`,`role`,`name`,`mobile`,`email`,`password_hash`,`locality`,`message`,`show_public`,`is_anonymous_default`,`status`) VALUES
(1,'super_admin','Developer Admin','9000000001','dev@example.com','$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patna',NULL,1,0,'active'),
(2,'admin','Amit Kumar','9000000002','admin@example.com','$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patna',NULL,1,0,'active'),
(3,'volunteer','Ravi Volunteer','9000000003',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Kankarbagh',NULL,1,0,'active'),
(10,'bhaktpremi','Ramesh Sah','9811000010',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Kankarbagh, Patna','छठी मईया सबके कल्याण करें 🙏',1,0,'active'),
(11,'bhaktpremi','Sunita Devi','9811000011',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Danapur, Patna','जय छठी मईया 🌅',1,0,'active'),
(12,'bhaktpremi','Manoj Gupta','9811000012',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Boring Road, Patna',NULL,1,0,'active'),
(13,'bhaktpremi','Anil Kumar','9811000013',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Rajendra Nagar, Patna',NULL,1,0,'active'),
(14,'bhaktpremi','Priya Sharma','9811000014',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patliputra, Patna',NULL,1,0,'active'),
(15,'bhaktpremi','Vikash Singh','9811000015',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Bailey Road, Patna',NULL,1,0,'active'),
(16,'bhaktpremi','Kavita Jha','9811000016',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Kankarbagh, Patna',NULL,1,0,'active'),
(17,'bhaktpremi','Deepak Yadav','9811000017',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Phulwari Sharif',NULL,1,0,'active'),
(18,'bhaktpremi','Seema Kumari','9811000018',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patna City',NULL,1,0,'active'),
(19,'bhaktpremi','Rahul Verma','9811000019',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Khagaul',NULL,1,0,'active'),
(20,'bhaktpremi','Suresh Prasad','9811000020',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Digha',NULL,1,0,'active'),
(21,'bhaktpremi','Nisha Devi','9811000021',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Gardanibagh',NULL,1,0,'active'),
(22,'bhaktpremi','Amit Raj','9811000022',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patrakar Nagar',NULL,1,0,'active'),
(23,'bhaktpremi','Ritu Sinha','9811000023',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Rajiv Nagar',NULL,1,0,'active'),
(24,'bhaktpremi','Sanjay Paswan','9811000024',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Kadamkuan',NULL,1,0,'active'),
(25,'bhaktpremi','Anonymous Bhakt','9811000025',NULL,'$2y$10$dDCDMwsq/iRXioxNi6umcuBP2lipgNhMKzV/A5J8pPkUEG1mRPzGe','Patna',NULL,0,1,'active');

INSERT IGNORE INTO `festivals`
(`id`,`name`,`year`,`theme`,`target_amount`,`start_date`,`end_date`,`is_active`) VALUES
(1,'Chhath Puja 2026',2026,'saffron',500000.00,'2026-11-17','2026-11-20',1);

INSERT IGNORE INTO `settings` (`skey`,`svalue`) VALUES
('committee_name','Shri Chhath Puja Samiti, Patna'),
('festival_title','Chhath Puja 2026'),
('banner_text','Chhath Puja 2026 — Chanda Collection'),
('upi_id',''),
('refresh_interval','8'),
('badge_cutoffs','{"platinum":1,"gold":2,"silver":3,"bronze":10}'),
('receipt_prefix','CH'),
('gateway_enabled','0'),
('maintenance_mode','0'),
('theme','saffron');

INSERT IGNORE INTO `security_pin` (`id`,`pin_hash`) VALUES
(1,'$2y$10$IkyO0lCYvr0H.RcyQUqjx.TPFDgi6PoeM4KUWG4x4rcclLg0G2t4O');

INSERT IGNORE INTO `donations`
(`id`,`festival_id`,`donor_id`,`amount`,`mode`,`utr_no`,`collected_by`,`status`,`is_anonymous`,`receipt_no`,`created_at`,`approved_by`,`approved_at`) VALUES
(1,1,10,51000.00,'upi','UTR510001',NULL,'approved',0,'CH2026-0001','2026-11-02 09:15:00',2,'2026-11-02 09:40:00'),
(2,1,11,38500.00,'upi','UTR385002',NULL,'approved',0,'CH2026-0002','2026-11-02 11:20:00',2,'2026-11-02 11:45:00'),
(3,1,12,27000.00,'cash',NULL,3,'approved',0,'CH2026-0003','2026-11-03 08:30:00',2,'2026-11-03 09:00:00'),
(4,1,13,21000.00,'upi','UTR210004',NULL,'approved',0,'CH2026-0004','2026-11-03 18:10:00',2,'2026-11-03 18:30:00'),
(5,1,14,15501.00,'upi','UTR155005',NULL,'approved',0,'CH2026-0005','2026-11-04 07:45:00',2,'2026-11-04 08:05:00'),
(6,1,15,12100.00,'cash',NULL,3,'approved',0,'CH2026-0006','2026-11-04 12:30:00',2,'2026-11-04 13:00:00'),
(7,1,16,9100.00,'upi','UTR910007',NULL,'approved',0,'CH2026-0007','2026-11-04 19:20:00',2,'2026-11-04 19:40:00'),
(8,1,17,7501.00,'upi','UTR750008',NULL,'approved',0,'CH2026-0008','2026-11-05 09:05:00',2,'2026-11-05 09:25:00'),
(9,1,18,5100.00,'cash',NULL,3,'approved',0,'CH2026-0009','2026-11-05 16:40:00',2,'2026-11-05 17:00:00'),
(10,1,19,3501.00,'upi','UTR350010',NULL,'approved',0,'CH2026-0010','2026-11-05 20:15:00',2,'2026-11-05 20:30:00'),
(11,1,20,3100.00,'upi','UTR310011',NULL,'approved',0,'CH2026-0011','2026-11-06 08:20:00',2,'2026-11-06 08:40:00'),
(12,1,21,2501.00,'cash',NULL,3,'approved',0,'CH2026-0012','2026-11-06 10:10:00',2,'2026-11-06 10:30:00'),
(13,1,22,2100.00,'upi','UTR210013',NULL,'approved',0,'CH2026-0013','2026-11-06 14:25:00',2,'2026-11-06 14:45:00'),
(14,1,23,1501.00,'upi','UTR150014',NULL,'approved',0,'CH2026-0014','2026-11-06 18:35:00',2,'2026-11-06 18:55:00'),
(15,1,24,1101.00,'cash',NULL,3,'approved',0,'CH2026-0015','2026-11-07 07:30:00',2,'2026-11-07 07:50:00'),
(16,1,25,44694.00,'upi','UTR446916',NULL,'approved',1,'CH2026-0016','2026-11-07 08:00:00',2,'2026-11-07 08:15:00'),
(17,1,10,2100.00,'upi','UTR210017',NULL,'pending',0,NULL,'2026-11-07 08:20:00',NULL,NULL);
