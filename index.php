<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaFind — Senegal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --bg:        #0b0f1a;
            --surface:   #121826;
            --surface2:  #1a2133;
            --border:    #1f2d45;
            --accent:    #00d4aa;
            --accent2:   #ff6b35;
            --accent3:   #7c5cfc;
            --text:      #e8edf5;
            --muted:     #6b7a99;
            --danger:    #ff4d6d;
            --success:   #00d4aa;
            --warning:   #f59e0b;
            --radius:    14px;
            --radius-sm: 8px;
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(0,212,170,.07) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124,92,252,.07) 0%, transparent 50%),
                linear-gradient(rgba(255,255,255,.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.015) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
            pointer-events: none; z-index: 0;
        }

        .wrapper { position: relative; z-index: 1; max-width: 1300px; margin: 0 auto; padding: 24px 20px 60px; }


        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 28px;
            background: rgba(18,24,38,.85);
            border: 1px solid var(--border);
            border-radius: 18px;
            backdrop-filter: blur(20px);
            margin-bottom: 36px;
        }
        .logo { display: flex; align-items: center; gap: 10px; font-family: 'Space Mono', monospace; font-size: 1.2rem; font-weight: 700; }
        .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), var(--accent3)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .logo span { color: var(--accent); }
        .nav-badge { background: linear-gradient(135deg, var(--accent), #00a882); color: #0b1a15; padding: 6px 16px; border-radius: 30px; font-size: .75rem; font-weight: 700; letter-spacing: .5px; }

    
        .tabs { display: flex; gap: 8px; background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 6px; margin-bottom: 32px; }
        .tab-btn { flex: 1; padding: 13px 10px; background: transparent; border: none; border-radius: 11px; color: var(--muted); font-family: 'Sora', sans-serif; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .25s; display: flex; align-items: center; justify-content: center; gap: 7px; }
        .tab-btn.active { background: var(--surface2); color: var(--text); box-shadow: 0 2px 12px rgba(0,0,0,.3); }
        .tab-btn.active[data-tab="partner"] { color: var(--accent); }
        .tab-btn.active[data-tab="client"]  { color: var(--accent2); }
        .tab-btn.active[data-tab="admin"]   { color: var(--accent3); }

        .panel { display: none; }
        .panel.active { display: block; animation: fadeUp .3s ease-out; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px; margin-bottom: 20px; }
        .card-title { font-size: 1rem; font-weight: 700; letter-spacing: .3px; margin-bottom: 20px; display: flex; align-items: center; gap: 9px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
        .card-title-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
        .icon-green  { background: rgba(0,212,170,.15); }
        .icon-orange { background: rgba(255,107,53,.15); }
        .icon-purple { background: rgba(124,92,252,.15); }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { margin-bottom: 16px; }
        .form-group.full { grid-column: 1 / -1; }
        label { display: block; font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: 6px; letter-spacing: .4px; text-transform: uppercase; }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%; background: var(--surface2); border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            padding: 11px 14px; color: var(--text); font-family: 'Sora', sans-serif; font-size: .88rem;
            transition: border-color .2s, box-shadow .2s; outline: none;
        }
        input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(0,212,170,.12); }
        input[readonly] { opacity: .6; cursor: default; }
        input::placeholder { color: var(--muted); }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 22px; border: none; border-radius: var(--radius-sm); font-family: 'Sora', sans-serif; font-size: .88rem; font-weight: 600; cursor: pointer; transition: all .2s; width: 100%; }
        .btn-primary { background: var(--accent); color: #051a14; }
        .btn-primary:hover { background: #00f0c2; transform: translateY(-1px); }
        .btn-orange { background: var(--accent2); color: #fff; }
        .btn-orange:hover { background: #ff8255; transform: translateY(-1px); }
        .btn-purple { background: var(--accent3); color: #fff; }
        .btn-purple:hover { background: #9b80ff; transform: translateY(-1px); }
        .btn-ghost { background: var(--surface2); color: var(--muted); border: 1.5px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger { background: rgba(255,77,109,.15); color: var(--danger); border: 1px solid rgba(255,77,109,.25); padding: 6px 12px; width: auto; font-size: .78rem; }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 8px 14px; font-size: .8rem; width: auto; }

        .map-container { border-radius: var(--radius-sm); overflow: hidden; border: 1.5px solid var(--border); margin-bottom: 8px; }
        .leaflet-map { height: 260px; width: 100%; display: block; border-radius: var(--radius-sm); }
        .map-hint { font-size: .72rem; color: var(--muted); margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }

        .leaflet-popup-content-wrapper { background: var(--surface2); color: var(--text); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.5); }
        .leaflet-popup-tip { background: var(--surface2); }
        .leaflet-popup-content { font-family: 'Sora', sans-serif; font-size: .82rem; line-height: 1.5; }
        .leaflet-control-zoom a { background: var(--surface2); color: var(--text); border-color: var(--border); }
        .leaflet-control-zoom a:hover { background: var(--surface); }
        .leaflet-control-attribution { background: rgba(11,15,26,.8); color: var(--muted); font-size: .6rem; }

        .stock-list { max-height: 280px; overflow-y: auto; margin-top: 12px; }
        .stock-list::-webkit-scrollbar { width: 4px; }
        .stock-list::-webkit-scrollbar-track { background: var(--surface2); }
        .stock-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .med-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); margin-bottom: 8px; transition: border-color .2s; }
        .med-item:hover { border-color: var(--accent); }
        .med-info strong { font-size: .88rem; display: block; margin-bottom: 3px; }
        .med-info small { font-size: .75rem; color: var(--muted); }
        .med-price { font-family: 'Space Mono', monospace; font-size: .82rem; color: var(--accent); font-weight: 700; }

        .pharmacy-card { background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 12px; cursor: pointer; transition: all .25s; position: relative; overflow: hidden; }
        .pharmacy-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--accent2); opacity: 0; transition: opacity .2s; }
        .pharmacy-card:hover { border-color: var(--accent2); transform: translateX(3px); }
        .pharmacy-card:hover::before { opacity: 1; }
        .pharmacy-card.nearest { border-color: var(--accent); }
        .pharmacy-card.nearest::before { background: var(--accent); opacity: 1; }
        .pharm-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .pharm-name { font-weight: 700; font-size: .92rem; }
        .pharm-name small { display: block; font-weight: 400; color: var(--muted); font-size: .75rem; margin-top: 2px; }
        .distance-badge { background: rgba(0,212,170,.12); color: var(--accent); border: 1px solid rgba(0,212,170,.2); padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; font-family: 'Space Mono', monospace; white-space: nowrap; }
        .pharm-meds { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .med-tag { background: var(--surface); border: 1px solid var(--border); padding: 3px 9px; border-radius: 20px; font-size: .7rem; color: var(--muted); }

        .admin-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .admin-table th { text-align: left; padding: 10px 14px; color: var(--muted); font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border); }
        .admin-table td { padding: 12px 14px; border-bottom: 1px solid rgba(31,45,69,.5); vertical-align: middle; }
        .admin-table tr:hover td { background: rgba(255,255,255,.02); }
        .count-badge { background: rgba(124,92,252,.15); color: var(--accent3); padding: 3px 9px; border-radius: 20px; font-size: .72rem; font-weight: 700; }

        #alertContainer { position: fixed; top: 20px; right: 20px; z-index: 9999; width: 320px; }
        .alert { padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 10px; font-size: .84rem; font-weight: 500; animation: slideRight .3s ease-out; display: flex; align-items: center; gap: 10px; backdrop-filter: blur(12px); }
        @keyframes slideRight { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }
        .alert-success { background: rgba(0,212,170,.15); border: 1px solid rgba(0,212,170,.3); color: #00d4aa; }
        .alert-error   { background: rgba(255,77,109,.15); border: 1px solid rgba(255,77,109,.3); color: #ff4d6d; }
        .alert-info    { background: rgba(124,92,252,.15); border: 1px solid rgba(124,92,252,.3); color: #a78bfa; }

        .partner-info-bar { background: linear-gradient(135deg, rgba(0,212,170,.1), rgba(0,212,170,.03)); border: 1px solid rgba(0,212,170,.2); border-radius: var(--radius-sm); padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .partner-info-bar strong { font-size: .92rem; color: var(--accent); }
        .partner-info-bar small  { font-size: .75rem; color: var(--muted); display: block; margin-top: 2px; }

        .auth-toggle { display: flex; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; padding: 4px; margin-bottom: 24px; }
        .auth-toggle button { flex: 1; padding: 9px; background: transparent; border: none; border-radius: 7px; color: var(--muted); font-family: 'Sora', sans-serif; font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .2s; }
        .auth-toggle button.active { background: var(--surface); color: var(--accent); box-shadow: 0 2px 8px rgba(0,0,0,.3); }

        .results-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
        .results-count { font-size: .8rem; color: var(--muted); display: flex; align-items: center; gap: 8px; }
        .results-count strong { color: var(--accent); }

        #mapModal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(6px); }
        #mapModal.open { display: flex; }
        .modal-inner { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; padding: 24px; width: min(760px, 95vw); max-height: 92vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .modal-title { font-weight: 700; font-size: 1rem; }
        .modal-close { width: 32px; height: 32px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; color: var(--muted); font-size: 1rem; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .modal-close:hover { color: var(--text); border-color: var(--muted); }
        #resultsMap { height: 380px; border-radius: 10px; border: 1px solid var(--border); }

        .empty-state { text-align: center; padding: 32px 16px; color: var(--muted); }
        .empty-state .es-icon { font-size: 2.5rem; margin-bottom: 8px; }
        .empty-state p { font-size: .82rem; }

        .gps-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(0,212,170,.1); border: 1px solid rgba(0,212,170,.2); color: var(--accent); padding: 5px 12px; border-radius: 20px; font-size: .72rem; font-weight: 600; margin-bottom: 10px; }

        @media (max-width: 700px) {
            .tabs { flex-wrap: wrap; }
            .form-row { grid-template-columns: 1fr; }
            .results-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<div id="alertContainer"></div>

<div id="mapModal">
    <div class="modal-inner">
        <div class="modal-header">
            <div class="modal-title">📍 Pharmacies à proximité — Vue carte</div>
            <button class="modal-close" onclick="closeMapModal()">✕</button>
        </div>
        <div id="resultsMap"></div>
        <div id="resultsMapLegend" style="margin-top:14px; display:flex; gap:16px; flex-wrap:wrap; font-size:.75rem; color:var(--muted);">
            <span>🟠 Votre position</span>
            <span style="color:var(--accent);">🟢 Pharmacie la plus proche</span>
            <span style="color:var(--accent3);">🟣 Autres pharmacies</span>
        </div>
    </div>
</div>

<div class="wrapper">


    <nav>
        <div class="logo">
            <div class="logo-icon">💊</div>
            Pharma<span>BECH</span>
        </div>
        <div class="nav-badge">pharma class</div>
    </nav>

    <div class="tabs">
        <button class="tab-btn active" data-tab="partner" onclick="switchTab('partner')">
            <span>🤝</span> Espace Partenaire
        </button>
        <button class="tab-btn" data-tab="client" onclick="switchTab('client')">
            <span>🔍</span> Recherche Client
        </button>
        <button class="tab-btn" data-tab="admin" onclick="switchTab('admin')">
            <span>👑</span> Administration
        </button>
    </div>

    <div id="panel-partner" class="panel active">

        <div class="auth-toggle" id="authToggle">
            <button id="toggleLogin" class="active" onclick="showAuthSection('login')">🔐 Connexion</button>
            <button id="toggleRegister" onclick="showAuthSection('register')">📝 Inscription</button>
        </div>

        <div id="loginSection" class="card">
            <div class="card-title">
                <div class="card-title-icon icon-green">🔐</div>
                Connexion partenaire
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Matricule</label>
                    <input type="text" id="loginMatricule" placeholder="Ex: PHARM001">
                </div>
                <div class="form-group">
                    <label>Code personnel</label>
                    <input type="password" id="loginCode" placeholder="••••••••">
                </div>
            </div>
            <button class="btn btn-primary" onclick="partnerLogin()">🔓 Se connecter</button>
        </div>

        <div id="registerSection" class="card" style="display:none;">
            <div class="card-title">
                <div class="card-title-icon icon-green">📝</div>
                Inscription d'une nouvelle pharmacie
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nom du responsable</label>
                    <input type="text" id="respNom" placeholder="Votre nom">
                </div>
                <div class="form-group">
                    <label>Prénom du responsable</label>
                    <input type="text" id="respPrenom" placeholder="Votre prénom">
                </div>
                <div class="form-group full">
                    <label>Nom de la pharmacie</label>
                    <input type="text" id="pharmNom" placeholder="Ex: Pharmacie Centrale İstanbul">
                </div>
                <div class="form-group">
                    <label>Matricule</label>
                    <input type="text" id="matricule" placeholder="Ex: PHARM001">
                </div>
                <div class="form-group">
                    <label>Code personnel</label>
                    <input type="password" id="codePerso" placeholder="••••••••">
                </div>
            </div>
            <div class="form-group">
                <label>📍 Position de votre pharmacie</label>
                <div class="map-hint">📌 Cliquez sur la carte pour placer votre pharmacie</div>
                <div class="map-container">
                    <div id="partnerMap" class="leaflet-map"></div>
                </div>
                <input type="text" id="partnerPosition" readonly placeholder="Aucune position sélectionnée" style="margin-top:8px;">
            </div>
            <button class="btn btn-primary" onclick="registerPharmacy()">✅ Enregistrer ma pharmacie</button>
        </div>

        <div id="partnerDashboard" style="display:none;">
            <div class="partner-info-bar">
                <div>
                    <strong id="dashPharmName">—</strong>
                    <small id="dashPharmInfo">—</small>
                </div>
                <button class="btn btn-ghost btn-sm" onclick="logoutPartner()">🚪 Déconnexion</button>
            </div>

            <div class="card">
                <div class="card-title">
                    <div class="card-title-icon icon-green">➕</div>
                    Ajouter un médicament
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label>Nom du médicament</label>
                        <input type="text" id="medicamentNom" placeholder="Ex: Paracétamol 500mg">
                    </div>
                    <div class="form-group">
                        <label>Prix (₺ TL)</label>
                        <input type="number" id="medicamentPrix" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>Quantité en stock</label>
                        <input type="number" id="medicamentQuantite" value="1" min="0">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addMedicament()">💊 Ajouter au stock</button>
            </div>

            <div class="card">
                <div class="card-title">
                    <div class="card-title-icon icon-green">📦</div>
                    Stock actuel
                    <span id="stockCount" style="font-family:'Space Mono',monospace;font-size:.75rem;color:var(--accent);margin-left:auto;background:rgba(0,212,170,.12);padding:3px 10px;border-radius:20px;">0</span>
                </div>
                <div id="stockList">
                    <div class="empty-state"><div class="es-icon">📦</div><p>Aucun médicament en stock</p></div>
                </div>
            </div>
        </div>
    </div>

    <div id="panel-client" class="panel">
        <div class="card">
            <div class="card-title">
                <div class="card-title-icon icon-orange">👤</div>
                Vos informations
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" id="clientNom" placeholder="Votre nom">
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" id="clientPrenom" placeholder="Votre prénom">
                </div>
                <div class="form-group full">
                    <label>💊 Médicament recherché <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:.7rem;">(facultatif — les fautes sont tolérées)</span></label>
                    <input type="text" id="clientMedicament" placeholder="Ex: paracétamol, amoxiciline, doliprane…">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <div class="card-title-icon icon-orange">📍</div>
                Votre position
            </div>
            <div class="map-hint">📌 Cliquez sur la carte ou utilisez votre GPS</div>
            <div class="map-container">
                <div id="clientMap" class="leaflet-map"></div>
            </div>
            <input type="text" id="clientPosition" readonly placeholder="Aucune position sélectionnée" style="margin:10px 0 14px;">
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <button class="btn btn-ghost" style="flex:1;" onclick="useGPSLocation()">📡 Ma position GPS</button>
                <button class="btn btn-orange" style="flex:2;" onclick="findNearestPharmacy()">🏥 Chercher les pharmacies proches</button>
            </div>
        </div>

        <div id="clientResults" style="display:none;">
            <div class="results-header">
                <div class="card-title" style="margin:0;border:none;padding:0;">
                    <div class="card-title-icon icon-orange">🏥</div>
                    Pharmacies trouvées
                </div>
                <div class="results-count">
                    <strong id="resultCount">0</strong> résultat(s)
                    <button class="btn btn-ghost btn-sm" onclick="showResultsOnMap()">🗺️ Voir sur la carte</button>
                </div>
            </div>
            <div id="nearestResult"></div>
        </div>
    </div>

    <div id="panel-admin" class="panel">
        <div id="adminLoginCard" class="card">
            <div class="card-title">
                <div class="card-title-icon icon-purple">👑</div>
                Accès administrateur
            </div>
            <div class="form-group">
                <label>Code administrateur</label>
                <input type="password" id="adminCode" placeholder="••••••••">
            </div>
            <button class="btn btn-purple" onclick="adminLogin()">🔓 Accéder au panneau</button>
        </div>

        <div id="adminPanel" style="display:none;">
            <div class="card">
                <div class="card-title">
                    <div class="card-title-icon icon-purple">🏥</div>
                    Toutes les pharmacies
                    <span id="totalPharmacies" style="font-family:'Space Mono',monospace;font-size:.75rem;color:var(--accent3);margin-left:auto;background:rgba(124,92,252,.12);padding:3px 10px;border-radius:20px;">0</span>
                    <button class="btn btn-ghost btn-sm" onclick="refreshAdminList()">🔄 Actualiser</button>
                    <button class="btn btn-ghost btn-sm" onclick="logoutAdmin()">Déconnexion</button>
                </div>
                <div id="allPharmaciesList">
                    <div class="empty-state"><div class="es-icon">⏳</div><p>Chargement…</p></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const API_URL = 'api.php';
let partnerMapL, clientMapL, resultsMapL;
let partnerMarkerL, clientMarkerL;
let partnerLatLng = null, clientLatLng = null;
let currentPartner = null;
let isAdminLogged = false;
let searchResults = [];

function makeIcon(color) {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">
        <path d="M14 0C7 0 2 6 2 12c0 8 12 24 12 24S26 20 26 12C26 6 21 0 14 0z" fill="${color}" stroke="rgba(0,0,0,.3)" stroke-width="1"/>
        <circle cx="14" cy="12" r="5" fill="rgba(0,0,0,.4)"/>
    </svg>`;
    return L.icon({ iconUrl: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg), iconSize: [28,36], iconAnchor: [14,36], popupAnchor: [0,-36] });
}

function initMaps() {
    const saintLouis = [16.0186, -16.4897];
    const tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
    const tileAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>';

    partnerMapL = L.map('partnerMap', { zoomControl: true }).setView(saintLouis, 14);
    L.tileLayer(tileUrl, { attribution: tileAttr, subdomains: 'abcd', maxZoom: 19 }).addTo(partnerMapL);
    partnerMarkerL = L.marker(saintLouis, { icon: makeIcon('#00d4aa'), draggable: true }).addTo(partnerMapL);

    partnerMarkerL.on('dragend', function() {
        const p = partnerMarkerL.getLatLng();
        partnerLatLng = { lat: p.lat, lng: p.lng };
        document.getElementById('partnerPosition').value = `${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
    });
    partnerMapL.on('click', function(e) {
        partnerMarkerL.setLatLng(e.latlng);
        partnerLatLng = { lat: e.latlng.lat, lng: e.latlng.lng };
        document.getElementById('partnerPosition').value = `${e.latlng.lat.toFixed(5)}, ${e.latlng.lng.toFixed(5)}`;
    });

    clientMapL = L.map('clientMap', { zoomControl: true }).setView(saintLouis, 14);
    L.tileLayer(tileUrl, { attribution: tileAttr, subdomains: 'abcd', maxZoom: 19 }).addTo(clientMapL);
    clientMarkerL = L.marker(saintLouis, { icon: makeIcon('#ff6b35'), draggable: true }).addTo(clientMapL);

    clientMarkerL.on('dragend', function() {
        const p = clientMarkerL.getLatLng();
        clientLatLng = { lat: p.lat, lng: p.lng };
        document.getElementById('clientPosition').value = `${p.lat.toFixed(5)}, ${p.lng.toFixed(5)}`;
    });
    clientMapL.on('click', function(e) {
        clientMarkerL.setLatLng(e.latlng);
        clientLatLng = { lat: e.latlng.lat, lng: e.latlng.lng };
        document.getElementById('clientPosition').value = `${e.latlng.lat.toFixed(5)}, ${e.latlng.lng.toFixed(5)}`;
    });
}

window.addEventListener('load', initMaps);

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    document.getElementById(`panel-${tab}`).classList.add('active');
    setTimeout(() => {
        if (tab === 'partner' && partnerMapL) partnerMapL.invalidateSize();
        if (tab === 'client'  && clientMapL)  clientMapL.invalidateSize();
    }, 100);
}

function showAuthSection(section) {
    const isLogin = section === 'login';
    document.getElementById('loginSection').style.display    = isLogin  ? 'block' : 'none';
    document.getElementById('registerSection').style.display = !isLogin ? 'block' : 'none';
    document.getElementById('toggleLogin').classList.toggle('active', isLogin);
    document.getElementById('toggleRegister').classList.toggle('active', !isLogin);

    if (!isLogin && partnerMapL) setTimeout(() => partnerMapL.invalidateSize(), 100);
}

function showAlert(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type}`;
    const icons = { success:'✅', error:'❌', info:'ℹ️' };
    el.innerHTML = `<span>${icons[type]||'•'}</span> ${msg}`;
    document.getElementById('alertContainer').appendChild(el);
    setTimeout(() => el.remove(), 4500);
}

async function callAPI(action, method, data = null) {
    const url = `${API_URL}?action=${action}`;
    const opts = { method, headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' };
    if (data && (method === 'POST' || method === 'DELETE')) opts.body = JSON.stringify(data);
    try {
        const r = await fetch(url, opts);
        return await r.json();
    } catch(e) {
        showAlert('Erreur réseau — vérifiez le serveur', 'error');
        return { success: false };
    }
}

function useGPSLocation() {
    if (!navigator.geolocation) { showAlert('Géolocalisation non supportée par votre navigateur', 'error'); return; }
    showAlert('Récupération de la position GPS…', 'info');
    navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude, lng = pos.coords.longitude;
        clientLatLng = { lat, lng };
        document.getElementById('clientPosition').value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        if (clientMapL) {
            clientMapL.setView([lat, lng], 15);
            clientMarkerL.setLatLng([lat, lng]);
        }
        showAlert('Position GPS détectée !', 'success');
    }, () => showAlert('Impossible de récupérer la position GPS', 'error'));
}

async function registerPharmacy() {
    const nom = document.getElementById('respNom').value.trim();
    const prenom = document.getElementById('respPrenom').value.trim();
    const nomPharmacie = document.getElementById('pharmNom').value.trim();
    const matricule = document.getElementById('matricule').value.trim();
    const codePerso = document.getElementById('codePerso').value;
    if (!nom || !prenom || !nomPharmacie || !matricule || !codePerso) { showAlert('Veuillez remplir tous les champs', 'error'); return; }
    if (!partnerLatLng) { showAlert('Placez la position de votre pharmacie sur la carte', 'error'); return; }
    const r = await callAPI('register_pharmacy', 'POST', { nom, prenom, nomPharmacie, lat: partnerLatLng.lat, lng: partnerLatLng.lng, matricule, codePerso });
    showAlert(r.message, r.success ? 'success' : 'error');
    if (r.success) {
        ['respNom','respPrenom','pharmNom','matricule','codePerso','partnerPosition'].forEach(id => document.getElementById(id).value = '');
        partnerLatLng = null;
        showAuthSection('login');
    }
}


async function partnerLogin() {
    const matricule = document.getElementById('loginMatricule').value.trim();
    const codePerso = document.getElementById('loginCode').value;
    if (!matricule || !codePerso) { showAlert('Matricule et code requis', 'error'); return; }
    const r = await callAPI('login_partner', 'POST', { matricule, codePerso });
    if (r.success) {
        currentPartner = r.data;
        document.getElementById('loginSection').style.display = 'none';
        document.getElementById('registerSection').style.display = 'none';
        document.getElementById('authToggle').style.display = 'none';
        document.getElementById('partnerDashboard').style.display = 'block';
        document.getElementById('dashPharmName').textContent = `🏪 ${currentPartner.nomPharmacie}`;
        document.getElementById('dashPharmInfo').textContent = `Responsable : ${currentPartner.prenom} ${currentPartner.nom}`;
        showAlert(`Bienvenue ${currentPartner.prenom} !`, 'success');
        refreshStockDisplay();
    } else {
        showAlert(r.message, 'error');
    }
}

async function logoutPartner() {
    await callAPI('logout_partner', 'POST');
    currentPartner = null;
    document.getElementById('partnerDashboard').style.display = 'none';
    document.getElementById('authToggle').style.display = 'flex';
    showAuthSection('login');
    ['loginMatricule','loginCode'].forEach(id => document.getElementById(id).value = '');
    showAlert('Déconnexion réussie', 'info');
}

async function refreshStockDisplay() {
    const r = await callAPI('get_stock', 'GET');
    if (!r.success) return;
    document.getElementById('stockCount').textContent = r.data.length;
    const list = document.getElementById('stockList');
    if (!r.data.length) {
        list.innerHTML = '<div class="empty-state"><div class="es-icon">📦</div><p>Aucun médicament en stock</p></div>';
        return;
    }
    list.innerHTML = r.data.map(m => `
        <div class="med-item">
            <div class="med-info">
                <strong>💊 ${m.nom_medicament}</strong>
                <small>Quantité : ${m.quantite}</small>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="med-price">₺ ${parseFloat(m.prix).toFixed(2)}</span>
                <button class="btn btn-danger" onclick="deleteMedicament(${m.id})">🗑️</button>
            </div>
        </div>`).join('');
}

async function addMedicament() {
    const nom = document.getElementById('medicamentNom').value.trim();
    const prix = document.getElementById('medicamentPrix').value;
    const quantite = document.getElementById('medicamentQuantite').value || 1;
    if (!nom || !prix) { showAlert('Nom et prix requis', 'error'); return; }
    const r = await callAPI('add_medicament', 'POST', { nom, prix: parseFloat(prix), quantite: parseInt(quantite) });
    showAlert(r.message, r.success ? 'success' : 'error');
    if (r.success) {
        ['medicamentNom','medicamentPrix'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('medicamentQuantite').value = '1';
        refreshStockDisplay();
    }
}

async function deleteMedicament(id) {
    const r = await callAPI('delete_medicament', 'DELETE', { medicament_id: id });
    showAlert(r.message, r.success ? 'success' : 'error');
    if (r.success) refreshStockDisplay();
}

// Fuzzy match: normalise accents + tolerance de fautes (distance de Levenshtein simplifiée)
function normalizeStr(s) {
    return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, '');
}
function levenshtein(a, b) {
    const m = a.length, n = b.length;
    const dp = Array.from({length: m+1}, (_, i) => Array.from({length: n+1}, (_, j) => j === 0 ? i : 0));
    for (let j = 1; j <= n; j++) dp[0][j] = j;
    for (let i = 1; i <= m; i++)
        for (let j = 1; j <= n; j++)
            dp[i][j] = a[i-1] === b[j-1] ? dp[i-1][j-1] : 1 + Math.min(dp[i-1][j], dp[i][j-1], dp[i-1][j-1]);
    return dp[m][n];
}
function fuzzyMatch(query, target) {
    const q = normalizeStr(query), t = normalizeStr(target);
    if (!q) return true;
    // Correspondance directe (contient)
    if (t.includes(q)) return true;
    // Tolérance : 1 faute par tranche de 4 caractères
    const maxDist = Math.floor(q.length / 4) + 1;
    // Fenêtre glissante sur la cible
    for (let i = 0; i <= t.length - q.length + maxDist; i++) {
        const window = t.substr(i, q.length + maxDist);
        if (levenshtein(q, window.substr(0, q.length)) <= maxDist) return true;
    }
    return false;
}

async function findNearestPharmacy() {
    if (!clientLatLng) { showAlert('Sélectionnez votre position sur la carte', 'error'); return; }
    const nom = document.getElementById('clientNom').value.trim();
    const prenom = document.getElementById('clientPrenom').value.trim();
    const medQuery = document.getElementById('clientMedicament').value.trim();
    if (!nom || !prenom) { showAlert('Veuillez entrer votre nom et prénom', 'error'); return; }
    await callAPI('save_client_location', 'POST', { nom, prenom, lat: clientLatLng.lat, lng: clientLatLng.lng });
    const r = await callAPI('search_pharmacies', 'POST', { lat: clientLatLng.lat, lng: clientLatLng.lng });
    if (!r.success || !r.data.length) { showAlert('Aucune pharmacie trouvée dans un rayon de 50 km', 'error'); return; }

    let pharmacies = r.data;

    // Si un médicament est saisi, trier : celles qui l'ont en premier, avec badge
    if (medQuery) {
        pharmacies = pharmacies.map(p => {
            const matchedMeds = (p.medicaments || []).filter(m => fuzzyMatch(medQuery, m.nom_medicament));
            return { ...p, _hasMed: matchedMeds.length > 0, _matchedMeds: matchedMeds };
        });
        // Celles qui ont le médicament en tête
        pharmacies.sort((a, b) => {
            if (a._hasMed && !b._hasMed) return -1;
            if (!a._hasMed && b._hasMed) return 1;
            return parseFloat(a.distance) - parseFloat(b.distance);
        });

        const found = pharmacies.filter(p => p._hasMed).length;
        if (found === 0) {
            showAlert(`Aucune pharmacie proche n'a "${medQuery}" en stock — affichage de toutes les pharmacies`, 'info');
        } else {
            showAlert(`${found} pharmacie(s) avec "${medQuery}" trouvée(s) !`, 'success');
        }
    }

    searchResults = pharmacies;
    document.getElementById('resultCount').textContent = pharmacies.length;
    document.getElementById('clientResults').style.display = 'block';
    document.getElementById('nearestResult').innerHTML = pharmacies.map((p, i) => {
        const hasMed = p._hasMed;
        const matchedMeds = p._matchedMeds || [];
        const allMeds = p.medicaments || [];
        const medsHtml = allMeds.length
            ? `<div class="pharm-meds">${allMeds.map(m => {
                const isMatch = medQuery && fuzzyMatch(medQuery, m.nom_medicament);
                return `<span class="med-tag" style="${isMatch ? 'background:rgba(0,212,170,.18);border-color:rgba(0,212,170,.5);color:var(--accent);font-weight:600;' : ''}">💊 ${m.nom_medicament}</span>`;
              }).join('')}</div>`
            : '<small style="color:var(--muted);">Aucun médicament renseigné</small>';

        const medBadge = hasMed
            ? `<span style="background:rgba(0,212,170,.15);border:1px solid rgba(0,212,170,.4);color:var(--accent);padding:3px 9px;border-radius:20px;font-size:.68rem;font-weight:700;">✅ Médicament disponible</span>`
            : (medQuery ? `<span style="background:rgba(255,77,109,.08);border:1px solid rgba(255,77,109,.2);color:var(--danger);padding:3px 9px;border-radius:20px;font-size:.68rem;">❌ Non disponible</span>` : '');

        return `
        <div class="pharmacy-card ${i===0&&!medQuery?'nearest':''} ${hasMed?'nearest':''}" onclick="focusPharmacyOnMap(${parseFloat(p.latitude)}, ${parseFloat(p.longitude)}, '${p.nom_pharmacie.replace(/'/g,"\\'")}')">
            <div class="pharm-header">
                <div class="pharm-name">
                    ${hasMed?'⭐ ':(i===0&&!medQuery?'⭐ ':'🏥 ')}${p.nom_pharmacie}
                    <small>👤 ${p.prenom_responsable} ${p.nom_responsable}</small>
                </div>
                <span class="distance-badge">${parseFloat(p.distance).toFixed(2)} km</span>
            </div>
            ${medBadge ? `<div style="margin-bottom:8px;">${medBadge}</div>` : ''}
            ${medsHtml}
        </div>`;
    }).join('');
}


let resultsMapInitialized = false;

function showResultsOnMap() {
    document.getElementById('mapModal').classList.add('open');
    setTimeout(() => {
        if (!resultsMapInitialized) {
            const tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            resultsMapL = L.map('resultsMap').setView([clientLatLng.lat, clientLatLng.lng], 13);
            L.tileLayer(tileUrl, { attribution: '', subdomains: 'abcd', maxZoom: 19 }).addTo(resultsMapL);
            resultsMapInitialized = true;
        } else {
            resultsMapL.invalidateSize();
        }
       
        resultsMapL.eachLayer(l => { if (l instanceof L.Marker || l instanceof L.Polyline) resultsMapL.removeLayer(l); });

        L.marker([clientLatLng.lat, clientLatLng.lng], { icon: makeIcon('#ff6b35') })
            .addTo(resultsMapL)
            .bindPopup('<strong>📍 Votre position</strong>').openPopup();

        searchResults.forEach((p, i) => {
            const lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
            const marker = L.marker([lat, lng], { icon: makeIcon(i===0?'#00d4aa':'#7c5cfc') }).addTo(resultsMapL);
            marker.bindPopup(`<strong>${i===0?'⭐ ':'🏥 '}${p.nom_pharmacie}</strong><br>
                <span style="color:#6b7a99;">👤 ${p.prenom_responsable} ${p.nom_responsable}</span><br>
                <strong style="color:#00d4aa;">${parseFloat(p.distance).toFixed(2)} km</strong>`);
            if (i === 0) L.polyline([[clientLatLng.lat, clientLatLng.lng], [lat, lng]], { color: '#00d4aa', weight: 2, dashArray: '6,6', opacity: .7 }).addTo(resultsMapL);
        });

        const bounds = L.latLngBounds(searchResults.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]));
        bounds.extend([clientLatLng.lat, clientLatLng.lng]);
        resultsMapL.fitBounds(bounds, { padding: [30,30] });
    }, 250);
}

