<?php

namespace cm;

/**
 * Installer
 */
class Deinstall {

    /**
     * Run Deinstallation code
     */
    public static function run(){
        flush_rewrite_rules();
    }
    
}