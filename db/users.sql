-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Ned 24. úno 2019, 15:40
-- Verze serveru: 10.1.30-MariaDB
-- Verze PHP: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `autocvk`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `role` enum('customer','admin') COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(45) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(45) COLLATE utf8_czech_ci NOT NULL,
  `street` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `city` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `postcode` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  `phone` varchar(12) COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `name`, `surname`, `street`, `city`, `postcode`, `phone`) VALUES
(1, 'lucny@sspu-opava.cz', '$2y$10$wOgc1uzYondOm03cQXgzDuf6E3tGOap8nBXVRKu5ONHp7JyRUZHo.', 'customer', 'Marek', 'Lučný', 'Kostelní 152', 'Mokré Lazce', '74762', '420603481088');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
