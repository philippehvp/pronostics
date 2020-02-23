<?php
    $dossierSauvegardes = '/home/lepoulpeg/backup/';
    if (file_exists($dossierSauvegardes)) {
        foreach (new DirectoryIterator($dossierSauvegardes) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 24*60*60) {
                unlink($fileInfo->getRealPath());
            }
        }
    }

    $dossierScores = '/home/lepoulpeg/traces/scores/';
    if (file_exists($dossierScores)) {
        foreach (new DirectoryIterator($dossierScores) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 7*24*60*60) {
                unlink($fileInfo->getRealPath());
            }
        }
    }

    $dossierButeurs = '/home/lepoulpeg/traces/buteurs/';
    if (file_exists($dossierButeurs)) {
        foreach (new DirectoryIterator($dossierButeurs) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 7*24*60*60) {
                unlink($fileInfo->getRealPath());
            }
        }
    }

    $dossierCanal = '/home/lepoulpeg/traces/canal/';
    if (file_exists($dossierCanal)) {
        foreach (new DirectoryIterator($dossierCanal) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= 7*24*60*60) {
                unlink($fileInfo->getRealPath());
            }
        }
    }
?>