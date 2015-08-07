<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.module.controlleradmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Listeextensions Installation Controller
 *
 * @since  1.6
 */
class ExtensionexportiibControllerListeextensions extends JControllerLegacy {

    /*
     * Vérifier si le dossier de l'élément se trouve dans le dossier media
     */
    function copy_media_folder($element, $folder){
        $media_element = JPATH_SITE . "/media/$element";
        if (JFolder::exists($media_element)) {
            JFolder::copy($media_element, $folder."/media", '', true);
        }
    }
    
    function plugin_folder_create($element, $folder) {
        $plugin_folder = ExtensionexportiibFOLDER . '/' . $element;
        //Création du dossier à zipper par la suite
        JFolder::create($plugin_folder);

        //Copier les dossiers du site
        $source_mod = JPATH_SITE . "/plugins/$folder/" . $element;
        JFolder::copy($source_mod, $plugin_folder, '', true);
    }

    function module_folder_create($element, $client_id) {

        if ($client_id == 0) {
            $path_mod = JPATH_SITE;
        } else {
            $path_mod = JPATH_ADMINISTRATOR;
        }
        $module_folder = ExtensionexportiibFOLDER . '/' . $element;
        //Création du dossier à zipper par la suite
        JFolder::create($module_folder);

        //Copier les dossiers du site
        $source_mod = $path_mod . "/modules/" . $element;
        JFolder::copy($source_mod, $module_folder, '', true);
        
        //Vérifier s'il y a des fichiers media et les copier
        $this->copy_media_folder($element, $module_folder);
        
    }

    function template_folder_create($element, $client_id) {
        if ($client_id == 0) {
            $path_tpl = JPATH_SITE;
        } else {
            $path_tpl = JPATH_ADMINISTRATOR;
        }
        $template_folder = ExtensionexportiibFOLDER . '/' . $element;
        //Création du dossier à zipper par la suite
        JFolder::create($template_folder);
        //Copier les dossiers du site
        $source_tpl = $path_tpl . "/templates/" . $element;
        JFolder::copy($source_tpl, $template_folder, '', true);
    }

    function component_folder_create($element) {
        $component_folder = ExtensionexportiibFOLDER . '/' . $element;
        //Création du dossier à zipper par la suite
        JFolder::create($component_folder);
        //Copier les dossiers de l'administration
        $source_admin = JPATH_ADMINISTRATOR . "/components/" . $element;
        $destination_admin = $component_folder . "/administrator";
        if (JFolder::exists($source_admin)) {
            JFolder::copy($source_admin, $destination_admin, '', true);
        }

        //Copier les dossiers du site
        $source_site = JPATH_SITE . "/components/" . $element;
        $destination_site = $component_folder . "/site";
        if (JFolder::exists($source_site)) {
            JFolder::copy($source_site, $destination_site, '', true);
        }

        //Déplacer le fichier .xml
        $component = substr($element, 4, strlen($element) - 4);
        $xml_file_source = $destination_admin . "/" . $component . '.xml';
        $xml_file_desination = $component_folder . "/" . $component . '.xml';
        JFile::move($xml_file_source, $xml_file_desination);
        
        //Vérifier s'il y a des fichiers media et les copier
        $this->copy_media_folder($element, $component_folder);
    }

    function extension_folder_create($element, $type, $client_id, $folder) {
        if ($type == "component") {
            $this->component_folder_create($element);
        } else if ($type == "module") {
            $this->module_folder_create($element, $client_id);
        } else if ($type == "template") {
            $this->template_folder_create($element, $client_id);
        } else if ($type == "plugin") {
            $this->plugin_folder_create($element, $folder);
        }
    }

    /*
     * Zipper un dossier
     */

    function zip_folder($element) {
        $folder_path = ExtensionexportiibFOLDER . '/' . $element;

// store the current joomla working directory
        $joomla = getcwd();

// in this example, we are changing the working directory
// to the script file's location
// We'll get the files from that location and store our zipfile there
        chdir($folder_path);

// the full file path of the zip file
        $zipfile = JPath::clean("$folder_path.zip");

        $files = JFolder::files('.', '', true, true);
        $data = array();
        foreach ($files as $file) {
            $tmp = array();
            $tmp['name'] = str_replace('./', '', $file);
            $tmp['data'] = file_get_contents($file);
            $tmp['time'] = filemtime($file);
            $data[] = $tmp;
        }

// get the zip adapter
        $zip = JArchive::getAdapter('zip');

//create the zip file
        $zip->create($zipfile, $data);

        chdir($joomla);
    }

