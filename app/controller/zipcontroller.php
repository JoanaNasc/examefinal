<?php

namespace app\controller;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
/*
    Classe que  chama a funcção para criação do zip
*/
class ZipController{
    public static function zip(){
        $argsToLog ['request'] = 'Zip Project';
       
        //pasta a ser zipada
        $rootFolder = PROJECT_PATH;

        //nome do zip com extensão
        $zipName ='Joana'.date("YmdHis").'.zip';
        //criação do zip
        $zip= New ZipArchive();
        $zip->open(SAFE_FILES_PATH.'/'.$zipName);

        //cria o ficheiro zip, caso já esteja criado reescreve o ficheiro.
        $zipCreated = $zip->open(SAFE_FILES_PATH.'/'.$zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        //caso de erro
        if(!$zipCreated){
            echo 'Zip creation failed, code:' . $zipCreated;
            return false;
        }


        //percorre a pasta recursivamente à procura dos ficheiros que lá existam para os puder adicionar ao zip
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootFolder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        //criação do zip de forma recursiva
        foreach ($files as $file) {
            //só adiciona no zip os ficheiros, ignora as directorias.
            if(!$file->isDir()){
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootFolder));
                    $zip->addFile($filePath, $relativePath);
            }
            
        }
        $closed = $zip->close();
        if($closed){
            $message='Zip was created successfully and saved in safeFiles directory.';
            $argsToLog ['description'] = $message;
            LogController::post($argsToLog);
            header('location:/');
        }else{
            $message='Error Creating Zip. Please try again';
            $argsToLog ['description'] = $message;
            LogController::post($argsToLog);
            header('location:/');
        }



    }
}


?>