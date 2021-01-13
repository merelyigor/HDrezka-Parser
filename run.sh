#!/bin/bash
#---------------------------------------------
#========================HDrezka Parser=======1.0
#----------------------------------------------
chmod 765 parser/start.php
brew services start tor
php parser/start.php