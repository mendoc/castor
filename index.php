<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once 'admin/inc/constants.php';

require_once 'admin/inc/helpers.php';

require_once 'admin/inc/utils.php';

// Vérifier si le fichier de configuration de l'application existe
Utils::exists(ADMIN_DIR . "app.json", l("Le fichier de configuration de l'application n'existe pas."));

// Tester l'existence du dossier des thèmes
Utils::exists(THEMES_DIR, l("Le dossier des thèmes n'existe pas."));

// Vérifier si le dossier des thèmes n'est pas vide
Utils::empty(THEMES_DIR, l("Il n'y a aucun thème dans le dossier des thèmes"));

// Lister tous les thèmes
Utils::themes(THEMES_DIR);