function focusPharmacyOnMap(lat, lng, name) {
    showResultsOnMap();
    setTimeout(() => {
        resultsMapL.setView([lat, lng], 16);
    }, 500);
}

function closeMapModal() {
    document.getElementById('mapModal').classList.remove('open');
}

/* ── ADMIN ── */
const ADMIN_CODE = '12345';
function adminLogin() {
    if (document.getElementById('adminCode').value !== ADMIN_CODE) { showAlert('Code administrateur incorrect', 'error'); return; }
    isAdminLogged = true;
    document.getElementById('adminLoginCard').style.display = 'none';
    document.getElementById('adminPanel').style.display = 'block';
    refreshAdminList();
}
function logoutAdmin() {
    isAdminLogged = false;
    document.getElementById('adminLoginCard').style.display = 'block';
    document.getElementById('adminPanel').style.display = 'none';
    document.getElementById('adminCode').value = '';
}
async function refreshAdminList() {
    const r = await callAPI('get_all_pharmacies', 'GET');
    if (!r.success) return;
    document.getElementById('totalPharmacies').textContent = r.data.length;
    const div = document.getElementById('allPharmaciesList');
    if (!r.data.length) { div.innerHTML = '<div class="empty-state"><div class="es-icon">🏥</div><p>Aucune pharmacie enregistrée</p></div>'; return; }
    div.innerHTML = `<table class="admin-table">
        <thead><tr>
            <th>Pharmacie</th><th>Responsable</th><th>Matricule</th><th>Médicaments</th><th>Date</th><th></th>
        </tr></thead>
        <tbody>${r.data.map(p => `<tr>
            <td><strong>🏥 ${p.nom_pharmacie}</strong></td>
            <td style="color:var(--muted);font-size:.78rem;">${p.prenom_responsable} ${p.nom_responsable}</td>
            <td><code style="font-family:'Space Mono',monospace;font-size:.75rem;color:var(--accent3);">${p.matricule}</code></td>
            <td><span class="count-badge">${p.nb_medicaments}</span></td>
            <td style="color:var(--muted);font-size:.75rem;">${new Date(p.date_inscription).toLocaleDateString('fr-FR')}</td>
            <td><button class="btn btn-danger" onclick="deletePharmacy(${p.id},'${p.nom_pharmacie.replace("'","\'")}')">🗑️</button></td>
        </tr>`).join('')}</tbody>
    </table>`;
}
async function deletePharmacy(id, name) {
    if (!confirm(`Supprimer "${name}" et tous ses médicaments ?`)) return;
    const r = await callAPI('delete_pharmacy', 'DELETE', { pharmacy_id: id });
    showAlert(r.message, r.success ? 'success' : 'error');
    if (r.success) refreshAdminList();
}

document.getElementById('mapModal').addEventListener('click', function(e) {
    if (e.target === this) closeMapModal();
});
</script>
</body>
</html>