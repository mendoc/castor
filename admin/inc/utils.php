<?php

class Utils
{
    public static function echoJSON(String $json)
    {
        header('Content-Type: application/json');
        die($json);
    }

    public static function echoError(String $message)
    {
        caslog($message);
        $json = json_encode(array("error" => true, "message" => $message));
        self::echoJSON($json);
        die();
    }

    public static function currentTheme()
    {
        $dirs = readdir(APP_DIR);
        var_dump($dirs);
    }

    public static function exists(String $path, String $message)
    {
        if (!file_exists($path)) {
            caslog($message);
            $json = json_encode(array("error" => true, "message" => $message));
            self::echoJSON($json);
            die();
        }
    }

    public static function empty(String $path, String $message)
    {
        $entries = scandir($path);
        $nbdir   = 0;
        $dir     = realpath($path);
        foreach($entries as $entry) {
            $fullpath = $dir . '/' . $entry;
            if (is_dir($fullpath) and $entry !== "." and $entry !== "..") {
                $nbdir++;
            }
        }
        if ($nbdir === 0){
            self::echoError($message);
        }
    }

    public static function themes(String $path)
    {
        $response = array("error" => false);
        $entries  = scandir($path);
        $dir      = realpath($path) . '/' ;
        $themes   = array();
        $config_filename = "config.json";

        foreach($entries as $entry) {
            $fullpath = $dir . $entry . "/";
            if (is_dir($fullpath) and $entry !== "." and $entry !== "..") {
                // Vérifier si le fichier config existe dans le dossier du thème
                $conf_path = $fullpath . $config_filename;
                if (file_exists($conf_path)) {
                    $theme = self::themeData($conf_path);
                    if ($theme) $themes[] = $theme;
                } else {
                    caslog("Le dossier \"$entry\" ne contient pas de fichier de configuration");
                }
            }
        }
        $response["themes"] = $themes;
        $json = json_encode($response);

        self::echoJSON($json);
    }

    public static function themeData(String $path)
    {
        $themedir          = basename(dirname($path));
        $response["error"] = false;
        $json              = file_get_contents($path);

        $config = self::appConfig();
        
        if ($json === false) {
            caslog("Problème lors de la récupération de la configuration du thème \"$themedir\"");
        } else if (empty($json)) {
            caslog("Le fichier de configuration du thème \"$themedir\" est vide.");
        } else {
            $data     = json_decode($json, true);
            caslog("Vérification des informations du thème \"$themedir\" ...");
            if (!self::checkFields($data, array("name", "id"))){
                if ($config["theme"]["active"] == $data["id"]) $data["active"] = true;
                return $data;
            }
            return null;
        }
    }

    public static function appConfig()
    {
        $app_json = file_get_contents(ADMIN_DIR . "app.json");

        if ($app_json === false) {
            self::echoError("Problème lors de la récupération de la configuration de l'application");
        } else if (empty($app_json)) {
            self::echoError("Le fichier de configuration de l'application est vide.");
        } else {
            $app_data = json_decode($app_json, true);
            if (!self::checkFields($app_data, array("theme"))){
                return $app_data;
            } else {
                self::echoError("Le fichier de configuration de l'application est incorrect.");
            }
        }
        return null;
    }
    public static function checkFields($data, $fields)
    {
        $error = false;
        foreach ($fields as $field){
            if (!isset($data[$field])){
                $error = true;
                caslog("Le paramètre '$field' n'est pas défini.");
            } else if (empty($data[$field])){
                $error = true;
                caslog("Le paramètre '$field' ne contient aucune valeur.");
            }
        }
        return $error;
    }

}

?>