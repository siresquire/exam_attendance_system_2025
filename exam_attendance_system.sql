-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 12:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `exam_id`, `timestamp`) VALUES
(3, 62, 2, '2025-08-16 22:04:38');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `title`, `lecturer_id`, `course_name`, `year`, `semester`, `department`, `program_id`) VALUES
(45, 'CSC101', 'Introduction to Computer Science', NULL, 'Introduction to Computer Science', '2025', 'Second', 'IT', 97),
(46, 'CSC202', 'Data Structures and Algorithms', NULL, 'Data Structures and Algorithms', '2025', 'Second', 'IT', 80),
(47, 'CSC210', 'Database Management Systems', NULL, 'Database Management Systems', '2025', 'First', 'IT', 97),
(48, 'CSC312', 'Operating Systems', NULL, 'Operating Systems', '2025', 'First', 'IT', 97),
(49, 'CSC220', 'Web Technologies', NULL, 'Web Technologies', '2025', 'Second', 'IT', 97),
(50, 'ITC342', 'Introduction to Educational Technology', NULL, 'Introduction to Educational Technology', '2025', 'First', 'IT', 97),
(54, 'ITC353', 'Digital Electronics', NULL, NULL, '2025', 'Second', 'IT', 97);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `lecturer_name` varchar(255) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `course_id`, `exam_date`, `start_time`, `end_time`, `venue_name`, `lecturer_name`, `semester`, `lecturer_id`) VALUES
(1, 46, '2025-01-01', '13:00:00', '14:00:00', 'ROB 2', 'Dr Adasa Nkrumah', 'Second', 8),
(2, 54, '2025-01-06', '09:00:00', '11:00:00', 'ROB 32', 'Dr Adasa Nkrumah', 'Second', 8),
(3, 48, '2025-12-08', '23:00:00', '01:00:00', 'ROB 14', 'Dr Stephen Asante', 'Second', 8);

-- --------------------------------------------------------

--
-- Table structure for table `exam_attendance`
--

CREATE TABLE `exam_attendance` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `reg_number` varchar(50) DEFAULT NULL,
  `present` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_registrations`
--

CREATE TABLE `exam_registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `registration_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_registrations`
--

INSERT INTO `exam_registrations` (`id`, `student_id`, `exam_id`, `registration_time`) VALUES
(12, 62, 1, '2025-08-16 17:35:43'),
(13, 62, 2, '2025-08-16 17:44:19'),
(14, 62, 3, '2025-08-16 22:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `name`, `email`, `phone`) VALUES
(2, 'Dr Adasa Nkrumah Kofi Frimpong', 'adasa1@gmail.com', '0244112233');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `program_name` text NOT NULL,
  `session` enum('Full-Time','Sandwich','Evening','Weekend') NOT NULL,
  `degree_level` varchar(100) DEFAULT NULL,
  `campus` enum('KSI','MPG') NOT NULL,
  `department` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `faculty`, `program_name`, `session`, `degree_level`, `campus`, `department`) VALUES
