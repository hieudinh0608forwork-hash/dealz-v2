<?php
/**
 * Reject literal hexadecimal colors anywhere in theme source except tokens.css.
 */
$theme = realpath( __DIR__ . '/../wp-content/themes/dealz-theme' );
$tokens = $theme ? realpath( $theme . '/assets/css/tokens.css' ) : false;
if ( ! $theme || ! $tokens ) { fwrite( STDERR, "Theme directory or canonical tokens.css missing.\n" ); exit( 1 ); }
$extensions = array( 'css','php','js','svg' );
$violations = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $f ) {
    if ( ! $f->isFile() || ! in_array( strtolower( $f->getExtension() ), $extensions, true ) || realpath( $f->getPathname() ) === $tokens ) { continue; }
    $body = file_get_contents( $f->getPathname() );
    if ( preg_match_all( '/#[0-9a-fA-F]{3,8}\b/', $body, $found ) ) {
        foreach ( array_unique( $found[0] ) as $color ) { $violations[] = substr( $f->getPathname(), strlen( $theme ) + 1 ) . ': ' . $color; }
    }
}
if ( $violations ) { fwrite( STDERR, "Hex colors are allowed only in assets/css/tokens.css:\n- " . implode( "\n- ", $violations ) . "\n" ); exit( 1 ); }
echo "Theme hex-token gate passed.\n";
