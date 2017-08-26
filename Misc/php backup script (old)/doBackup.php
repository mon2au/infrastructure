<?php
/**
 * Quickly and easily backup your MySQL database and have the .tgz copied to
 * your Dropbox account.
 *
 * Copyright (c) 2012 Eric Silva
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Eric Silva [eric@ericsilva.org] [http://ericsilva.org/]
 * @version 1.0.0
 */
require('DropboxUploader.php');
// location of your temp directory

$tmpDir = "/tmp/";
// username for MySQL
$user = "root";
// password for MySQL
$password = "2c4ukb11";
// the zip file emailed to you will have this prefixed
$prefix = "gbox_";

// username for Dropbox
$dropbox_user = "glassboxprojects@gmail.com";
// password for Dropbox
$dropbox_password = "Lambdabunker3";
// Destination folder in Dropbox (folder will be created if doesn't yet exist)
$dropbox_dest = "GlassboxBackups";

date_default_timezone_set('Australia/Sydney');

// Create the database backup file
$sqlFile = $tmpDir.$prefix.date('Y_m_d').".sql";
$webFile = $tmpDir.$prefix."www";
$backupFilename = $prefix.date('Y_m_d').".tar.gz";
$backupFile = $tmpDir.$backupFilename;

$createDBBackup = "mysqldump -A -u ".$user." -p".$password." > ".$sqlFile;
echo "\n\nDatabase Command: ".$createDBBackup;

$createWebBackup = "cp -r /var/www/sites /tmp/".$prefix."www";
echo "\n\nWeb Command: ".$createWebBackup;
exec($createWebBackup);

$createWebBackup = "cp -r /var/www/glassbox /tmp/".$prefix."www";
echo "\n\nWeb Command: ".$createWebBackup;
exec($createWebBackup);

//create zip
$createZip = "tar cvzf $backupFile $sqlFile $webFile";
//display zip command
echo "\n\nZip Command: ".$createZip."\n\n";
exec($createDBBackup);
exec($createWebBackup);
exec($createZip);

try {
    // Upload database backup to Dropbox
    $uploader = new DropboxUploader($dropbox_user, $dropbox_password);
    $uploader->upload($backupFile, $dropbox_dest,  $backupFilename);
} catch(Exception $e) {
    die($e->getMessage());
}

// Delete the temporary files
unlink($sqlFile);
exec("rm -r /tmp/".$prefix."www");
unlink($backupFile);

?>

