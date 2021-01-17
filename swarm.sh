#!/bin/bash
#---------------------------------------------
#========================HDrezka Parser=======1.0
#----------------------------------------------
chmod 765 parser/start.php
# trap ctrl-c and call ctrl_c() при нажатии на Ctrl+C в терминале вызывается функция ctrl_c()
trap ctrl_c INT

function ctrl_c() {
  php parser/start.php stop
}

php parser/start.php
