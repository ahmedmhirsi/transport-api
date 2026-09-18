USE reservation_api;

-- Users (16 total)
-- Password is 'password' for all, using hash '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin System', 'admin@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Sami Ben Ali', 'manager1@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(3, 'Leila Trabelsi', 'manager2@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(4, 'Karim Mansour', 'manager3@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(5, 'Nadia Bouazizi', 'manager4@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(6, 'Youssef Gharbi', 'manager5@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
(7, 'Ahmed Mejri', 'employee1@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(8, 'Fatma Saidi', 'employee2@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(9, 'Mohamed Khelifi', 'employee3@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(10, 'Salma Chaabane', 'employee4@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(11, 'Omar Belhadj', 'employee5@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(12, 'Ines Hamdi', 'employee6@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(13, 'Rami Jebali', 'employee7@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(14, 'Manel Rezgui', 'employee8@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(15, 'Bilel Ayari', 'employee9@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee'),
(16, 'Haifa Mrad', 'employee10@transport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee');

-- Transport Providers (10 total)
INSERT INTO `transport_providers` (`id`, `company_name`, `contact_name`, `address`, `email`, `password`, `phone`) VALUES
(1, 'Atlas Transport', 'Ali Jendoubi', 'Rue de Carthage, Tunis', 'contact@atlastransport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '71234567'),
(2, 'Maghreb Mobility', 'Mourad Sassi', 'Avenue Habib Bourguiba, Sousse', 'contact@maghrebmobility.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '73123456'),
(3, 'Carthage Transport', 'Sonia Mbarek', 'Route de Gremda, Sfax', 'contact@carthagetransport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '74123456'),
(4, 'Tunis Express', 'Nabil Oueslati', 'Les Berges du Lac, Tunis', 'info@tunisexpress.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '71345678'),
(5, 'North Africa Travel', 'Anis Ferchichi', 'Corniche, Bizerte', 'booking@northafricatravel.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72123456'),
(6, 'Sahel Transit', 'Rym Guesmi', 'Zone Industrielle, Monastir', 'contact@saheltransit.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '73234567'),
(7, 'Medina Logistics', 'Kamel Zoghlami', 'Avenue Ibn Jazar, Kairouan', 'info@medinalogistics.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '77123456'),
(8, 'Cap Bon Transport', 'Amira Karray', 'Avenue Neapolis, Nabeul', 'contact@capbontransport.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72234567'),
(9, 'Djerba Shuttle', 'Chokri Ben Said', 'Midoun, Djerba', 'booking@djerbashuttle.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '75123456'),
(10, 'Hammamet Lines', 'Hedi Dridi', 'Yasmine Hammamet', 'info@hammametlines.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '72345678');

-- Vehicles Park
INSERT INTO `vehicles_park` (`id`, `provider_id`, `immatriculation`, `location`, `type`, `model`, `capacity`) VALUES
(1, 1, '123-TU-4567', 'Tunis', 'Bus', 'Mercedes Sprinter', 50),
(2, 1, '124-TU-8901', 'Tunis', 'Minibus', 'Renault Master', 20),
(3, 2, '125-TU-2345', 'Sousse', 'Van', 'Iveco Daily', 9),
(4, 2, '126-TU-6789', 'Sousse', 'Bus', 'Toyota Coaster', 50),
(5, 3, '127-TU-1234', 'Sfax', 'Minibus', 'Mercedes Sprinter', 20),
(6, 4, '128-TU-5678', 'Tunis', 'Van', 'Renault Master', 9),
(7, 5, '129-TU-9012', 'Bizerte', 'Bus', 'Iveco Daily', 50),
(8, 6, '130-TU-3456', 'Monastir', 'Minibus', 'Toyota Coaster', 20),
(9, 7, '131-TU-7890', 'Kairouan', 'Van', 'Mercedes Sprinter', 9),
(10, 8, '132-TU-2345', 'Nabeul', 'Bus', 'Renault Master', 50),
(11, 9, '133-TU-6789', 'Djerba', 'Minibus', 'Iveco Daily', 20),
(12, 10, '134-TU-1234', 'Hammamet', 'Van', 'Toyota Coaster', 9),
(13, 1, '135-TU-5678', 'Tunis', 'Bus', 'Mercedes Sprinter', 50),
(14, 2, '136-TU-9012', 'Sousse', 'Minibus', 'Renault Master', 20),
(15, 3, '137-TU-3456', 'Sfax', 'Van', 'Iveco Daily', 9);

-- Trajets (Future dates in Oct 2026)
INSERT INTO `trajets` (`id`, `provider_id`, `vehicle_id`, `departure`, `destination`, `departure_date`, `departure_time`, `arrival_time`, `capacity`, `available_seats`, `status`) VALUES
(1, 1, 1, 'Tunis', 'Sousse', '2026-10-01', '08:00:00', '10:00:00', 50, 48, 'active'),
(2, 2, 3, 'Sousse', 'Sfax', '2026-10-02', '09:00:00', '11:00:00', 9, 7, 'active'),
(3, 3, 5, 'Sfax', 'Gabes', '2026-10-03', '10:00:00', '12:00:00', 20, 20, 'active'),
(4, 4, 6, 'Tunis', 'Bizerte', '2026-10-04', '11:00:00', '12:00:00', 9, 9, 'active'),
(5, 5, 7, 'Bizerte', 'Tunis', '2026-10-05', '12:00:00', '13:00:00', 50, 50, 'active'),
(6, 6, 8, 'Monastir', 'Sousse', '2026-10-06', '13:00:00', '13:30:00', 20, 20, 'active'),
(7, 7, 9, 'Kairouan', 'Tunis', '2026-10-07', '14:00:00', '16:00:00', 9, 9, 'active'),
(8, 8, 10, 'Nabeul', 'Hammamet', '2026-10-08', '15:00:00', '15:30:00', 50, 50, 'active'),
(9, 9, 11, 'Djerba', 'Zarzis', '2026-10-09', '16:00:00', '17:00:00', 20, 20, 'active'),
(10, 10, 12, 'Hammamet', 'Tunis', '2026-10-10', '17:00:00', '18:00:00', 9, 9, 'active');

-- Transport Requests
INSERT INTO `transport_requests` (`manager_email`, `employee_id`, `target_week`, `day_of_week`, `shift_start`, `shift_end`, `notes`, `status`) VALUES
('manager1@transport.tn', '7', '2026-W40', 'Monday', '08:00:00', '17:00:00', 'Morning shift', 'Pending'),
('manager2@transport.tn', '8', '2026-W40', 'Tuesday', '09:00:00', '18:00:00', 'Regular shift', 'Approved'),
('manager3@transport.tn', '9', '2026-W40', 'Wednesday', '14:00:00', '22:00:00', 'Evening shift', 'Rejected'),
('manager4@transport.tn', '10', '2026-W40', 'Thursday', '08:00:00', '17:00:00', '', 'Selected'),
('manager5@transport.tn', '11', '2026-W40', 'Friday', '09:00:00', '18:00:00', '', 'Pending'),
('manager1@transport.tn', '12', '2026-W41', 'Monday', '08:00:00', '17:00:00', '', 'Approved'),
('manager2@transport.tn', '13', '2026-W41', 'Tuesday', '09:00:00', '18:00:00', '', 'Pending'),
('manager3@transport.tn', '14', '2026-W41', 'Wednesday', '14:00:00', '22:00:00', '', 'Pending'),
('manager4@transport.tn', '15', '2026-W41', 'Thursday', '08:00:00', '17:00:00', '', 'Pending'),
('manager5@transport.tn', '16', '2026-W41', 'Friday', '09:00:00', '18:00:00', '', 'Pending');

-- Reservations
INSERT INTO `reservations` (`user_id`, `trajet_id`, `seats`, `status`) VALUES
(7, 1, 1, 'confirmed'),
(8, 1, 1, 'confirmed'),
(9, 2, 1, 'confirmed'),
(10, 2, 1, 'pending'),
(11, 3, 1, 'pending'),
(12, 4, 1, 'pending'),
(13, 5, 1, 'pending'),
(14, 6, 1, 'cancelled'),
(15, 7, 1, 'pending'),
(16, 8, 1, 'pending');

-- API Keys (SHA256 hashes of the plain keys)
-- key 1: 'tk_live_test_key_travel_agency_2026'
-- key 2: 'tk_live_test_key_corporate_portal_2026'
INSERT INTO `api_keys` (`application_name`, `api_key_hash`, `active`) VALUES
('Travel Agency App', 'f79850ef0918d2a3cd78f249c85860aa3b4ca146269bb2bb416d0ae6ef01863c', 1),
('Corporate Portal', '22ffb12b840530cdbaad8aeef7ecce0083d7dd78918b5c2d6aa2779a13cd5fd7', 1);

-- Webhooks
INSERT INTO `webhooks` (`application_name`, `endpoint_url`, `secret`) VALUES
('Travel Agency App', 'http://localhost/dxc/webhooks/test-endpoint.php', 'wh_sec_test_secret_123'),
('Corporate Portal', 'http://localhost/dxc/webhooks/test-endpoint.php', 'wh_sec_test_secret_456');

-- Webhook Events
INSERT INTO `webhook_events` (`webhook_id`, `event_type`, `payload`, `status`) VALUES
(1, 'reservation.created', '{"reservation_id": 1, "status": "confirmed"}', 'sent'),
(1, 'reservation.updated', '{"reservation_id": 1, "status": "cancelled"}', 'pending'),
(2, 'trajet.created', '{"trajet_id": 1}', 'pending');
