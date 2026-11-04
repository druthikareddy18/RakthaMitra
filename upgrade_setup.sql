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