    /*
     * Supprimer le dossier du composant et le fichier zip créés
     */

    function delete_files($element) {
        //Supprimer le dossier de l'extension
        JFolder::delete(ExtensionexportiibFOLDER . '/' . $element);
        //Supprimer le fichier zippé
        JFile::delete(ExtensionexportiibFOLDER . '/' . $element . '.zip');
    }

    /*
     * 1- Récupérer le dossier
     * 2- Vérifier si un fichier de langue de l'extension s'y trouve
     * 3- Copier les fichiers de langue vers la destination appropriée
     */

    function copy_language_files($folder_name, $folder_fullname, $element, $folder_dest) {
        $index_html_content = "<!DOCTYPE html><title></title>";
        $array_ini = array("ini", "sys.ini");
        foreach ($array_ini as $value) {
            $ext_file = "$folder_fullname/$folder_name.$element.$value";
            if (JFile::exists($ext_file)) {
                JFolder::create("$folder_dest/$folder_name");
                JFile::write("$folder_dest/index.html", $index_html_content);
                $destination = "$folder_dest/$folder_name/$folder_name.$element.$value";
                JFile::copy($ext_file, $destination);
                JFile::write("$folder_dest/$folder_name/index.html", $index_html_content);
            }
        }
    }

    /*
     * Vérifier si des fichiers de langue existent dans un dossier
     * Il faudra rechercher les fichiers de langue en-GB, fr-FR, etc
     */

    function search_language_in_folder($folder_search, $folder_dest, $element) {
        if (JFolder::folders($folder_search) != false) {
            $list_folders = JFolder::listFolderTree($folder_search, $filter = '');
            foreach ($list_folders as $folder) {
                $this->copy_language_files($folder['name'], $folder['fullname'], $element, $folder_dest);
            }
        }
    }

    /*
     * Copier les fichiers de langue présents
     * Il faudra chercher dans les différents dossiers de langue 
     * (JPATH_ADMINISTRATOR/language, JPATH_SITE/language, JPATH_COMPONENT_ADMIN/language)
     * Si on trouve un fichier fr-FR.... par exemple, on le copie dans le repertoire approprié
     */

    function language_files($element, $type) {
        /*
         * Déclarer les variables
         */
        if ($type == "component") {
            $dest_ext_site = ExtensionexportiibFOLDER . "/" . $element . "/languages/site";
            $dest_ext_admin = ExtensionexportiibFOLDER . "/" . $element . "/languages/administrator";
        } else {
            $dest_ext_admin = $dest_ext_site = ExtensionexportiibFOLDER . "/" . $element . "/language";
        }
        $search_language_admin = JPATH_ADMINISTRATOR . "/language";
        $search_language_site = JPATH_SITE . "/language";

        /*
         * Démarrer la recherche dans les différents répertoires
         */
        $this->search_language_in_folder($search_language_admin, $dest_ext_admin, $element);
        $this->search_language_in_folder($search_language_site, $dest_ext_site, $element);
    }

    /*
     * Exporter une extension
     * 1- Récupérer le type de l'extension
     * 2- Créer le fichier zip 
     * 3- Rediriger vers la vue de liste
     * 4- Exporter automatiquement l'extension
     */

    public function export() {
        $input = JFactory::getApplication()->input;
        $extension_id = $input->get("id");
        define('ExtensionexportiibFOLDER', JPATH_ADMINISTRATOR . '/ExtensionExportIIB');

        //Récupérer le nom de l'extension
        $model = $this->getModel('listeextensions');
        $attributes = $model->getAttributes($extension_id);

        $element = $attributes->element;
        $type = $attributes->type;
        $client_id = $attributes->client_id;
        $folder = $attributes->folder;

        //Créer le dossier regroupant tous les fichiers de l'extension
        $this->extension_folder_create($element, $type, $client_id, $folder);

        //Copier les fichiers de langue nécessaires
        $this->language_files($element, $type);

        //Zipper le dossier précédent
        $this->zip_folder($element);

        //Enregistrer le nom de l'extension dans la session
        JFactory::getApplication()->setUserState('com_extensionexportiib.listeextensions.extension', $element);

        //Définir le message à afficher
        JFactory::getApplication()->enqueueMessage(JText::_("COM_EXTENSIONEXPORTIIB_MESSAGE_EXPORTATION_SUCCESS"), 'message');

        //Rediriger vers la vue appropriée
        $this->setRedirect(JRoute::_('index.php?option=com_extensionexportiib&view=listeextensions', false));
    }

}
