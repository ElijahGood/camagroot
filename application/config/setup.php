<?php
    exec("~/Applications/mamp/mysql/bin/mysql -u root -pqwerty < ./db_camagroot.sql  2>&- ");
    echo "Camagroot database was created. Or not, you never know...\n";
?>