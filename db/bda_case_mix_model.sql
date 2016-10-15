-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2016 at 12:02 PM
-- Server version: 10.1.18-MariaDB
-- PHP Version: 7.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bda_case_mix_model`
--

-- --------------------------------------------------------

--
-- Table structure for table `documentation`
--

CREATE TABLE `documentation` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `label` varchar(255) NOT NULL COMMENT 'Label',
  `title` varchar(255) NOT NULL COMMENT 'Title',
  `text` text NOT NULL COMMENT 'Text',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds supporting documentation and help';

--
-- Dumping data for table `documentation`
--

INSERT INTO `documentation` (`id`, `label`, `title`, `text`, `updated`) VALUES
(1, 'Introduction', 'Introduction', '<p>Guidance on commissioning for special care dentistry recommends that commissioners \r\nappraise themselves of the complex needs of many patients accessing special care dentistry \r\nas such contracts must reflect the additional time and resources required to provide care for \r\nthis group of patients (BSDH 2006).</p>\r\n<p>The Department of Health in its publication &quot;<a href="http://webarchive.nationalarchives.gov.uk/20160701122411/http://www.sepho.org.uk/Download/Public/12757/1/valuing_peoples_oral_health%5B1%5D.pdf">Valuing Peoples Oral Health&quot;</a> recommends that commissioners need information regarding the degree of difficulty in carrying out dental treatment, based on the individual\'s impairment or disability and the impact this has on providing a responsive service.</p> \r\n<p>This case mix model is a tool designed to measure patient complexity by using a system of \r\nidentifiable criteria applied to a weighted scoring system.  The model identifies the various \r\nchallenges patient complexity can present dental services (such as difficulties in \r\ncommunication or co-operation). These may result in the need for a greater length of time or \r\nadditional staff to provide care for a particular patient, in comparison to an average member \r\nof the population, irrespective of which contract currency is in use to monitor the dental work undertaken.</p>\r\n<p>This model provides a methodology of describing those complex needs, which can then be \r\nused to inform contracts.  In time it is expected that its use will become widespread across \r\nthe country and across different models of dental service provision including secondary care \r\nand independent contractors.  This will enable commissioners to benchmark the services \r\nprovided to their local special needs population and ensure that the services commissioned \r\nprovide for a full range of these patient\'s needs in a way that demonstrates value for money.  \r\nIt is intended that it be used as one of a number of measures to ensure adequate provision \r\nof services for this client group.</p>\r\n<p>The model ranks the complexities presented, and a provisional weighting system has been \r\napplied to enable comparisons to be made, for example between different clinician\'s \r\ncaseloads, different clinics, and in time across different services.</p>\r\n<p>Each individual patient episode of care is measured separately, and as such it is anticipated that an individual patient will score differently for different episodes of care reflecting the complexity related to the nature of that episode. In this respect the model is more sensitive than a &lquot;patient label&rquot; in that it reflects the actual level of resource required and not a theoretical level that is only needed when the patient actually needs active treatment.</p>\r\n<p>Usage of the model is not restricted solely to primary dental care or to the UDA system \r\ncurrently operating in England and Wales. It is important to emphasise that this is a tool \r\nto measure patient complexity.  It is not intended to reflect or be used to give weight \r\nto the complexity of the dentistry  undertaken.</p>\r\n<p>A trial involving 25 salaried primary dental care services in England and Wales was carried \r\nout over 2006/2007.  With nearly all Strategic Health Authorities represented, data from 8500 \r\npatient episodes of care was submitted and analysed.  Questionnaires were sent to \r\nparticipating services of which 68 were completed and returned with positive feedback on \r\nthe model overall.  The results helped inform the development of the following criteria and \r\nscoring methods.</p>', '2016-10-02 09:34:37'),
(2, 'criteria', 'Criteria', '<p>This model identifies six independent criteria that, either solely or in combination, indicate a measurable level of patient complexity.  Each criteria covers both actual provision of clinical care for the patient, and the many additional pieces of work needed to facilitate care for many of these patients.</p>\r\n<ol class="emph">\r\n<li>Ability to communicate</li> \r\n<li>Ability to co-operate</li>\r\n<li>Medical status</li>     \r\n<li>Oral risk factors</li> \r\n<li>Access to oral care</li>\r\n<li>Legal and ethical barriers to care</li>\r\n</ol>\r\n<p>Each of the criteria is independently measured on a 4 point scale where 0 represents an \r\naverage fit and well child or adult attending for dental care, and A, B and C represent \r\nincreasing levels of complexity. The complexity may be related to the actual provision of care \r\nand/or the many additional actions necessary to facilitate care for such patients.</p> \r\n<p>The criteria and the scores given relate to a course of treatment (episode of care), and will normally be assessed when a course is either completed or discontinued.  There will be an \r\nelement of subjectivity in assessing the scores, but this pack aims to provide you with \r\nenough information to serve as a &quot;best guide&quot; model.</p> \r\n<p>Specific notes regarding each criteria:</p>', '2016-10-13 15:50:56'),
(3, 'recording_and_analysis', 'Recording and Analysis', '<ol>\r\n<li>Recording</li>\r\n<li>Provisional Weightings</li>\r\n<li>Analysis</li>\r\n<ol> ', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `patient_data`
--

CREATE TABLE `patient_data` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `patient_id` varchar(50) NOT NULL COMMENT 'Patient ID',
  `age_range` enum('0-4 years','5-15 years','16-65 years','65 years and Over') NOT NULL COMMENT 'Age Range',
  `communication` enum('0','A','B','C') DEFAULT NULL COMMENT 'Communication',
  `cooperation` enum('0','A','B','C') DEFAULT NULL COMMENT 'Cooperation',
  `medical` enum('0','A','B','C') DEFAULT NULL COMMENT 'Medical',
  `oral_risk` enum('0','A','B','C') DEFAULT NULL COMMENT 'Oral Risk',
  `access` enum('0','A','B','C') DEFAULT NULL COMMENT 'Access',
  `legal_and_ethical` enum('0','A','B','C') DEFAULT NULL COMMENT 'Legal and Ethical',
  `comments` text NOT NULL COMMENT 'Comments',
  `weighting` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Weighting',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `patient_data`
--

INSERT INTO `patient_data` (`id`, `patient_id`, `age_range`, `communication`, `cooperation`, `medical`, `oral_risk`, `access`, `legal_and_ethical`, `comments`, `weighting`, `updated`) VALUES
(46, '12345', '5-15 years', 'A', '0', 'B', 'C', '0', 'B', 'A few comments', 24, '2016-10-13 22:12:23'),
(48, '123456789', '5-15 years', 'C', 'C', 'C', 'C', 'C', 'C', 'Extreme complexity', 60, '2016-10-13 19:29:14'),
(50, '12345', '16-65 years', 'C', '0', '0', 'A', '0', 'B', '', 15, '2016-10-13 21:58:29'),
(52, '55555555555', '16-65 years', '0', '0', '0', '0', '0', '0', '', 0, '0000-00-00 00:00:00'),
(53, '9999999', '0-4 years', '0', '0', '0', '0', '0', 'A', '', 2, '2016-10-13 22:21:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documentation`
--
ALTER TABLE `documentation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_data`
--
ALTER TABLE `patient_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documentation`
--
ALTER TABLE `documentation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `patient_data`
--
ALTER TABLE `patient_data`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=54;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
