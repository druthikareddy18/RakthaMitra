-- RakthaMitra: complete supporting database tables
-- Select the rakthamitra database in phpMyAdmin before running this file.
-- This script preserves existing users and donors records.

-- The existing donors table is used by registration/login.
-- Add the availability field required by donor search, dashboard and profile.
ALTER TABLE donors
    ADD COLUMN IF NOT EXISTS availability VARCHAR(20) NOT NULL DEFAULT 'Available';

-- Blood requests used by Request Blood, Request History, Dashboard and Donor Responses.
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    units INT NOT NULL DEFAULT 1,
    hospital VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    required_date DATE NULL,
    urgency VARCHAR(30) NOT NULL DEFAULT 'Planned',
    notes TEXT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'Request Created',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_blood_requests_user (user_id),
    INDEX idx_blood_requests_match (blood_group, city, status),
    CONSTRAINT fk_blood_requests_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- In-app notifications used by Notifications page.
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifications_user_read (user_id, is_read),
    INDEX idx_notifications_created (created_at),
    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_request
        FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Donor responses used by Donor Responses / open requests.
CREATE TABLE IF NOT EXISTS donor_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    donor_user_id INT NOT NULL,
    response_status VARCHAR(20) NOT NULL DEFAULT 'Interested',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_donor_response (request_id, donor_user_id),
    CONSTRAINT fk_donor_responses_request
        FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_donor_responses_user
        FOREIGN KEY (donor_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
