-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Värd: s679.loopia.se
-- Tid vid skapande: 06 dec 2018 kl 19:21
-- Serverversion: 10.2.19-MariaDB-log
-- PHP-version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `sebastianoveland_com_db_1`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `administrator`
--

CREATE TABLE `administrator` (
  `Username` varchar(50) NOT NULL,
  `Password` varchar(60) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PassToken` varchar(60) DEFAULT NULL,
  `PassTime` timestamp NULL DEFAULT NULL,
  `LastLogin` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `class`
--

CREATE TABLE `class` (
  `ID` int(11) NOT NULL,
  `Gender` varchar(20) NOT NULL,
  `Distance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `club`
--

CREATE TABLE `club` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `gender`
--

CREATE TABLE `gender` (
  `Gender` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `race`
--

CREATE TABLE `race` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `race_runner`
--

CREATE TABLE `race_runner` (
  `Race` int(11) NOT NULL,
  `Class` int(11) NOT NULL,
  `Runner` int(11) NOT NULL,
  `Bib` int(11) NOT NULL,
  `Status` varchar(10) NOT NULL,
  `Club` int(11) DEFAULT NULL,
  `Place` int(11) NOT NULL,
  `TotalTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `race_station`
--

CREATE TABLE `race_station` (
  `Race` int(11) NOT NULL,
  `Station` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `runner`
--

CREATE TABLE `runner` (
  `ID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `Country` varchar(50) NOT NULL,
  `City` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `runner_status`
--

CREATE TABLE `runner_status` (
  `Status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `runner_units`
--

CREATE TABLE `runner_units` (
  `Runner` int(11) NOT NULL,
  `SI_unit` int(11) NOT NULL,
  `Race` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `si_unit`
--

CREATE TABLE `si_unit` (
  `ID` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `si_unit_status`
--

CREATE TABLE `si_unit_status` (
  `Status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `station`
--

CREATE TABLE `station` (
  `ID` varchar(12) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Code` int(11) NOT NULL,
  `LengthFromStart` int(11) NOT NULL,
  `LastID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `timestamp`
--

CREATE TABLE `timestamp` (
  `Timestamp` datetime NOT NULL,
  `SI_unit` int(11) NOT NULL,
  `Runner` int(11) NOT NULL,
  `Station` varchar(12) NOT NULL,
  `Race` int(11) DEFAULT NULL,
  `Place` int(11) DEFAULT NULL,
  `Lap` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`Username`);

--
-- Index för tabell `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `class_gender` (`Gender`);

--
-- Index för tabell `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`ID`);

--
-- Index för tabell `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`Gender`);

--
-- Index för tabell `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`ID`);

--
-- Index för tabell `race_runner`
--
ALTER TABLE `race_runner`
  ADD PRIMARY KEY (`Race`,`Runner`),
  ADD KEY `race_runner_club` (`Club`),
  ADD KEY `race_runner_runner` (`Runner`),
  ADD KEY `race_runner_status` (`Status`),
  ADD KEY `race_runner_class` (`Class`);

--
-- Index för tabell `race_station`
--
ALTER TABLE `race_station`
  ADD KEY `race_station_race` (`Race`),
  ADD KEY `race_station_station` (`Station`);

--
-- Index för tabell `runner`
--
ALTER TABLE `runner`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `runner_gender` (`Gender`);

--
-- Index för tabell `runner_status`
--
ALTER TABLE `runner_status`
  ADD PRIMARY KEY (`Status`);

--
-- Index för tabell `runner_units`
--
ALTER TABLE `runner_units`
  ADD PRIMARY KEY (`Runner`,`SI_unit`,`Race`),
  ADD UNIQUE KEY `SI_unit` (`SI_unit`),
  ADD KEY `runner_units_race` (`Race`);

--
-- Index för tabell `si_unit`
--
ALTER TABLE `si_unit`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `si_unit_si_unit_status` (`Status`);

--
-- Index för tabell `si_unit_status`
--
ALTER TABLE `si_unit_status`
  ADD PRIMARY KEY (`Status`);

--
-- Index för tabell `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`ID`);

--
-- Index för tabell `timestamp`
--
ALTER TABLE `timestamp`
  ADD PRIMARY KEY (`Timestamp`,`Runner`),
  ADD KEY `timestamp_station` (`Station`),
  ADD KEY `timestamp_si_unit` (`SI_unit`),
  ADD KEY `timestamp_runner` (`Runner`),
  ADD KEY `timestamp_race` (`Race`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `class`
--
ALTER TABLE `class`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `club`
--
ALTER TABLE `club`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `race`
--
ALTER TABLE `race`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `runner`
--
ALTER TABLE `runner`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_gender` FOREIGN KEY (`Gender`) REFERENCES `gender` (`Gender`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `race_runner`
--
ALTER TABLE `race_runner`
  ADD CONSTRAINT `race_runner_class` FOREIGN KEY (`Class`) REFERENCES `class` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `race_runner_club` FOREIGN KEY (`Club`) REFERENCES `club` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `race_runner_race` FOREIGN KEY (`Race`) REFERENCES `race` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `race_runner_runner` FOREIGN KEY (`Runner`) REFERENCES `runner` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `race_runner_status` FOREIGN KEY (`Status`) REFERENCES `runner_status` (`Status`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `race_station`
--
ALTER TABLE `race_station`
  ADD CONSTRAINT `race_station_race` FOREIGN KEY (`Race`) REFERENCES `race` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `race_station_station` FOREIGN KEY (`Station`) REFERENCES `station` (`ID`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `runner`
--
ALTER TABLE `runner`
  ADD CONSTRAINT `runner_gender` FOREIGN KEY (`Gender`) REFERENCES `gender` (`Gender`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `runner_units`
--
ALTER TABLE `runner_units`
  ADD CONSTRAINT `runner_units_race` FOREIGN KEY (`Race`) REFERENCES `race` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `runner_units_runner` FOREIGN KEY (`Runner`) REFERENCES `runner` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `runner_units_si_units` FOREIGN KEY (`SI_unit`) REFERENCES `si_unit` (`ID`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `si_unit`
--
ALTER TABLE `si_unit`
  ADD CONSTRAINT `si_unit_si_unit_status` FOREIGN KEY (`Status`) REFERENCES `si_unit_status` (`Status`) ON UPDATE CASCADE;

--
-- Restriktioner för tabell `timestamp`
--
ALTER TABLE `timestamp`
  ADD CONSTRAINT `timestamp_race` FOREIGN KEY (`Race`) REFERENCES `race` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `timestamp_runner` FOREIGN KEY (`Runner`) REFERENCES `runner` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `timestamp_si_unit` FOREIGN KEY (`SI_unit`) REFERENCES `si_unit` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `timestamp_station` FOREIGN KEY (`Station`) REFERENCES `station` (`ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
