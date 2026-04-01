-- CineBook DB schema (adjusted for MySQL)
-- NOTE: DEFAULT CURRENT_TIMESTAMP used for compatibility

CREATE TABLE IF NOT EXISTS `Users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `fullname` varchar(255),
  `avatar` varchar(255),
  `username` varchar(255) UNIQUE,
  `password` varchar(255),
  `email` varchar(255) UNIQUE NOT NULL,
  `phone` varchar(255),
  `security_code` varchar(255),
  `google_id` varchar(255) UNIQUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `Cinemas` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `address` text
);

CREATE TABLE IF NOT EXISTS `Subtitles` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255)
);

CREATE TABLE IF NOT EXISTS `Movies` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `genre` varchar(255),
  `duration` int,
  `release_date` date,
  `director` varchar(255),
  `description` text,
  `poster` varchar(255),
  `actors` text,
  `age_limit` int,
  `trailer_link` varchar(255)
);

CREATE TABLE IF NOT EXISTS `Rooms` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255),
  `cinema_id` int,
  `seat_count` int
);

CREATE TABLE IF NOT EXISTS `Seats` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `room_id` int,
  `seat_name` varchar(255),
  `seat_type` varchar(255)
);

CREATE TABLE IF NOT EXISTS `Showtimes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `movie_id` int,
  `room_id` int,
  `subtitle_id` int,
  `start_time` datetime
);

CREATE TABLE IF NOT EXISTS `Ratings` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `movie_id` int,
  `rate` int,
  `comment` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `Feedbacks` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `title` varchar(255),
  `context` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `Tickets` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `showtime_id` int,
  `fullname` varchar(255),
  `email` varchar(255),
  `phone` varchar(255),
  `booking_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(10,2)
);

CREATE TABLE IF NOT EXISTS `TicketDetails` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `ticket_id` int,
  `seat_id` int,
  `price_at_booking` decimal(10,2)
);

-- Foreign keys
ALTER TABLE `Rooms` ADD CONSTRAINT fk_rooms_cinema FOREIGN KEY (`cinema_id`) REFERENCES `Cinemas` (`id`);
ALTER TABLE `Seats` ADD CONSTRAINT fk_seats_room FOREIGN KEY (`room_id`) REFERENCES `Rooms` (`id`);
ALTER TABLE `Showtimes` ADD CONSTRAINT fk_showtimes_movie FOREIGN KEY (`movie_id`) REFERENCES `Movies` (`id`);
ALTER TABLE `Showtimes` ADD CONSTRAINT fk_showtimes_room FOREIGN KEY (`room_id`) REFERENCES `Rooms` (`id`);
ALTER TABLE `Showtimes` ADD CONSTRAINT fk_showtimes_subtitle FOREIGN KEY (`subtitle_id`) REFERENCES `Subtitles` (`id`);
ALTER TABLE `Ratings` ADD CONSTRAINT fk_ratings_user FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Ratings` ADD CONSTRAINT fk_ratings_movie FOREIGN KEY (`movie_id`) REFERENCES `Movies` (`id`);
ALTER TABLE `Feedbacks` ADD CONSTRAINT fk_feedbacks_user FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Tickets` ADD CONSTRAINT fk_tickets_user FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);
ALTER TABLE `Tickets` ADD CONSTRAINT fk_tickets_showtime FOREIGN KEY (`showtime_id`) REFERENCES `Showtimes` (`id`);
ALTER TABLE `TicketDetails` ADD CONSTRAINT fk_ticketdetails_ticket FOREIGN KEY (`ticket_id`) REFERENCES `Tickets` (`id`);
ALTER TABLE `TicketDetails` ADD CONSTRAINT fk_ticketdetails_seat FOREIGN KEY (`seat_id`) REFERENCES `Seats` (`id`);

-- Sample data for quick verification
INSERT INTO `Movies` (`name`, `genre`, `duration`, `release_date`, `director`, `description`, `poster`, `actors`, `age_limit`, `trailer_link`)
VALUES
("Sample Movie A", "Action", 120, '2026-03-01', "Director A", "An action packed adventure.", "", "Actor A, Actor B", 13, ""),
("Sample Movie B", "Drama", 100, '2026-04-15', "Director B", "A heartfelt drama.", "", "Actor C, Actor D", 16, "");
