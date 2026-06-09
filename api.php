<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Router
switch($action) {
    case 'register_pharmacy':
        if($method == 'POST') handleRegisterPharmacy($pdo);
        break;
    case 'login_partner':
        if($method == 'POST') handleLoginPartner($pdo);
        break;
    case 'logout_partner':
        if($method == 'POST') handleLogoutPartner($pdo);
        break;
    case 'add_medicament':
        if($method == 'POST') handleAddMedicament($pdo);
        break;
    case 'get_stock':
        if($method == 'GET') handleGetStock($pdo);
        break;
    case 'delete_medicament':
        if($method == 'DELETE') handleDeleteMedicament($pdo);
        break;
    case 'search_pharmacies':
        if($method == 'POST') handleSearchPharmacies($pdo);
        break;
    case 'get_all_pharmacies':
        if($method == 'GET') handleGetAllPharmacies($pdo);
        break;
    case 'delete_pharmacy':
        if($method == 'DELETE') handleDeletePharmacy($pdo);
        break;
    case 'save_client_location':
        if($method == 'POST') handleSaveClientLocation($pdo);
        break;
    case 'get_pharmacy_details':
        if($method == 'GET') handleGetPharmacyDetails($pdo);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
}
?>

<?php
function handleRegisterPharmacy($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $check = $pdo->prepare("SELECT id FROM pharmacies WHERE matricule = ?");
    $check->execute([$data['matricule']]);
    if($check->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Ce matricule existe déjà']);
        return;
    }
    
    $hashedPassword = password_hash($data['codePerso'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO pharmacies (nom_responsable, prenom_responsable, nom_pharmacie, latitude, longitude, matricule, code_personnel) 
            VALUES (:nom, :prenom, :nom_pharmacie, :lat, :lng, :matricule, :code)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':nom_pharmacie' => $data['nomPharmacie'],
        ':lat' => $data['lat'],
        ':lng' => $data['lng'],
        ':matricule' => $data['matricule'],
        ':code' => $hashedPassword
    ]);
    
    if($result) {
        echo json_encode(['success' => true, 'message' => 'Pharmacie enregistrée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
    }
}

function handleLoginPartner($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "SELECT id, nom_pharmacie, prenom_responsable, nom_responsable, code_personnel, latitude, longitude 
            FROM pharmacies WHERE matricule = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['matricule']]);
    $pharmacy = $stmt->fetch();
    
    if($pharmacy && password_verify($data['codePerso'], $pharmacy['code_personnel'])) {
        $token = generateToken();
        
        $pdo->prepare("DELETE FROM sessions_partenaire WHERE pharmacie_id = ?")->execute([$pharmacy['id']]);
        
        $pdo->prepare("INSERT INTO sessions_partenaire (pharmacie_id, token) VALUES (?, ?)")->execute([$pharmacy['id'], $token]);
        
        $_SESSION['partner_token'] = $token;
        $_SESSION['partner_id'] = $pharmacy['id'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Connexion réussie',
            'data' => [
                'id' => $pharmacy['id'],
                'nomPharmacie' => $pharmacy['nom_pharmacie'],
                'prenom' => $pharmacy['prenom_responsable'],
                'nom' => $pharmacy['nom_responsable'],
                'lat' => $pharmacy['latitude'],
                'lng' => $pharmacy['longitude'],
                'token' => $token
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Matricule ou code personnel incorrect']);
    }
}

function handleLogoutPartner($pdo) {
    if(isset($_SESSION['partner_token'])) {
        $pdo->prepare("DELETE FROM sessions_partenaire WHERE token = ?")->execute([$_SESSION['partner_token']]);
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Non connecté']);
    }
}

function handleAddMedicament($pdo) {
    if(!isPartnerLogged()) {
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $pharmacie_id = $_SESSION['partner_id'];
    
    $sql = "INSERT INTO medicaments (pharmacie_id, nom_medicament, prix, quantite) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$pharmacie_id, $data['nom'], $data['prix'], $data['quantite'] ?? 0]);
    
    if($result) {
        echo json_encode(['success' => true, 'message' => 'Médicament ajouté', 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
    }
}

function handleGetStock($pdo) {
    if(!isPartnerLogged()) {
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        return;
    }
    
    $pharmacie_id = $_SESSION['partner_id'];
    $sql = "SELECT id, nom_medicament, prix, quantite, date_ajout FROM medicaments WHERE pharmacie_id = ? ORDER BY date_ajout DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pharmacie_id]);
    $medicaments = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $medicaments]);
}

function handleDeleteMedicament($pdo) {
    if(!isPartnerLogged()) {
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "DELETE FROM medicaments WHERE id = ? AND pharmacie_id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$data['medicament_id'], $_SESSION['partner_id']]);
    
    echo json_encode(['success' => $result, 'message' => $result ? 'Médicament supprimé' : 'Erreur lors de la suppression']);
}

function handleSearchPharmacies($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $client_lat = $data['lat'];
    $client_lng = $data['lng'];
    
    $sql = "SELECT *, 
            (6371 * acos(cos(radians(:lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(:lng)) + sin(radians(:lat)) * sin(radians(latitude)))) AS distance 
            FROM pharmacies 
            HAVING distance < 50 
            ORDER BY distance LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':lat' => $client_lat, ':lng' => $client_lng]);
    $pharmacies = $stmt->fetchAll();
    
    foreach($pharmacies as &$pharmacy) {
        $meds = $pdo->prepare("SELECT nom_medicament, prix, quantite FROM medicaments WHERE pharmacie_id = ? LIMIT 5");
        $meds->execute([$pharmacy['id']]);
        $pharmacy['medicaments'] = $meds->fetchAll();
    }
    
    echo json_encode(['success' => true, 'data' => $pharmacies]);
}

function handleGetAllPharmacies($pdo) {
    $sql = "SELECT p.*, COUNT(m.id) as nb_medicaments 
            FROM pharmacies p 
            LEFT JOIN medicaments m ON p.id = m.pharmacie_id 
            GROUP BY p.id 
            ORDER BY p.date_inscription DESC";
    $stmt = $pdo->query($sql);
    $pharmacies = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $pharmacies]);
}

function handleDeletePharmacy($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "DELETE FROM pharmacies WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$data['pharmacy_id']]);
    
    echo json_encode(['success' => $result, 'message' => $result ? 'Pharmacie supprimée' : 'Erreur lors de la suppression']);
}

function handleSaveClientLocation($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO clients (nom, prenom, latitude, longitude) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$data['nom'], $data['prenom'], $data['lat'], $data['lng']]);
    
    echo json_encode(['success' => $result]);
}

function handleGetPharmacyDetails($pdo) {
    $pharmacy_id = $_GET['id'] ?? 0;
    
    $sql = "SELECT * FROM pharmacies WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pharmacy_id]);
    $pharmacy = $stmt->fetch();
    
    if($pharmacy) {
        $meds = $pdo->prepare("SELECT * FROM medicaments WHERE pharmacie_id = ?");
        $meds->execute([$pharmacy_id]);
        $pharmacy['medicaments'] = $meds->fetchAll();
    }
    
    echo json_encode(['success' => true, 'data' => $pharmacy]);
}
?>