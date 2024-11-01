<?php

class Vivio_Swift_Utility_File
{
    
    function __construct(){
    }
    
    static function write_content_to_file($file_path, $contents)
    {
        @chmod($file_path, 0777);
        if (is_writeable($file_path)) {
            $handle = fopen($file_path, 'w');
            fwrite($handle, $contents);
            /*
            foreach( $new_contents as $line ) {
                fwrite($handle, $line);
            }
            */
            fclose($handle);
            @chmod($file_path, 0644); //Let's change the file back to a secure permission setting
            return true;
    	} else {
                return false;
    	}
    }
    
    static function backup_a_file($src_file_path, $suffix = 'backup')
    {
        $backup_file_path = $src_file_path . '.' . $suffix;
        if (!copy($src_file_path, $backup_file_path)) {
            // Failed to make a backup copy
            return false;
        }
        return true;
    }
    
    /*
     * custom file backup method for htaccess to vivio swift backup dir
     */
    static function backup_and_rename_htaccess($src_file_path, $suffix = 'backup')
    {
        global $vivio_swift_global;
        
        // Check to see if the main "backups" directory exists - create it otherwise
        $vivio_swift_backup_dir = VIVIO_SWIFT_BACKUPS_PATH;
        if (!$vivio_swift_global->util_file->create_dir($vivio_swift_backup_dir))
        {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_File::backup_and_rename_htaccess() - Creation of backup directory failed!", 4);
            return false;
        }
        
        $src_parts = pathinfo($src_file_path);
        $backup_file_name = $src_parts['basename'] . '.' . $suffix;
        
        $backup_file_path = $vivio_swift_backup_dir . '/' . $backup_file_name;
        if (!copy($src_file_path, $backup_file_path)) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_File::backup_and_rename_htaccess() - Failed to backup ".$src_file_path." to ".$backup_file_path, 4);
            // Failed to make a backup copy
            return false;
        }
        return true;
    }
    
    static function recursive_file_search($pattern='*', $flags = 0, $path='')
    {
        $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
        if ($paths === FALSE){
            return FALSE;
        }
        $files=glob($path.$pattern, $flags);
        if ($files === FALSE){
            return FALSE;
        }
        foreach ($paths as $path) { $files=array_merge($files,$vivio_swift_global->util_file->recursive_file_search($pattern, $flags, $path)); }
        return $files;
    }
    
    /*
     * Useful when wanting to echo file contents to screen with <br /> tags
     */
    static function get_file_contents_with_br($src_file)
    {
        $file_contents = file_get_contents($src_file);        
        return nl2br($file_contents);
    }

    /*
     * Useful when wanting to echo file contents inside textarea
     */
    static function get_file_contents($src_file)
    {
        $file_contents = file_get_contents($src_file);        
        return $file_contents;
    }
    
    /*
     * Returns the file's permission value eg, "0755"
     */
    static function get_file_permission($filepath)
    {
        if (!function_exists('fileperms')) 
        {
            $perms = '-1';
        }
        else 
        {
            clearstatcache();
            $perms = substr(sprintf("%o", @fileperms($filepath)), -4);
        }
        return $perms;
    }

    /*
     * Checks if a write operation is possible for a currently non-existing file
     * use is_writable() for files or directories that already exist.
     */
    static function is_file_writable($filepath)
    {
        $test_string = ""; //We will attempt to append an empty string at the end of the file for the test
        $write_result = @file_put_contents($filepath, $test_string, FILE_APPEND | LOCK_EX);
        if ($write_result === false)
        {
            return false;
        } 
        else
        {
            return true;
        }
    }

    static function download_a_file_option1($file_path, $file_name = '')
    {
        $file = $file_path;//Full ABS path to the file
        if(empty($file_name)){$file_name = basename($file);}

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file_name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        //ob_clean();
        //flush();
        readfile($file);
        exit;
    }
    
    static function download_content_to_a_file($output, $file_name = '')
    {
        if(empty($file_name)){$file_name = "aiowps_" . date("Y-m-d_H-i", time()).".txt";}

        header("Content-Encoding: UTF-8");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=" . $file_name);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . strlen($output));
        echo $output;
        exit;
    }

    /*
     * Checks if a directory exists and creates one if it does not
     */
    static function create_dir($dirpath='')
    {
        $res = true;
        if ($dirpath != '')
        {
            //TODO - maybe add some checks to make sure someone is not passing a path with a filename, ie, something which has ".<extenstion>" at the end
            //$path_parts = pathinfo($dirpath);
            //$dirpath = $path_parts['dirname'] . '/' . $path_parts['basename'];
            if (!file_exists($dirpath))
            {
                $res = mkdir($dirpath, 0755);
            }
        }
        return $res;
    }

    static function create_dir_recursive($dirpath='')
    {
        $res = true;
        if ($dirpath != '')
        {
            //TODO - maybe add some checks to make sure someone is not passing a path with a filename, ie, something which has ".<extenstion>" at the end
            //$path_parts = pathinfo($dirpath);
            //$dirpath = $path_parts['dirname'] . '/' . $path_parts['basename'];
            if (!file_exists($dirpath))
            {
                $res = mkdir($dirpath, 0755, true);
            }
        }
        return $res;
    }

    /**
     * Will return an indexed array of files sorted by last modified timestamp
     * @param string $dir
     * @param string $sort (ASC, DESC)
     * @return array
     */
    static function scan_dir_sort_date($dir, $sort='DESC')
    {
        $files = array();
        foreach (scandir($dir) as $file) {
            $files[$file] = filemtime($dir . '/' . $file);
        }

        if ($sort === 'ASC') {
            asort($files);
        }
        else {
            arsort($files);
        }

        return array_keys($files);
    }

    /**
     * recursively removes cached files in the vivio swift cache directory
     * @param string $dir
     * @return boolean
     */
    static function clear_cache_dir() {
        global $vivio_swift_global;
        $dir=VIVIO_SWIFT_CACHE_PATH;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_File::clear_cache_dir() - Attempting to clear cache dir: ".$dir, 1);
        $objects = scandir($dir); 
        foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (is_dir($dir."/".$object))
                    $vivio_swift_global->util_file->rm_dir_r($dir."/".$object);
                else
                    unlink($dir."/".$object); 
            } 
        }
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_File::clear_cache_dir() - Clear cache process completed for: ".$dir, 1);
    }

    // used by clear_cache_dir to recursively remove cache directories
    private function rm_dir_r($dir='')
    {
        global $vivio_swift_global;
        if ($dir != ''){
            if (is_dir($dir)) { 
                $objects = scandir($dir); 
                foreach ($objects as $object) { 
                    if ($object != "." && $object != "..") { 
                        if (is_dir($dir."/".$object))
                            $vivio_swift_global->util_file->rm_dir_r($dir."/".$object);
                        else
                            unlink($dir."/".$object); 
                    } 
                }
                rmdir($dir);
            } 
        }
    }

    /**
     * recursively counts the number of files in a directory
     * @param string $dir
     * @return integer
     */
    static function get_file_count()
    {
        global $vivio_swift_global;
        return $vivio_swift_global->util_file->file_cnt_r(VIVIO_SWIFT_CACHE_PATH);
    }

    // used by get_file_count to recursively count files
    private function file_cnt_r($dir)
    {
        global $vivio_swift_global;
        $count = 0;
        $ignore = array('.','..');
        $files = scandir($dir);
        foreach($files as $t) {
            if(in_array($t, $ignore)) continue;
            if (is_dir(rtrim($dir, '/') . '/' . $t)) {
                $count += $vivio_swift_global->util_file->file_cnt_r(rtrim($dir, '/') . '/' . $t);
            } else {
                $count++;
            }   
        }
        return $count;
    }

    static function get_directory_size()
    {
        global $vivio_swift_global;
        return $vivio_swift_global->util_file->file_size_to_string($vivio_swift_global->util_file->dir_size_r(VIVIO_SWIFT_CACHE_PATH));
    }

    // used by get_directory_size to recursively size files and add them together
    private function dir_size_r($dir)
    {
        global $vivio_swift_global;
        $size = 0;
        $ignore = array('.','..');
        $files = scandir($dir);
        foreach($files as $file) {
            if(in_array($file, $ignore)) continue;
            if(is_dir($dir.'/'.$file)){
                $size+=$vivio_swift_global->util_file->dir_size_r("{$dir}/{$file}");
            } else {
                $size = $size + filesize("{$dir}/{$file}");
            }
        }
        return $size;
    }

    static function file_size_to_string($size)
    {
        if($size >= 1024000000000)return number_format(($size/1024000000000),2).' TB';
        if($size >= 1024000000)return number_format(($size/1024000000),2).' GB';
        if($size >= 1024000)return number_format(($size/1024000),2).' MB';
        if($size >= 1024)return number_format(($size/1024),2).' KB';
        if($size >= 0)return $size.' bytes';
    }

}
