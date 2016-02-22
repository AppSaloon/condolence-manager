<?php

namespace cm;

/**
 * Installer
 */
class Install {

    /**
     * Run installation code
     */
    public static function run(){
        flush_rewrite_rules();
    }

}