<?php
/**
 * Lightweight bootstrap to ensure required tables/columns exist
 * and seed a few records for the prototype features.
 */
require_once __DIR__ . "/db.php";

function ecotrack_bootstrap(PDO $pdo): void
{
    // Notifications
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message VARCHAR(255) NOT NULL,
            type VARCHAR(50) DEFAULT 'info',
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Challenges
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS challenges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            description TEXT,
            difficulty VARCHAR(30) DEFAULT 'Beginner',
            points_reward INT DEFAULT 100,
            status VARCHAR(30) DEFAULT 'active',
            start_date DATE DEFAULT (CURRENT_DATE),
            end_date DATE DEFAULT (CURRENT_DATE + INTERVAL 30 DAY)
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_challenges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            challenge_id INT NOT NULL,
            status VARCHAR(30) DEFAULT 'in_progress',
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_user_challenge (user_id, challenge_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE
        )
    ");

    // Quizzes
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quizzes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            difficulty VARCHAR(30) DEFAULT 'Easy',
            points_reward INT DEFAULT 150
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quiz_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quiz_id INT NOT NULL,
            question TEXT NOT NULL,
            option_a VARCHAR(255) NOT NULL,
            option_b VARCHAR(255) NOT NULL,
            option_c VARCHAR(255) NOT NULL,
            option_d VARCHAR(255) NOT NULL,
            correct_option ENUM('A','B','C','D') NOT NULL,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quiz_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            quiz_id INT NOT NULL,
            score INT DEFAULT 0,
            points_awarded INT DEFAULT 0,
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )
    ");

    // Sale price column for products
    $hasSale = $pdo->query("SHOW COLUMNS FROM products LIKE 'sale_price'")->rowCount() > 0;
    if (!$hasSale) {
        $pdo->exec("ALTER TABLE products ADD COLUMN sale_price DECIMAL(10,2) DEFAULT NULL AFTER price");
    }
    // Ensure at least some sale prices exist
    $pdo->exec("UPDATE products SET sale_price = ROUND(price * 0.85, 2) WHERE sale_price IS NULL");

    // Seed challenges (idempotent)
    $countChallenges = $pdo->query("SELECT COUNT(*) FROM challenges")->fetchColumn();
    if ($countChallenges == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO challenges (title, description, difficulty, points_reward) VALUES
            ('Plastic-Free Week', 'Avoid single-use plastics for 7 days', 'Beginner', 200),
            ('Paperless Office', 'Reduce paper usage by 50% this week', 'Intermediate', 250),
            ('Community Cleanup', 'Collect and sort 5kg of recyclables', 'Intermediate', 300),
            ('Green Commuter', 'Use public transport/bike for 3 days', 'Beginner', 150)
        ");
        $stmt->execute();
    }

    // Seed quizzes (idempotent)
    $countQuizzes = $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn();
    if ($countQuizzes == 0) {
        $pdo->exec("
            INSERT INTO quizzes (title, difficulty, points_reward) VALUES
            ('Plastic Identification', 'Easy', 150),
            ('Ocean Conservation', 'Medium', 200)
        ");
    }

    $countQuestions = $pdo->query("SELECT COUNT(*) FROM quiz_questions")->fetchColumn();
    if ($countQuestions == 0) {
        $pdo->exec("
            INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES
            (1, 'Which plastic code is typically recyclable curbside?', 'Code 1 (PET)', 'Code 3 (PVC)', 'Code 6 (PS)', 'Code 7 (Other)', 'A'),
            (1, 'Best way to recycle a plastic bottle?', 'Crush and cap it', 'Leave liquid inside', 'Keep the label on', 'Wrap in plastic bag', 'A'),
            (2, 'Main threat to coral reefs?', 'Plastic straws', 'Ocean acidification', 'Glass bottles', 'Seaweed growth', 'B'),
            (2, 'What is ghost gear?', 'Abandoned fishing gear', 'A rare fish species', 'Deep sea current', 'Glow plankton', 'A')
        ");
    }
}

// Run bootstrap once per request include
ecotrack_bootstrap($pdo);
