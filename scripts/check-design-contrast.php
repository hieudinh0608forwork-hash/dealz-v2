<?php
/**
 * Check the approved contrast matrix using canonical CSS tokens.
 */
$file = __DIR__ . '/../wp-content/themes/dealz-theme/assets/css/tokens.css';
$css = is_file( $file ) ? file_get_contents( $file ) : false;
if ( false === $css ) { fwrite( STDERR, "Missing canonical tokens.css\n" ); exit( 1 ); }
preg_match_all( '/--dealz-([a-z0-9-]+)\s*:\s*(#[0-9a-fA-F]{6})\s*;/i', $css, $matches, PREG_SET_ORDER );
$t = array();
foreach ( $matches as $m ) { $t[ $m[1] ] = strtoupper( $m[2] ); }
$required = array( 'ink','muted','label','placeholder','orange-text','white','cream','mint-50','mint-100','lime-50','orange-50','forest','primary','focus-outline','input-border','input-focus','lime' );
foreach ( $required as $name ) { if ( ! isset( $t[ $name ] ) ) { fwrite( STDERR, "Missing token --dealz-{$name}\n" ); exit( 1 ); } }
function dealz_lum( string $hex ): float {
    $c = array( hexdec( substr( $hex, 1, 2 ) ), hexdec( substr( $hex, 3, 2 ) ), hexdec( substr( $hex, 5, 2 ) ) );
    $v = array_map( static function ( $n ): float { $n /= 255; return $n <= 0.04045 ? $n / 12.92 : pow( ( $n + 0.055 ) / 1.055, 2.4 ); }, $c );
    return 0.2126 * $v[0] + 0.7152 * $v[1] + 0.0722 * $v[2];
}
function dealz_ratio( string $a, string $b ): float { $x = dealz_lum( $a ); $y = dealz_lum( $b ); return ( max( $x, $y ) + 0.05 ) / ( min( $x, $y ) + 0.05 ); }
$bad = array();
$check = static function ( string $label, string $a, string $b, float $min ) use ( &$bad ): void {
    $ratio = dealz_ratio( $a, $b );
    printf( "%-48s %0.5f:1 (min %0.2f)\n", $label, $ratio, $min );
    if ( $ratio + 0.0000001 < $min ) { $bad[] = $label . ' below ' . $min . ':1'; }
};
$light = array( 'white','cream','mint-50','mint-100','lime-50','orange-50' );
foreach ( array( 'ink','muted','label' ) as $fg ) { foreach ( $light as $bg ) { $check( $fg . ' on ' . $bg, $t[$fg], $t[$bg], 4.5 ); } }
$check( 'placeholder on white input', $t['placeholder'], $t['white'], 4.5 );
foreach ( array( 'white','cream','mint-50','lime-50','orange-50' ) as $bg ) { $check( 'orange text on ' . $bg, $t['orange-text'], $t[$bg], 4.5 ); }
$check( 'large orange text on mint-100', $t['orange-text'], $t['mint-100'], 3.0 );
$check( 'white on forest', $t['white'], $t['forest'], 4.5 );
$check( 'cream on forest', $t['cream'], $t['forest'], 4.5 );
$check( 'white on primary button', $t['white'], $t['primary'], 4.5 );
foreach ( $light as $bg ) { $check( 'solid focus outline on ' . $bg, $t['focus-outline'], $t[$bg], 3.0 ); }
$check( 'lime focus ring on forest', $t['lime'], $t['forest'], 3.0 );
foreach ( array( 'white','cream','mint-50' ) as $outer ) {
    $check( 'input border vs white input', $t['input-border'], $t['white'], 3.0 );
    $check( 'input border vs outer ' . $outer, $t['input-border'], $t[$outer], 3.0 );
}
$check( 'focused input border on white', $t['input-focus'], $t['white'], 3.0 );
if ( $bad ) { fwrite( STDERR, "\nContrast gate failed:\n- " . implode( "\n- ", $bad ) . "\n" ); exit( 1 ); }
echo "\nDealZ contrast matrix passed.\n";