(43, 'CONSTRUCTION AND WOOD', '2-Year Diploma in Construction Technology', 'Full-Time', 'Diploma', 'KSI', 'CONSTRUCTION AND WOOD'),
(44, 'ELECTRICALS AND ELECTRONICS', '2-Year Diploma in Electrical and Electronics Engineering Technology', 'Full-Time', 'Diploma', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(45, 'FASHION AND TEXTILES', '2-year Diploma in Fashion Design and Textiles', 'Full-Time', 'Diploma', 'KSI', 'FASHION AND TEXTILES'),
(46, 'MANAGEMENT EDUCATION', '2-year Diploma in Office Management and Computer Applications', 'Full-Time', 'Diploma', 'KSI', 'MANAGEMENT EDUCATION'),
(47, 'CONSTRUCTION AND WOOD', '2-year Diploma in Wood Technology', 'Full-Time', 'Diploma', 'KSI', 'CONSTRUCTION AND WOOD'),
(48, 'MECHANICAL AND AUTOMOTIVE', '2-year in Diploma in Architecture and Digital Construction', 'Full-Time', 'Diploma', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(49, 'HOSPITALITY AND TOURISM', '2-year Diploma in Catering and Hospitality', 'Full-Time', 'Diploma', 'KSI', 'HOSPITALITY AND TOURISM'),
(50, 'ACCOUNTING EDUCATION', '2-year Diploma in Business Administration, Accounting', 'Full-Time', 'Diploma', 'KSI', 'ACCOUNTING EDUCATION'),
(51, 'MANAGEMENT EDUCATION', '2-year Diploma in Business Administration, Management', 'Full-Time', 'Diploma', 'KSI', 'MANAGEMENT EDUCATION'),
(52, 'ECONOMICS EDUCATION', '2-year Diploma in Economics', 'Full-Time', 'Diploma', 'KSI', 'ECONOMICS EDUCATION'),
(53, 'HUMAN RESOURCE', '2-year Diploma in Human Resource Management', 'Full-Time', 'Diploma', 'KSI', 'HUMAN RESOURCE'),
(54, 'ELECTRICALS AND ELECTRONICS', 'B.Sc. Biomedical Equipment Engineering', 'Full-Time', 'B.Sc.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(55, 'ELECTRICALS AND ELECTRONICS', 'B.Ed. Applied Technology (Electrical and Electronics)', 'Full-Time', 'B.Ed.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(56, 'ELECTRICALS AND ELECTRONICS', 'B.Ed. STEM (Engineering with Biomedical Science)', 'Full-Time', 'B.Ed.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(57, 'CONSTRUCTION AND WOOD', 'B.Ed. STEM (Engineering with Manufacturing)', 'Full-Time', 'B.Ed.', 'KSI', 'CONSTRUCTION AND WOOD'),
(58, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Ed. Computing with Internet of Things (IOT)', 'Full-Time', 'B.Ed.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(59, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Ed. Computing with Artificial Intelligence (AI)', 'Full-Time', 'B.Ed.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(60, 'ELECTRICALS AND ELECTRONICS', 'B.Ed. STEM (Engineering with Robotics)', 'Full-Time', 'B.Ed.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(61, 'MECHANICAL AND AUTOMOTIVE', 'B.Ed. Applied Technology (Automotive and Mechanical)', 'Full-Time', 'B.Ed.', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(63, 'MANAGEMENT EDUCATION', 'B.B.A. Management', 'Weekend', 'B.B.A.', 'KSI', 'MANAGEMENT EDUCATION'),
(64, 'INTERDISPLINARY STUDIES', 'B.Ed Early Grade (Early Childhood)', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(65, 'INTERDISPLINARY STUDIES', 'B.Ed Junior High', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(66, 'INTERDISPLINARY STUDIES', 'B.Ed Upper Primary', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(67, 'CONSTRUCTION AND WOOD', 'B.Ed. Applied Technology (Building Construction and Wood)', 'Full-Time', 'B.Ed.', 'KSI', 'CONSTRUCTION AND WOOD'),
(68, 'LANGUAGES EDUCATION', 'B.Ed. Arabic', 'Full-Time', 'B.Ed.', 'KSI', 'LANGUAGES EDUCATION'),
(69, 'ACCOUNTING EDUCATION', 'B.Ed. Business Studies (Accounting)', 'Full-Time', 'B.Ed.', 'KSI', 'ACCOUNTING EDUCATION'),
(70, 'MANAGEMENT EDUCATION', 'B.Ed. Business Studies (Management)', 'Full-Time', 'B.Ed.', 'KSI', 'MANAGEMENT EDUCATION'),
(71, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Ed. Computing with Artificial Intelligence', 'Full-Time', 'B.Ed.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(72, 'CONSTRUCTION AND WOOD', 'B.Ed. Design and Communication Technology', 'Full-Time', 'B.Ed.', 'KSI', 'CONSTRUCTION AND WOOD'),
(73, 'ECONOMICS EDUCATION', 'B.Ed. Economics', 'Full-Time', 'B.Ed.', 'KSI', 'ECONOMICS EDUCATION'),
(74, 'LANGUAGES EDUCATION', 'B.Ed. English', 'Full-Time', 'B.Ed.', 'KSI', 'LANGUAGES EDUCATION'),
(75, 'LANGUAGES EDUCATION', 'B.Ed. French', 'Full-Time', 'B.Ed.', 'KSI', 'LANGUAGES EDUCATION'),
(76, 'INTERDISPLINARY STUDIES', 'B.Ed. Geography', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(77, 'LANGUAGES EDUCATION', 'B.Ed. Ghanaian Language', 'Full-Time', 'B.Ed.', 'KSI', 'LANGUAGES EDUCATION'),
(78, 'FASHION AND TEXTILES', 'B.Ed. Home Economics (Clothing and Textiles)', 'Full-Time', 'B.Ed.', 'KSI', 'FASHION AND TEXTILES'),
(79, 'HOSPITALITY AND TOURISM', 'B.Ed. Home Economics (Food and Nutrition)', 'Full-Time', 'B.Ed.', 'KSI', 'HOSPITALITY AND TOURISM'),
(80, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Ed. Information Technology', '', 'B.Ed.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(82, 'INTERDISPLINARY STUDIES', 'B.Ed. Physical Education and Health', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(83, 'INTERDISPLINARY STUDIES', 'B.Ed. Social Studies', 'Full-Time', 'B.Ed.', 'KSI', 'INTERDISPLINARY STUDIES'),
(84, 'ACCOUNTING EDUCATION', 'B.Sc Computerised Accounting', 'Weekend', 'B.Sc.', 'KSI', 'ACCOUNTING EDUCATION'),
(85, 'ACCOUNTING EDUCATION', 'B.Sc. Accounting', '', 'B.Sc.', 'KSI', 'ACCOUNTING EDUCATION'),
(86, 'ACCOUNTING EDUCATION', 'B.Sc. Admin Accounting', 'Full-Time', 'B.Sc.', 'KSI', 'ACCOUNTING EDUCATION'),
(87, 'MECHANICAL AND AUTOMOTIVE', 'B.Sc. Automotive Engineering Technology Education', '', 'B.Sc.', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(88, 'ACCOUNTING EDUCATION', 'B.Sc. Banking and Finance', '', 'B.Sc.', 'KSI', 'ACCOUNTING EDUCATION'),
(89, 'HOSPITALITY AND TOURISM', 'B.Sc. Catering and Hospitality Education', '', 'B.Sc.', 'KSI', 'HOSPITALITY AND TOURISM'),
(90, 'CONSTRUCTION AND WOOD', 'B.Sc. Civil Engineering', 'Full-Time', 'B.Sc.', 'KSI', 'CONSTRUCTION AND WOOD'),
(91, 'CONSTRUCTION AND WOOD', 'B.Sc. Construction Technology and Management with Education', '', 'B.Sc.', 'KSI', 'CONSTRUCTION AND WOOD'),
(92, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Sc. Cyber Security and Digital Forensics', 'Full-Time', 'B.Sc.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(93, 'ECONOMICS EDUCATION', 'B.Sc. Economics', 'Weekend', 'B.Sc.', 'KSI', 'ECONOMICS EDUCATION'),
(94, 'ELECTRICALS AND ELECTRONICS', 'B.Sc. Electrical and Electronics Engineering', 'Full-Time', 'B.Sc.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(95, 'ELECTRICALS AND ELECTRONICS', 'B.Sc. Electrical and Electronics Engineering Technology Education', '', 'B.Sc.', 'KSI', 'ELECTRICALS AND ELECTRONICS'),
(96, 'FASHION AND TEXTILES', 'B.Sc. Fashion Design and Textiles Education', '', 'B.Sc.', 'KSI', 'FASHION AND TEXTILES'),
(97, 'INFORMATION TECHNOLOGY EDUCATION', 'B.Sc. Information Technology', '', 'B.Sc.', 'KSI', 'INFORMATION TECHNOLOGY EDUCATION'),
(98, 'MANAGEMENT EDUCATION', 'B.Sc. Marketing', 'Full-Time', 'B.Sc.', 'KSI', 'MANAGEMENT EDUCATION'),
(99, 'IEDEI', 'B.Sc.Entrepreneurship Education', 'Full-Time', 'B.Sc.', 'KSI', 'IEDEI'),
(100, 'IEDEI', 'B.Sc. Marketing and Entrepreneurship', 'Full-Time', 'B.Sc.', 'KSI', 'IEDEI'),
(101, 'MECHANICAL AND AUTOMOTIVE', 'B.Sc. Mechanical Engineering Technology', '', 'B.Sc.', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(102, 'MECHANICAL AND AUTOMOTIVE', 'B.Sc. Mechanical Engineering Technology Education', '', 'B.Sc.', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(103, 'CONSTRUCTION AND WOOD', 'B.Sc. Plumbing, Gas and Sanitary Technology', 'Full-Time', 'B.Sc.', 'KSI', 'CONSTRUCTION AND WOOD'),
(104, 'ACCOUNTING EDUCATION', 'B.Sc. Procurement and Supply Chain Management', '', 'B.Sc.', 'KSI', 'ACCOUNTING EDUCATION'),
(105, 'MECHANICAL AND AUTOMOTIVE', 'B.Sc. Welding and Fabrication Technology with Education', 'Full-Time', 'B.Sc.', 'KSI', 'MECHANICAL AND AUTOMOTIVE'),
(106, '(COMPETENCY-BASED TRAINING) CBT', 'Diploma in Education (Competency-Based Training) CBT', 'Sandwich', 'Diploma', 'KSI', '(COMPETENCY-BASED TRAINING) CBT'),
(108, 'AGRICULTURAL EDUCATION', 'B.SC. AGRICULTURAL SCIENCE EDUCATION', 'Full-Time', 'B.Sc.', 'MPG', 'AGRICULTURAL EDUCATION'),
(109, 'AGRICULTURAL EDUCATION', 'B.ED. AGRICULTURAL SCIENCE', 'Full-Time', 'B.Ed.', 'MPG', 'AGRICULTURAL EDUCATION'),
(110, 'CROPS AND SOIL SCIENCE', 'B.SC. NATURAL RESOURCES MANAGEMENT AND EDUCATION', 'Full-Time', 'B.Sc.', 'MPG', 'CROPS AND SOIL SCIENCE'),
(111, 'AGRICULTURE ECONOMICS AND EXTENSION', 'B.SC. AGRIBUSINESS MANAGEMENT AND ENTREPRENEURSHIP EDUCATION', 'Full-Time', 'B.Sc.', 'MPG', 'AGRICULTURE ECONOMICS AND EXTENSION'),
(112, 'AGRICULTURE MECHANISATION AND ENGINEERING', 'B.SC. AGRICULTURAL ENGINEERING, TECHNOLOGY AND INNOVATIONS EDUCATION', 'Full-Time', 'B.Sc.', 'MPG', 'AGRICULTURE MECHANISATION AND ENGINEERING'),
(113, 'INTEGRATED SCIENCE EDUCATION', 'B.ED. GENERAL SCIENCE', 'Full-Time', 'B.Ed.', 'MPG', 'INTEGRATED SCIENCE EDUCATION'),
(114, 'BIOLOGICAL SCIENCES EDUCATION', 'B.ED. BIOLOGY', 'Full-Time', 'B.Ed.', 'MPG', 'BIOLOGICAL SCIENCES EDUCATION'),
(115, 'INTEGRATED SCIENCE EDUCATION', 'B.ED. MATHEMATICS', 'Full-Time', 'B.Ed.', 'MPG', 'INTEGRATED SCIENCE EDUCATION'),
(116, 'INTEGRATED SCIENCE EDUCATION', 'B.ED. PHYSICS', 'Full-Time', 'B.Ed.', 'MPG', 'INTEGRATED SCIENCE EDUCATION'),
(117, 'CHEMISTRY EDUCATION', 'B.ED. CHEMISTRY', 'Full-Time', 'B.Ed.', 'MPG', 'CHEMISTRY EDUCATION'),
(118, 'BIOLOGICAL SCIENCES EDUCATION', 'B.SC. NUTRITION AND DIETETICS', 'Full-Time', 'B.Sc.', 'MPG', 'BIOLOGICAL SCIENCES EDUCATION'),
(119, 'EDUCATIONAL STUDIES', 'B.ED. JUNIOR HIGH', 'Full-Time', 'B.Ed.', 'MPG', 'EDUCATIONAL STUDIES'),
(120, 'EDUCATIONAL STUDIES', 'B.ED. EARLY GRADE', 'Full-Time', 'B.Ed.', 'MPG', 'EDUCATIONAL STUDIES'),
(121, 'EDUCATIONAL STUDIES', 'B.ED. UPPER PRIMARY', 'Full-Time', 'B.Ed.', 'MPG', 'EDUCATIONAL STUDIES'),
(122, 'INTERDISCIPLINARY STUDIES', 'B.ED. ENGLISH', 'Full-Time', 'B.Ed.', 'MPG', 'INTERDISCIPLINARY STUDIES'),
(123, 'INTERDISCIPLINARY STUDIES', 'B.ED. FRENCH', 'Full-Time', 'B.Ed.', 'MPG', 'INTERDISCIPLINARY STUDIES'),
(124, 'INTERDISCIPLINARY STUDIES', 'B.ED. GHANAIAN LANGUAGE (ASANTE TWI OPTION)', 'Full-Time', 'B.Ed.', 'MPG', 'INTERDISCIPLINARY STUDIES'),
(125, 'INTERDISCIPLINARY STUDIES', 'B.ED. PHYSICAL EDUCATION AND HEALTH EDUCATION', 'Full-Time', 'B.Ed.', 'MPG', 'INTERDISCIPLINARY STUDIES'),
(126, 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION', 'B.SC. ENVIRONMENTAL HEALTH AND SANITATION', 'Full-Time', 'B.Sc.', 'MPG', 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION'),
(127, 'PUBLIC HEALTH EDUCATION', 'B.SC. PUBLIC HEALTH (DISEASE CONTROL)', 'Full-Time', 'B.Sc.', 'MPG', 'PUBLIC HEALTH EDUCATION'),
(128, 'PUBLIC HEALTH EDUCATION', 'B.SC. PUBLIC HEALTH (NUTRITION)', 'Full-Time', 'B.Sc.', 'MPG', 'PUBLIC HEALTH EDUCATION'),
(129, 'PUBLIC HEALTH EDUCATION', 'B.SC. PUBLIC HEALTH (PROMOTION)', 'Full-Time', 'B.Sc.', 'MPG', 'PUBLIC HEALTH EDUCATION'),
(130, 'PUBLIC HEALTH EDUCATION', 'B.SC. OCCUPATIONAL HEALTH AND SAFETY', 'Full-Time', 'B.Sc.', 'MPG', 'PUBLIC HEALTH EDUCATION'),
(131, 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION', 'DIPLOMA IN ENVIRONMENTAL HEALTH AND SANITATION', 'Sandwich', 'Diploma', 'MPG', 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION'),
(132, 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION', 'POST DIPLOMA (B.SC.) ENVIRONMENTAL HEALTH AND SANITATION', 'Sandwich', 'Post Diploma', 'MPG', 'ENVIRONMENTAL HEALTH AND SANITATION EDUCATION'),
(133, 'EDUCATIONAL STUDIES', '2-YEAR DIPLOMA IN JUNIOR HIGH EDUCATION', 'Full-Time', 'Diploma', 'MPG', 'EDUCATIONAL STUDIES'),
(134, 'EDUCATIONAL STUDIES', '2-YEAR DIPLOMA IN UPPER PRIMARY EDUCATION', 'Full-Time', 'Diploma', 'MPG', 'EDUCATIONAL STUDIES'),
(135, 'EDUCATIONAL STUDIES', '2-YEAR DIPLOMA IN EARLY GRADE EDUCATION', 'Full-Time', 'Diploma', 'MPG', 'EDUCATIONAL STUDIES');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `index_number` varchar(50) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `index_number`, `department`, `user_id`) VALUES
(61, 'Ahmed', '5300104020', 'IT', 11),
(62, 'King Fahd Adam', '5200111010', 'IT', 14);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` text NOT NULL,
  `role` enum('admin','lecturer','supervisor','student') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`, `avatar`) VALUES
(8, 'Admin User', 'grp@gmail.com', '$2y$10$yh.kRJ3EZju5WkVTjWUn3O8eixDAwb461ogS9RHbi/NPXy7oelaLO', 'admin', '2025-07-12 12:20:07', 'uploads/68794250a9935.png'),
(11, 'Ahmed', 'ahmed1@gmail.com', '$2y$10$QJlbywn3keHmbAiX7SoUYOQ1rePzcttNmhwCJ1xav6kw1Y02lGCju', 'student', '2025-07-12 22:37:49', 'uploads/6872e3bd191ea.png'),
(13, 'Dr Adasa Nkrumah Kofi Frimpong', 'adasa1@gmail.com', '$2y$10$lXui.w3gUnx8JBbo33OFvOnC/6T.IaOFpIXWeVDyjH12CqJ027iXu', 'lecturer', '2025-07-17 18:50:07', 'uploads/687945dfa0ee5.jpg'),
(14, 'King Fahd Adam', 'kingfahd@gmail.com', '$2y$10$Z1./7bej3N76q/ZMYPbvuuNJ2v4q7xtfYr721OFrMrXJKsQNRdyUm', 'student', '2025-08-16 12:30:52', 'uploads/68a079fc46627.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`exam_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `fk_program` (`program_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_exam_registration` (`student_id`,`exam_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_number` (`index_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  ADD CONSTRAINT `exam_attendance_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD CONSTRAINT `exam_registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_registrations_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
