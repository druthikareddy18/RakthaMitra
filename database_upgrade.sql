CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifications_user_read (user_id, is_read),
    INDEX idx_notifications_created (created_at),
    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_request
        FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- RakthaMitra complete upgrade database additions
CREATE TABLE IF NOT EXISTS donor_responses (
 id INT AUTO_INCREMENT PRIMARY KEY,
 request_id INT NOT NULL,
 donor_user_id INT NOT NULL,
 response_status VARCHAR(20) NOT NULL DEFAULT 'Interested',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_donor_response (request_id, donor_user_id),
 FOREIGN KEY (request_id) REFERENCES blood_requests(id) ON DELETE CASCADE,
 FOREIGN KEY (donor_user_id) REFERENCES users(id) ON DELETE CASCADE
);
