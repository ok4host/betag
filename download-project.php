<?php
/**
 * Download Project Files
 * Access this file via browser to download the complete project
 */

// Security: Only allow download once, then delete this file
$zipFile = __DIR__ . '/../betag-complete.zip';

if (!file_exists($zipFile)) {
    // Create ZIP if not exists
    $zip = new ZipArchive();
    $zipPath = $zipFile;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $rootPath = __DIR__;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Skip git folder and this download file
                if (strpos($relativePath, '.git') === 0) continue;
                if ($relativePath === 'download-project.php') continue;

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }
}

if (file_exists($zipFile)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="betag-project.zip"');
    header('Content-Length: ' . filesize($zipFile));
    header('Cache-Control: no-cache, must-revalidate');

    readfile($zipFile);
    exit;
} else {
    echo "Error creating ZIP file";
}
