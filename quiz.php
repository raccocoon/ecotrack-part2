<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
require "config/bootstrap.php";

$userId = $_SESSION["user_id"];
$message = "";
$quizId = isset($_GET['quiz']) ? (int)$_GET['quiz'] : null;
$showQuiz = false;
$quiz = null;
$questions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    $quizId = (int)$_POST['quiz_id'];
    $quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quizStmt->execute([$quizId]);
    $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

    $questionsStmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $questionsStmt->execute([$quizId]);
    $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $score = 0;
    foreach ($questions as $question) {
        $userAnswer = $_POST['q_'.$question['id']] ?? '';
        if ($userAnswer === $question['correct_option']) {
            $score++;
        }
    }

    $pointsAwarded = 0;
    if ($quiz && count($questions) > 0) {
        $pointsAwarded = (int)round(($score / count($questions)) * $quiz['points_reward']);
        $pdo->prepare("INSERT INTO quiz_attempts (user_id, quiz_id, score, points_awarded) VALUES (?, ?, ?, ?)")
            ->execute([$userId, $quizId, $score, $pointsAwarded]);
        $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?")->execute([$pointsAwarded, $userId]);
        $pdo->prepare("INSERT INTO notifications (user_id, message, type) VALUES (?, ?, 'success')")
            ->execute([$userId, "You completed '{$quiz['title']}' quiz and earned {$pointsAwarded} pts."]);
        $message = "Quiz submitted! Score: {$score}/".count($questions)." (+{$pointsAwarded} pts)";
    }
}

if ($quizId && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quizStmt->execute([$quizId]);
    $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

    if ($quiz) {
        $questionsStmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
        $questionsStmt->execute([$quizId]);
        $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);
        $showQuiz = true;
    }
}

$totalScoreStmt = $pdo->prepare("SELECT COALESCE(SUM(points_awarded), 0) FROM quiz_attempts WHERE user_id = ?");
$totalScoreStmt->execute([$userId]);
$totalScore = $totalScoreStmt->fetchColumn();

$userLevel = 1; // This should be calculated based on user points, but for now using a simple calculation
$userPointsStmt = $pdo->prepare("SELECT points FROM users WHERE id = ?");
$userPointsStmt->execute([$userId]);
$userPoints = $userPointsStmt->fetchColumn();
if ($userPoints >= 5000) $userLevel = 10;
elseif ($userPoints >= 4000) $userLevel = 9;
elseif ($userPoints >= 3000) $userLevel = 8;
elseif ($userPoints >= 2500) $userLevel = 7;
elseif ($userPoints >= 2000) $userLevel = 6;
elseif ($userPoints >= 1500) $userLevel = 5;
elseif ($userPoints >= 1000) $userLevel = 4;
elseif ($userPoints >= 500) $userLevel = 3;
elseif ($userPoints >= 200) $userLevel = 2;

$quizzesStmt = $pdo->prepare("
    SELECT q.*,
           COUNT(qq.id) as question_count,
           COALESCE(MAX(qa.score), 0) as last_score
    FROM quizzes q
    LEFT JOIN quiz_questions qq ON qq.quiz_id = q.id
    LEFT JOIN quiz_attempts qa ON qa.quiz_id = q.id AND qa.user_id = ?
    GROUP BY q.id
    ORDER BY q.id DESC
");
$quizzesStmt->execute([$userId]);
$quizzes = $quizzesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "partials/header.php"; ?>
<?php include "partials/sidebar.php"; ?>
<?php include "partials/member-header.php"; ?>

<main class="ml-64 px-10 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Eco-Knowledge Quizzes</h1>
            <p class="text-slate-600">Test your eco knowledge and earn points</p>
        </div>
        <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-lg font-bold">
            Total Score: <?= number_format($totalScore) ?>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($showQuiz && $quiz): ?>
        <div class="glass rounded-3xl p-8 shadow-xl mb-8">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900"><?= htmlspecialchars($quiz['title']) ?></h2>
                    <p class="text-sm text-slate-500">Difficulty: <?= htmlspecialchars($quiz['difficulty']) ?> • Reward: <?= $quiz['points_reward'] ?> pts</p>
                </div>
                <a href="quiz.php" class="text-emerald-600 font-semibold hover:underline text-sm">Back</a>
            </div>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                <?php foreach ($questions as $idx => $q): ?>
                    <div class="bg-white border border-slate-100 rounded-xl p-4">
                        <p class="font-bold text-slate-800 mb-3"><?= $idx+1 ?>. <?= htmlspecialchars($q['question']) ?></p>
                        <?php foreach (['A','B','C','D'] as $opt): ?>
                            <?php $field = strtolower($opt); ?>
                            <label class="flex items-center gap-3 text-sm text-slate-700 mb-2 cursor-pointer">
                                <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $opt ?>" required class="text-emerald-600">
                                <span><?= htmlspecialchars($q['option_'.$field]) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <button type="submit"
                        class="w-full bg-emerald-600 text-white py-3 rounded-xl font-extrabold hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1">
                    Submit Quiz
                </button>
            </form>
        </div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php foreach ($quizzes as $quizItem): ?>
            <?php
            $isLocked = isset($quizItem['unlock_level']) && $userLevel < $quizItem['unlock_level'];
            $cardClass = $isLocked ? 'bg-gray-100 border-gray-200 opacity-60' : 'bg-white border-gray-100 hover:shadow-md';
            ?>
            <div class="<?= $cardClass ?> p-6 rounded-xl shadow-sm border flex justify-between items-center transition">
                <div class="flex gap-4 items-center">
                    <div class="bg-emerald-50 p-4 rounded-lg text-emerald-600 text-2xl"><i class="fas fa-question"></i></div>
                    <div>
                        <h4 class="font-bold text-lg text-slate-800 <?= $isLocked ? 'text-gray-500' : '' ?>"><?= htmlspecialchars($quizItem['title']) ?></h4>
                        <p class="text-sm text-slate-500">
                            Difficulty: <span class="text-emerald-600 font-bold"><?= htmlspecialchars($quizItem['difficulty']) ?></span> •
                            <?= $quizItem['question_count'] ?> questions •
                            Reward: <?= $quizItem['points_reward'] ?> pts
                        </p>
                        <?php if ($isLocked): ?>
                            <p class="text-xs text-gray-500 mt-1">Unlock at Level <?= $quizItem['unlock_level'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <?php if ($quizItem['last_score'] && !$isLocked): ?>
                        <span class="text-sm text-slate-500">Last score: <?= $quizItem['last_score'] ?>/<?= $quizItem['question_count'] ?></span>
                    <?php endif; ?>
                    <?php if ($isLocked): ?>
                        <span class="text-gray-500 font-semibold">Locked</span>
                    <?php else: ?>
                        <a href="quiz.php?quiz=<?= $quizItem['id'] ?>" class="bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-emerald-700">Start Quiz</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
