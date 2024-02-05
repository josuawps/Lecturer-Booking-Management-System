-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Jun 2022 pada 15.21
-- Versi server: 10.4.20-MariaDB
-- Versi PHP: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dosen_appointment`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_table`
--

CREATE TABLE `admin_table` (
  `admin_id` int(11) NOT NULL,
  `admin_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `admin_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `admin_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `institut_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `institut_address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `institut_contact_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `institut_logo` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `admin_table`
--

INSERT INTO `admin_table` (`admin_id`, `admin_email_address`, `admin_password`, `admin_name`, `institut_name`, `institut_address`, `institut_contact_no`, `institut_logo`) VALUES
(1, 'admin@gmail.com', 'admin', 'Super Admin', 'Institut Teknologi Del', 'Depan gerbang Institut Teknologi Del, Sitoluama, Kec. Balige, Toba, Sumatera Utara 22381', '+62 632 331234', '../images/1024470851.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointment_table`
--

CREATE TABLE `appointment_table` (
  `appointment_id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dosen_schedule_id` int(11) NOT NULL,
  `appointment_number` int(11) NOT NULL,
  `reason_for_appointment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `appointment_time` time NOT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_come_into_institut` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL,
  `dosen_comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `appointment_approval` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `appointment_table`
--

INSERT INTO `appointment_table` (`appointment_id`, `dosen_id`, `student_id`, `dosen_schedule_id`, `appointment_number`, `reason_for_appointment`, `appointment_time`, `status`, `student_come_into_institut`, `dosen_comment`, `appointment_approval`) VALUES
(28, 11, 1, 53, 1001, 'TEST', '00:55:00', 'Cancel', 'No', '', 'Yes'),
(32, 11, 1, 57, 1005, 'wadawdawd', '02:00:00', 'Booked', 'No', '', 'Yes'),
(36, 11, 2, 59, 1009, 'test', '04:09:00', 'Completed', 'Yes', '', 'Yes');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen_schedule_table`
--

CREATE TABLE `dosen_schedule_table` (
  `dosen_schedule_id` int(11) NOT NULL,
  `dosen_id` int(11) NOT NULL,
  `dosen_schedule_date` date NOT NULL,
  `dosen_schedule_day` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') COLLATE utf8_unicode_ci NOT NULL,
  `dosen_schedule_start_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_schedule_end_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `average_consulting_time` int(5) NOT NULL,
  `dosen_schedule_room` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_schedule_status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `dosen_schedule_table`
--

INSERT INTO `dosen_schedule_table` (`dosen_schedule_id`, `dosen_id`, `dosen_schedule_date`, `dosen_schedule_day`, `dosen_schedule_start_time`, `dosen_schedule_end_time`, `average_consulting_time`, `dosen_schedule_room`, `dosen_schedule_status`) VALUES
(59, 11, '2022-06-30', 'Thursday', '04:09', '05:09', 65, 'GD 523', 'Active');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen_table`
--

CREATE TABLE `dosen_table` (
  `dosen_id` int(11) NOT NULL,
  `dosen_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_profile_image` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_phone_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `dosen_status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL,
  `dosen_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `dosen_table`
--

INSERT INTO `dosen_table` (`dosen_id`, `dosen_email_address`, `dosen_password`, `dosen_name`, `dosen_profile_image`, `dosen_phone_no`, `dosen_status`, `dosen_added_on`) VALUES
(11, 'istas.manalu@del.ac.id', '123', 'Istas Pratomo Manalu, S.Si, M.Sc', '../images/941187327.png', '+62 853-7319-6868', 'Active', '2022-06-16 13:33:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `student_table`
--

CREATE TABLE `student_table` (
  `student_id` int(11) NOT NULL,
  `student_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `student_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_date_of_birth` date NOT NULL,
  `student_gender` enum('Male','Female','Other') COLLATE utf8_unicode_ci NOT NULL,
  `student_address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `student_phone_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_class_year` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_added_on` datetime NOT NULL,
  `student_verification_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email_verify` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `student_table`
--

INSERT INTO `student_table` (`student_id`, `student_email_address`, `student_password`, `student_first_name`, `student_last_name`, `student_date_of_birth`, `student_gender`, `student_address`, `student_phone_no`, `student_class_year`, `student_added_on`, `student_verification_code`, `email_verify`) VALUES
(1, 'frengkykozeks@gmail.com', '123', '13321005', 'Frengky Manurung', '2022-06-20', 'Male', 'Medan', '082267470812', '2021', '2022-06-12 21:32:25', '9e06aaba5242f8afa826391aeb8d6fa6', 'Yes'),
(2, 'josuawira@gmail.com', '123', '13321009', 'Josua Sembiring', '2022-06-13', 'Male', 'Medan', '082234564721', '2019', '2022-06-12 21:33:38', '64e7e3310945816526542f4f47196c79', 'Yes'),
(32, 'jusepril@gmail.com', '123', '13321013', 'Jusepril Simanjuntak', '2022-02-01', 'Male', 'Riau', '0822453632', '2021', '2022-06-15 12:48:39', '262f5ba72c7b17a5a2e8d3002fff9d6f', 'Yes');

-- --------------------------------------------------------

--
-- Struktur dari tabel `viewing_table`
--

CREATE TABLE `viewing_table` (
  `dosen_schedule_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indeks untuk tabel `appointment_table`
--
ALTER TABLE `appointment_table`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indeks untuk tabel `dosen_schedule_table`
--
ALTER TABLE `dosen_schedule_table`
  ADD PRIMARY KEY (`dosen_schedule_id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indeks untuk tabel `dosen_table`
--
ALTER TABLE `dosen_table`
  ADD PRIMARY KEY (`dosen_id`);

--
-- Indeks untuk tabel `student_table`
--
ALTER TABLE `student_table`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `appointment_table`
--
ALTER TABLE `appointment_table`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `dosen_schedule_table`
--
ALTER TABLE `dosen_schedule_table`
  MODIFY `dosen_schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `dosen_table`
--
ALTER TABLE `dosen_table`
  MODIFY `dosen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `student_table`
--
ALTER TABLE `student_table`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dosen_schedule_table`
--
ALTER TABLE `dosen_schedule_table`
  ADD CONSTRAINT `dosen_schedule_table_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen_table` (`dosen_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
