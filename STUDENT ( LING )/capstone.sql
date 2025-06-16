-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 04:50 PM
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
-- Database: `capstone`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `admin_id` char(5) NOT NULL,
  `admin_email` varchar(100) DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_password` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

CREATE TABLE `attempts` (
  `attempt_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `student_id` char(5) DEFAULT NULL,
  `score` int(4) DEFAULT NULL,
  `date_of_attempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `bookmark_id` int(11) NOT NULL,
  `student_id` char(5) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `date_added` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `goal_id` int(11) NOT NULL,
  `student_id` char(5) DEFAULT NULL,
  `weekly` tinyint(1) DEFAULT NULL,
  `monthly` tinyint(1) DEFAULT NULL,
  `time_set` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructor_id` char(5) NOT NULL,
  `instuctor_email` varchar(100) DEFAULT NULL,
  `intructor_name` varchar(100) DEFAULT NULL,
  `instructor_password` char(50) DEFAULT NULL,
  `instructor_certificate` blob DEFAULT NULL,
  `approval_status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_materials`
--

CREATE TABLE `learning_materials` (
  `material_id` int(11) NOT NULL,
  `instructor_id` char(5) DEFAULT NULL,
  `material_title` text DEFAULT NULL,
  `material_subject` char(30) DEFAULT NULL,
  `material_chapter` int(11) DEFAULT NULL,
  `material_parts` int(11) DEFAULT NULL,
  `material_learning_type` varchar(100) DEFAULT NULL,
  `material_contents` longtext DEFAULT NULL,
  `completion_status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_material_feedbacks`
--

CREATE TABLE `learning_material_feedbacks` (
  `material_feedback_id` int(11) NOT NULL,
  `material_id` int(11) DEFAULT NULL,
  `student_id` char(5) DEFAULT NULL,
  `feedback` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_pathways`
--

CREATE TABLE `learning_pathways` (
  `pathway_id` int(11) NOT NULL,
  `student_id` char(5) DEFAULT NULL,
  `last_datetime_altered` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `attempt_id` int(11) NOT NULL,
  `material_id` int(11) DEFAULT NULL,
  `student_id` char(5) DEFAULT NULL,
  `progress` int(4) DEFAULT NULL,
  `last_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questionaires`
--

CREATE TABLE `questionaires` (
  `questionaire_id` int(11) NOT NULL,
  `admin_id` char(5) DEFAULT NULL,
  `questionaire` varchar(500) DEFAULT NULL,
  `questionaire_options` varchar(500) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question` varchar(500) DEFAULT NULL,
  `question_style_id` char(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_answers`
--

CREATE TABLE `question_answers` (
  `answer_id` int(11) NOT NULL,
  `answer_list` varchar(500) DEFAULT NULL,
  `correct_answer` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_styles`
--

CREATE TABLE `question_styles` (
  `question_style_id` char(5) NOT NULL,
  `question_style` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `instructor_id` char(5) DEFAULT NULL,
  `quiz_title` text DEFAULT NULL,
  `quiz_total_questions` int(11) DEFAULT NULL,
  `quiz_subject` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_feedbacks`
--

CREATE TABLE `quiz_feedbacks` (
  `quiz_feedback_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `student_id` char(5) DEFAULT NULL,
  `feedback` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `questionaire_id` int(11) NOT NULL,
  `student_id` char(5) NOT NULL,
  `option_selected` varchar(500) DEFAULT NULL,
  `date_taken` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE `sequences` (
  `pathway_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `sequence` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` char(5) NOT NULL,
  `student_email` varchar(100) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `student_password` char(50) DEFAULT NULL,
  `student_learning_style` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `answer_selected` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_feedbacks`
--

CREATE TABLE `system_feedbacks` (
  `system_feedback_id` int(11) NOT NULL,
  `student_id` char(5) DEFAULT NULL,
  `instructor_id` char(5) DEFAULT NULL,
  `feedback` longtext DEFAULT NULL,
  `datetime_of_feedback` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` char(5) DEFAULT NULL,
  `student_id` char(5) DEFAULT NULL,
  `instructor_id` char(5) DEFAULT NULL,
  `datetime_of_log` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attempts`
--
ALTER TABLE `attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructor_id`);

--
-- Indexes for table `learning_materials`
--
ALTER TABLE `learning_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `learning_material_feedbacks`
--
ALTER TABLE `learning_material_feedbacks`
  ADD PRIMARY KEY (`material_feedback_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `learning_pathways`
--
ALTER TABLE `learning_pathways`
  ADD PRIMARY KEY (`pathway_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `questionaires`
--
ALTER TABLE `questionaires`
  ADD PRIMARY KEY (`questionaire_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_style_id` (`question_style_id`);

--
-- Indexes for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD PRIMARY KEY (`answer_id`);

--
-- Indexes for table `question_styles`
--
ALTER TABLE `question_styles`
  ADD PRIMARY KEY (`question_style_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `quiz_feedbacks`
--
ALTER TABLE `quiz_feedbacks`
  ADD PRIMARY KEY (`quiz_feedback_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`questionaire_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `sequences`
--
ALTER TABLE `sequences`
  ADD PRIMARY KEY (`pathway_id`,`material_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `attempt_id` (`attempt_id`);

--
-- Indexes for table `system_feedbacks`
--
ALTER TABLE `system_feedbacks`
  ADD PRIMARY KEY (`system_feedback_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_materials`
--
ALTER TABLE `learning_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_material_feedbacks`
--
ALTER TABLE `learning_material_feedbacks`
  MODIFY `material_feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_pathways`
--
ALTER TABLE `learning_pathways`
  MODIFY `pathway_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questionaires`
--
ALTER TABLE `questionaires`
  MODIFY `questionaire_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_answers`
--
ALTER TABLE `question_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_feedbacks`
--
ALTER TABLE `quiz_feedbacks`
  MODIFY `quiz_feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_feedbacks`
--
ALTER TABLE `system_feedbacks`
  MODIFY `system_feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
  ADD CONSTRAINT `attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `learning_materials` (`material_id`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `learning_materials`
--
ALTER TABLE `learning_materials`
  ADD CONSTRAINT `learning_materials_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`);

--
-- Constraints for table `learning_material_feedbacks`
--
ALTER TABLE `learning_material_feedbacks`
  ADD CONSTRAINT `learning_material_feedbacks_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `learning_materials` (`material_id`),
  ADD CONSTRAINT `learning_material_feedbacks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `learning_pathways`
--
ALTER TABLE `learning_pathways`
  ADD CONSTRAINT `learning_pathways_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `learning_materials` (`material_id`),
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `questionaires`
--
ALTER TABLE `questionaires`
  ADD CONSTRAINT `questionaires_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administrators` (`admin_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`question_style_id`) REFERENCES `question_styles` (`question_style_id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`);

--
-- Constraints for table `quiz_feedbacks`
--
ALTER TABLE `quiz_feedbacks`
  ADD CONSTRAINT `quiz_feedbacks_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
  ADD CONSTRAINT `quiz_feedbacks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`questionaire_id`) REFERENCES `questionaires` (`questionaire_id`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `sequences`
--
ALTER TABLE `sequences`
  ADD CONSTRAINT `sequences_ibfk_1` FOREIGN KEY (`pathway_id`) REFERENCES `learning_pathways` (`pathway_id`),
  ADD CONSTRAINT `sequences_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `learning_materials` (`material_id`);

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`attempt_id`) REFERENCES `attempts` (`attempt_id`);

--
-- Constraints for table `system_feedbacks`
--
ALTER TABLE `system_feedbacks`
  ADD CONSTRAINT `system_feedbacks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `system_feedbacks_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`);

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administrators` (`admin_id`),
  ADD CONSTRAINT `user_logs_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `user_logs_ibfk_3` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
