<?php
include 'includes/db.php';

session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT 
            r.id, 
            p.nom AS patient_nom, 
            m.nom AS medecin_nom, 
            r.date, 
            r.heure_debut
        FROM rendez_vous r
        JOIN patient p ON r.id_patient = p.id
        JOIN medecins m ON r.id_medecin = m.id
        ORDER BY r.date, r.heure_debut
    ");

    $first = true;
    echo "[";

    while ($rdv = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($rdv['date']) && !empty($rdv['heure_debut'])) {
            // ⚠️ conversion : heure_debut est un INT, donc on le convertit en "HH:00"
            $heure = str_pad($rdv['heure_debut'], 2, "0", STR_PAD_LEFT) . ":00";

            $start = (new DateTime($rdv['date'] . ' ' . $heure))->format('c');
            $end = (new DateTime($rdv['date'] . ' ' . $heure))->add(new DateInterval('PT30M'))->format('c');

            if (!$first) echo ",";
            echo json_encode([
                'id'    => $rdv['id'],
                'title' => "{$rdv['patient_nom']} - {$rdv['medecin_nom']}",
                'start' => $start,
                'end'   => $end
            ], JSON_UNESCAPED_UNICODE);
            $first = false;
        }
    }

    echo "]";

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => "Erreur : " . $e->getMessage()]);
}
