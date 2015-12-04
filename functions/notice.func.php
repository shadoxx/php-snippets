<?php
/**
 * notice() - write a plain text message to stdout in the context of the call to printf()
 *
 * @param   string  $tag    the class of message this notice represent such as notice, debug, error, etc
 * @param   string  $mesg   an arbitrary message to be printed after $tag
 *
 * Example Usage:
 *  notice( "notice", sprintf("Initializing database connection %s", $dsnString) ); 
 */
function notice( $tag, $mesg ) { return printf("[%s] %s: %s\n", microtime(), $tag, $mesg); }
