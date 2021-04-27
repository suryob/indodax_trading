<?php
    define('OURSECRET',1);
    require "../lib/config.php";

    include dirname(__FILE__) . "/../lib/" . "db.php";

    $db = new db(DBHOST,DBUSER,DBPASS,DBNAME);

    $path = trim($_SERVER['REQUEST_URI'],"/");
    // var_dump($path);
    $konten = "yellow";
    if($path==""){
        include "templates/index.php";
    }else{
        $arrUri = explode("?",$path);
        // var_dump($arrUri);
        $arrPath = explode("/",$arrUri[0]);

        // var_dump($arrPath);
        $fileToInclude = $arrPath[0]."_".$arrPath[1].".php";
        // var_dump($fileToInclude);
        if(file_exists("templates/".$fileToInclude)){
            include "templates/".$fileToInclude;
        }else{
            include "templates/index.php";
        }
    }
