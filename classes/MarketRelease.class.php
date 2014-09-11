<?php

require_once 'lib/datei.inc.php';

class MarketRelease extends SimpleORMap {

    static public function getReleaseDataPath() {
        return $GLOBALS['STUDIP_BASE_PATH'] . "/data/pluginmarket_releases";
    }

    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_releases';
        $config['belongs_to']['plugin'] = array(
            'class_name' => 'MarketPlugin',
            'foreign_key' => 'plugin_id',
        );
        parent::configure($config);
    }

    public function installFile() {
        $hash = md5(uniqid());
        $tmp_folder = $GLOBALS['TMP_PATH']."/temp_plugin_".$hash;
        mkdir($tmp_folder);
        $file = $GLOBALS['TMP_PATH']."/temp_plugin_".$hash.".zip";
        if ($this['repository_download_url']) {
            file_put_contents($file, file_get_contents($this['repository_download_url']));
        } elseif ($_FILES['release_file']['tmp_name']) {
            move_uploaded_file($_FILES['release_file']['tmp_name'], $file);
        } else {
            return false;
        }
        unzip_file($file, $tmp_folder);
        $objects = scandir($tmp_folder);
        if (count($objects) === 3) {
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $plugin_dir = $tmp_folder."/".$object;
                }
            }
        } else {
            $plugin_dir = $tmp_folder;
        }
        $this->installFromDirectory($plugin_dir);

        rmdirr($tmp_folder);
        unlink($file);
    }

    protected function installFromDirectory($dir) {
        $manifest = PluginManager::getInstance()->getPluginManifest($dir);
        $this['studip_min_version'] = $manifest['studipMinVersion'];
        $this['studip_max_version'] = $manifest['studipMaxVersion'];
        if (!$this['version']) {
            $this['version'] = $manifest['version'];
        }
        $hash = md5(uniqid());
        $plugin = $GLOBALS['TMP_PATH']."/plugin_$hash.zip";
        create_zip_from_directory($dir, $plugin);

        $RELEASE_DATA_PATH = self::getReleaseDataPath();
        if (!file_exists($RELEASE_DATA_PATH)) {
            mkdir($RELEASE_DATA_PATH);
        }
        if (!$this->getId()) {
            $this->setId($this->getNewId());
        }

        copy($plugin, $RELEASE_DATA_PATH."/".$this->getId());
        unlink($plugin);
        return true;
    }

}