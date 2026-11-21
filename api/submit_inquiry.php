<?php
require_once __DIR__ . '/../config/bootstrap.php';
verify_post_csrf();

$back = app_url() . '#brief';
if (!empty($_POST['website'])) redirect(app_url() . '?brief=success#brief');
if (!empty($_SESSION['last_lead_at']) && time() - (int)$_SESSION['last_lead_at'] < 15) {
    redirect(app_url() . '?brief=error#brief');
}

$name = trim((string)($_POST['name'] ?? ''));
$email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$phone = trim((string)($_POST['phone'] ?? ''));
$company = trim((string)($_POST['company'] ?? ''));
$serviceId = filter_var($_POST['service_id'] ?? '', FILTER_VALIDATE_INT) ?: null;
$goal = trim((string)($_POST['goal'] ?? ''));
$targetAudience = trim((string)($_POST['target_audience'] ?? ''));
$scope = trim((string)($_POST['scope'] ?? ''));
$budget = trim((string)($_POST['budget_range'] ?? ''));
$deadline = trim((string)($_POST['deadline'] ?? ''));
$referenceUrl = trim((string)($_POST['reference_url'] ?? ''));
$briefConfirmed = isset($_POST['brief_confirm']) && $_POST['brief_confirm'] === 'on';

if ($deadline !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $deadline);
    if (!$date || $date->format('Y-m-d') !== $deadline) $deadline = null;
} else {
    $deadline = null;
}
$phoneDigits = preg_replace('/\D/', '', $phone);

if (!$briefConfirmed || mb_strlen($name) < 2 || mb_strlen($name) > 120 || !$email || mb_strlen($phoneDigits) < 8 || mb_strlen($phoneDigits) > 30 || mb_strlen($scope) < 10 || mb_strlen($scope) > 5000 || $serviceId === null || $budget === '' || mb_strlen($goal) < 3 || $deadline === null || mb_strlen($targetAudience) < 3) {
    redirect(app_url() . '?brief=error#brief');
}
if ($serviceId !== null) {
    $check = $pdo->prepare("SELECT id FROM services WHERE id = :id AND status = 'published' LIMIT 1");
    $check->execute(['id' => $serviceId]);
    if (!$check->fetchColumn()) $serviceId = null;
}
if ($referenceUrl !== '' && !safe_http_url($referenceUrl)) {
    redirect(app_url() . '?brief=error#brief');
}

try {
    $insert = $pdo->prepare('INSERT INTO leads (service_id, name, email, phone, company, goal, target_audience, scope, budget_range, deadline, reference_url, status) VALUES (:service_id, :name, :email, :phone, :company, :goal, :target_audience, :scope, :budget_range, :deadline, :reference_url, \'New\')');
    $insert->execute([
        'service_id' => $serviceId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'company' => $company !== '' ? $company : null,
        'goal' => $goal !== '' ? $goal : null,
        'target_audience' => $targetAudience !== '' ? $targetAudience : null,
        'scope' => $scope,
        'budget_range' => $budget !== '' ? $budget : null,
        'deadline' => $deadline,
        'reference_url' => $referenceUrl !== '' ? $referenceUrl : null,
    ]);
    $_SESSION['last_lead_at'] = time();
    redirect(app_url() . '?brief=success#brief');
} catch (PDOException $exception) {
    error_log('[LEAD SUBMIT] ' . $exception->getMessage());
    redirect(app_url() . '?brief=error#brief');
}
