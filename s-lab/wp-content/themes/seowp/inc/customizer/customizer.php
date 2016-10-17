<?php
/**
 * Theme Customizer controls
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * This file describe all the controls we use in Theme Customizer.
 * We also extend default controls by creating value sliders,
 * custom color picker with transparency support, Goole Fonts, etc.
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */


/**
 * ----------------------------------------------------------------------
 * Adds body class .in-wp-customizer
 */
add_filter( 'body_class', 'lbmn_customizer_body_class' );

function lbmn_customizer_body_class( $classes = '' ) {
	if ( !empty( $GLOBALS['wp_customize'] ) ) {
		$classes[] = 'in-wp-customizer';
	}
	return $classes;
}

function lbmn_get_goolefonts() {
	$googlefonts = array(
		"ABeeZee"  => array( '400', '400italic' ),
		"Abel"=> array(),
		"Abril+Fatface"=> array(),
		"Aclonica"=> array(),
		"Acme"=> array(),
		"Actor"=> array(),
		"Adamina"=> array(),
		"Advent+Pro"  => array( '100', '200', '300', '400', '500', '600', '700' ),
		"Aguafina+Script" => array(),
		"Akronim" => array(),
		"Aladin" => array(),
		"Aldrich" => array(),
		"Alegreya"  => array( '400', '400italic', '700', '700italic', '900', '900italic' ),
		"Alegreya+SC"  => array( '400', '400italic', '700', '700italic', '900', '900italic' ),
		"Alex+Brush" => array(),
		"Alfa+Slab+One" => array(),
		"Alice" => array(),
		"Alike" => array(),
		"Alike+Angular" => array(),
		"Allan"  => array( '400', '700' ),
		"Allerta" => array(),
		"Allerta+Stencil" => array(),
		"Allura" => array(),
		"Almendra"  => array( '400', '400italic', '700', '700italic' ),
		"Almendra+SC" => array(),
		"Amarante" => array(),
		"Amaranth" => array( '400', '400italic', '700', '700italic' ),
		"Amatic+SC" => array( '400', '700' ),
		"Amethysta" => array(),
		"Anaheim" => array(),
		"Andada" => array(),
		"Andika" => array(),
		"Angkor" => array(),
		"Annie+Use+Your+Telescope" => array(),
		"Anonymous+Pro" => array( '400', '400italic', '700', '700italic' ),
		"Antic" => array(),
		"Antic+Didone" => array(),
		"Antic+Slab" => array(),
		"Anton" => array(),
		"Arapey" => array( '400', '400italic' ),
		"Arbutus" => array(),
		"Arbutus+Slab" => array(),
		"Architects+Daughter" => array(),
		"Archivo+Black" => array(),
		"Archivo+Narrow" => array( '400', '400italic', '700', '700italic' ),
		"Arimo" => array( '400', '400italic', '700', '700italic' ),
		"Arizonia" => array(),
		"Armata" => array(),
		"Artifika" => array(),
		"Arvo" => array( '400', '400italic', '700', '700italic' ),
		"Asap" => array( '400', '400italic', '700', '700italic' ),
		"Asset" => array(),
		"Astloch" => array( '400', '700' ),
		"Asul" => array( '400', '700' ),
		"Atomic+Age" => array(),
		"Aubrey" => array(),
		"Audiowide" => array(),
		"Autour+One" => array(),
		"Average" => array(),
		"Average+Sans" => array(),
		"Averia+Gruesa+Libre" => array(),
		"Averia+Libre" => array( '300', '300italic', '400', '400italic', '700', '700italic' ),
		"Averia+Sans+Libre" => array( '300', '300italic', '400', '400italic', '700', '700italic' ),
		"Averia+Serif+Libre" => array( '300', '300italic', '400', '400italic', '700', '700italic' ),
		"Bad+Script" => array(),
		"Balthazar" => array(),
		"Bangers" => array(),
		"Basic" => array(),
		"Battambang" => array( '400', '700' ),
		"Baumans" => array(),
		"Bayon" => array(),
		"Belgrano" => array(),
		"Belleza" => array(),
		"BenchNine" => array( '300', '400', '700' ),
		"Bentham" => array(),
		"Berkshire+Swash" => array(),
		"Bevan" => array(),
		"Bigshot+One" => array(),
		"Bilbo" => array(),
		"Bilbo+Swash+Caps" => array(),
		"Bitter" => array( '400', '400italic', '700' ),
		"Black+Ops+One" => array(),
		"Bokor" => array(),
		"Bonbon" => array(),
		"Boogaloo" => array(),
		"Bowlby+One" => array(),
		"Bowlby+One+SC" => array(),
		"Brawler" => array(),
		"Bree+Serif" => array(),
		"Bubblegum+Sans" => array(),
		"Bubbler+One" => array(),
		"Buda" => array( '300' ),
		"Buenard" => array( '400', '700' ),
		"Butcherman" => array(),
		"Butterfly+Kids" => array(),
		"Cabin" => array( '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic' ),
		"Cabin+Condensed" => array( '400', '500', '600', '700' ),
		"Cabin+Sketch" => array( '400', '700' ),
		"Caesar+Dressing" => array(),
		"Cagliostro" => array(),
		"Calligraffitti" => array(),
		"Cambo" => array(),
		"Candal" => array(),
		"Cantarell" => array( '400', '400italic', '700', '700italic' ),
		"Cantata+One" => array(),
		"Cantora+One" => array(),
		"Capriola" => array(),
		"Cardo" => array( '400', '400italic', '700' ),
		"Carme" => array(),
		"Carrois+Gothic" => array(),
		"Carrois+Gothic+SC" => array(),
		"Carter+One" => array(),
		"Caudex" => array( '400', '400italic', '700', '700italic' ),
		"Cedarville+Cursive" => array(),
		"Ceviche+One" => array(),
		"Changa+One" => array( '400', '400italic' ),
		"Chango" => array(),
		"Chau+Philomene+One" => array( '400', '400italic' ),
		"Chela+One" => array(),
		"Chelsea+Market" => array(),
		"Chenla" => array(),
		"Cherry+Cream+Soda" => array(),
		"Cherry+Swash" => array( '400', '700' ),
		"Chewy" => array(),
		"Chicle" => array(),
		"Chivo" => array( '400', '400italic', '900', '900italic' ),
		"Cinzel" => array( '400', '700', '900' ),
		"Cinzel+Decorative" => array( '400', '700', '900' ),
		"Coda" => array( '400', '800', '800' ),
		"Codystar" => array( '300' ),
		"Codystar" => array( '400' ),
		"Combo" => array(),
		"Comfortaa" => array( '300' ),
		"Comfortaa" => array( '400', '700' ),
		"Coming+Soon" => array(),
		"Concert+One" => array(),
		"Condiment" => array(),
		"Content" => array( '400', '700' ),
		"Contrail+One" => array(),
		"Convergence" => array(),
		"Cookie" => array(),
		"Copse" => array(),
		"Corben" => array( '400', '700' ),
		"Courgette" => array(),
		"Cousine" => array( '400', '400italic', '700', '700italic' ),
		"Coustard" => array( '400', '900' ),
		"Covered+By+Your+Grace" => array(),
		"Crafty+Girls" => array(),
		"Creepster" => array(),
		"Crete+Round" => array( '400', '400italic' ),
		"Crimson+Text" => array( '400', '400italic', '600', '600italic', '700', '700italic' ),
		"Crushed" => array(),
		"Cuprum" => array( '400', '400italic', '700', '700italic' ),
		"Cutive" => array(),
		"Cutive+Mono" => array(),
		"Damion" => array(),
		"Dancing+Script" => array( '400', '700' ),
		"Dangrek" => array(),
		"Dawning+of+a+New+Day" => array(),
		"Days+One" => array(),
		"Delius" => array(),
		"Delius+Swash+Caps" => array(),
		"Delius+Unicase" => array( '400', '700' ),
		"Della+Respira" => array(),
		"Devonshire" => array(),
		"Didact+Gothic" => array(),
		"Diplomata" => array(),
		"Diplomata+SC" => array(),
		"Doppio+One" => array(),
		"Dorsa" => array(),
		"Dosis" => array( '200', '300' ),
		"Dosis" => array( '400', '500', '600', '700', '800' ),
		"Dr+Sugiyama" => array(),
		"Droid+Sans" => array( '400', '700' ),
		"Droid+Sans+Mono" => array(),
		"Droid+Serif" => array( '400', '400italic', '700', '700italic' ),
		"Duru+Sans" => array(),
		"Dynalight" => array(),
		"EB+Garamond" => array(),
		"Eagle+Lake" => array(),
		"Eater" => array(),
		"Economica" => array( '400', '400italic', '700', '700italic' ),
		"Electrolize" => array(),
		"Emblema+One" => array(),
		"Emilys+Candy" => array(),
		"Engagement" => array(),
		"Enriqueta" => array( '400', '700' ),
		"Erica+One" => array(),
		"Esteban" => array(),
		"Euphoria+Script" => array(),
		"Ewert" => array(),
		"Exo" => array( '100', '100italic', '200', '200italic', '300', '300italic', '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic' ),
		"Expletus+Sans" => array( '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic' ),
		"Fanwood+Text" => array( '400', '400italic' ),
		"Fascinate" => array(),
		"Fascinate+Inline" => array(),
		"Faster+One" => array(),
		"Fasthand" => array(),
		"Federant" => array(),
		"Federo" => array(),
		"Felipa" => array(),
		"Fenix" => array(),
		"Finger+Paint" => array(),
		"Fjord+One" => array(),
		"Flamenco" => array( '300', '400' ),
		"Flavors" => array(),
		"Fondamento" => array( '400', '400italic' ),
		"Fontdiner+Swanky" => array(),
		"Forum" => array(),
		"Francois+One" => array(),
		"Fredericka+the+Great" => array(),
		"Fredoka+One" => array(),
		"Freehand" => array(),
		"Fresca" => array(),
		"Frijole" => array(),
		"Fugaz+One" => array(),
		"GFS+Didot" => array(),
		"GFS+Neohellenic" => array( '400', '400italic', '700', '700italic' ),
		"Galdeano" => array(),
		"Galindo" => array(),
		"Gentium+Basic" => array( '400', '400italic', '700', '700italic' ),
		"Gentium+Book+Basic" => array( '400', '400italic', '700', '700italic' ),
		"Geo" => array( '400', '400italic' ),
		"Geostar" => array(),
		"Geostar+Fill" => array(),
		"Germania+One" => array(),
		"Give+You+Glory" => array(),
		"Glass+Antiqua" => array(),
		"Glegoo" => array(),
		"Gloria+Hallelujah" => array(),
		"Goblin+One" => array(),
		"Gochi+Hand" => array(),
		"Gorditas" => array( '400', '700' ),
		"Goudy+Bookletter+1911" => array(),
		"Graduate" => array(),
		"Gravitas+One" => array(),
		"Great+Vibes" => array(),
		"Griffy" => array(),
		"Gruppo" => array(),
		"Gudea" => array( '400', '400italic', '700' ),
		"Habibi" => array(),
		"Hammersmith+One" => array(),
		"Handlee" => array(),
		"Hanuman" => array( '400', '700' ),
		"Happy+Monkey" => array(),
		"Headland+One" => array(),
		"Henny+Penny" => array(),
		"Herr+Von+Muellerhoff" => array(),
		"Holtwood+One+SC" => array(),
		"Homemade+Apple" => array(),
		"Homenaje" => array(),
		"IM+Fell+DW+Pica" => array( '400', '400italic' ),
		"IM+Fell+DW+Pica+SC" => array(),
		"IM+Fell+Double+Pica" => array( '400', '400italic' ),
		"IM+Fell+Double+Pica+SC" => array(),
		"IM+Fell+English" => array( '400', '400italic' ),
		"IM+Fell+English+SC" => array(),
		"IM+Fell+French+Canon" => array( '400', '400italic' ),
		"IM+Fell+French+Canon+SC" => array(),
		"IM+Fell+Great+Primer" => array( '400', '400italic' ),
		"IM+Fell+Great+Primer+SC" => array(),
		"Iceberg" => array(),
		"Iceland" => array(),
		"Imprima" => array(),
		"Inconsolata" => array( '400', '700' ),
		"Inder" => array(),
		"Indie+Flower" => array(),
		"Inika" => array( '400', '700' ),
		"Irish+Grover" => array(),
		"Istok+Web" => array( '400', '400italic', '700', '700italic' ),
		"Italiana" => array(),
		"Italianno" => array(),
		"Jacques+Francois" => array(),
		"Jacques+Francois+Shadow" => array(),
		"Jim+Nightshade" => array(),
		"Jockey+One" => array(),
		"Jolly+Lodger" => array(),
		"Josefin+Sans" => array( '100', '100italic', '300', '300italic', '400', '400italic', '600', '600italic', '700', '700italic' ),
		"Josefin+Slab" => array( '100', '100italic', '300', '300italic', '400', '400italic', '600', '600italic', '700', '700italic' ),
		"Judson" => array( '400', '400italic', '700' ),
		"Julee" => array(),
		"Julius+Sans+One" => array(),
		"Junge" => array(),
		"Jura" => array( '300' ),
		"Jura" => array( '400', '500', '600' ),
		"Just+Another+Hand" => array(),
		"Just+Me+Again+Down+Here" => array(),
		"Kameron" => array( '400', '700' ),
		"Karla" => array( '400', '400italic', '700', '700italic' ),
		"Kaushan+Script" => array(),
		"Kelly+Slab" => array(),
		"Kenia" => array(),
		"Khmer" => array(),
		"Kite+One" => array(),
		"Knewave" => array(),
		"Kotta+One" => array(),
		"Koulen" => array(),
		"Kranky" => array(),
		"Kreon" => array( '300', '400', '700' ),
		"Kristi" => array(),
		"Krona+One" => array(),
		"La+Belle+Aurore" => array(),
		"Lancelot" => array(),
		"Lato" => array( '100', '100italic', '300', '300italic', '400', '400italic', '700', '700italic', '900', '900italic' ),
		"League+Script" => array(),
		"Leckerli+One" => array(),
		"Ledger" => array(),
		"Lekton" => array( '400', '400italic', '700' ),
		"Lemon" => array(),
		"Life+Savers" => array(),
		"Lilita+One" => array(),
		"Limelight" => array(),
		"Linden+Hill" => array( '400', '400italic' ),
		"Lobster" => array(),
		"Lobster+Two" => array( '400', '400italic', '700', '700italic' ),
		"Londrina+Outline" => array(),
		"Londrina+Shadow" => array(),
		"Londrina+Sketch" => array(),
		"Londrina+Solid" => array(),
		"Lora" => array( '400', '400italic', '700', '700italic' ),
		"Love+Ya+Like+A+Sister" => array(),
		"Loved+by+the+King" => array(),
		"Lovers+Quarrel" => array(),
		"Luckiest+Guy" => array(),
		"Lusitana" => array( '400', '700' ),
		"Lustria" => array(),
		"Macondo" => array(),
		"Macondo+Swash+Caps" => array(),
		"Magra" => array( '400', '700' ),
		"Maiden+Orange" => array(),
		"Mako" => array(),
		"Marcellus" => array(),
		"Marcellus+SC" => array(),
		"Marck+Script" => array(),
		"Marko+One" => array(),
		"Marmelad" => array(),
		"Marvel" => array( '400', '400italic', '700', '700italic' ),
		"Mate" => array( '400', '400italic' ),
		"Mate+SC" => array(),
		"Maven+Pro" => array( '400', '500', '700', '900' ),
		"McLaren" => array(),
		"Meddon" => array(),
		"MedievalSharp" => array(),
		"Medula+One" => array(),
		"Megrim" => array(),
		"Meie+Script" => array(),
		"Merienda+One" => array(),
		"Merriweather" => array( '300', '400', '700', '900' ),
		"Metal" => array(),
		"Metal+Mania" => array(),
		"Metamorphous" => array(),
		"Metrophobic" => array(),
		"Michroma" => array(),
		"Miltonian" => array(),
		"Miltonian+Tattoo" => array(),
		"Miniver" => array(),
		"Miss+Fajardose" => array(),
		"Modern+Antiqua" => array(),
		"Molengo" => array(),
		"Molle" => array( '400italic' ),
		"Monofett" => array(),
		"Monoton" => array(),
		"Monsieur+La+Doulaise" => array(),
		"Montaga" => array(),
		"Montez" => array(),
		"Montserrat" => array( '400', '700' ),
		"Montserrat+Alternates" => array( '400', '700' ),
		"Montserrat+Subrayada" => array( '400', '700' ),
		"Moul" => array(),
		"Moulpali" => array(),
		"Mountains+of+Christmas" => array( '400', '700' ),
		"Mr+Bedfort" => array(),
		"Mr+Dafoe" => array(),
		"Mr+De+Haviland" => array(),
		"Mrs+Saint+Delafield" => array(),
		"Mrs+Sheppards" => array(),
		"Muli" => array( '300', '300italic', '400', '400italic' ),
		"Mystery+Quest" => array(),
		"Neucha" => array(),
		"Neuton" => array( '200', '300', '400', '400italic', '700', '800' ),
		"News+Cycle" => array( '400', '700' ),
		"Niconne" => array(),
		"Nixie+One" => array(),
		"Nobile" => array( '400', '400italic', '700', '700italic' ),
		"Nokora" => array( '400', '700' ),
		"Norican" => array(),
		"Nosifer" => array(),
		"Nothing+You+Could+Do" => array(),
		"Noticia+Text" => array( '400', '400italic', '700', '700italic' ),
		"Nova+Cut" => array(),
		"Nova+Flat" => array(),
		"Nova+Mono" => array(),
		"Nova+Oval" => array(),
		"Nova+Round" => array(),
		"Nova+Script" => array(),
		"Nova+Slim" => array(),
		"Nova+Square" => array(),
		"Numans" => array(),
		"Nunito" => array( '300', '400', '700' ),
		"Odor+Mean+Chey" => array(),
		"Offside" => array(),
		"Old+Standard+TT" => array( '400', '400italic', '700' ),
		"Oldenburg" => array(),
		"Oleo+Script" => array( '400', '700' ),
		"Open+Sans" => array( '300', '300italic', '400', '400italic', '600', '600italic', '700', '700italic', '800', '800italic' ),
		"Open+Sans+Condensed" => array( '300', '300italic', '700' ),
		"Oranienbaum" => array(),
		"Orbitron" => array( '400', '500', '700', '900' ),
		"Oregano" => array( '400', '400italic' ),
		"Orienta" => array(),
		"Original+Surfer" => array(),
		"Oswald" => array( '300', '400', '700' ),
		"Over+the+Rainbow" => array(),
		"Overlock" => array( '400', '400italic', '700', '700italic', '900', '900italic' ),
		"Overlock+SC" => array(),
		"Ovo" => array(),
		"Oxygen" => array( '300', '400', '700' ),
		"Oxygen+Mono" => array(),
		"PT+Mono" => array(),
		"PT+Sans" => array( '400', '400italic', '700', '700italic' ),
		"PT+Sans+Caption" => array( '400', '700' ),
		"PT+Sans+Narrow" => array( '400', '700' ),
		"PT+Serif" => array( '400', '400italic', '700', '700italic' ),
		"PT+Serif+Caption" => array( '400', '400italic' ),
		"Pacifico" => array(),
		"Paprika" => array(),
		"Parisienne" => array(),
		"Passero+One" => array(),
		"Passion+One" => array( '400', '700', '900' ),
		"Patrick+Hand" => array(),
		"Patua+One" => array(),
		"Paytone+One" => array(),
		"Peralta" => array(),
		"Permanent+Marker" => array(),
		"Petit+Formal+Script" => array(),
		"Petrona" => array(),
		"Philosopher" => array( '400', '400italic', '700', '700italic' ),
		"Piedra" => array(),
		"Pinyon+Script" => array(),
		"Plaster" => array(),
		"Play" => array( '400', '700' ),
		"Playball" => array(),
		"Playfair+Display" => array( '400', '400italic', '700', '700italic', '900', '900italic' ),
		"Playfair+Display+SC" => array( '400', '400italic', '700', '700italic', '900', '900italic' ),
		"Podkova" => array( '400', '700' ),
		"Poiret+One" => array(),
		"Poller+One" => array(),
		"Poly" => array( '400', '400italic' ),
		"Pompiere" => array(),
		"Pontano+Sans" => array(),
		"Port+Lligat+Sans" => array(),
		"Port+Lligat+Slab" => array(),
		"Prata" => array(),
		"Preahvihear" => array(),
		"Press+Start+2P" => array(),
		"Princess+Sofia" => array(),
		"Prociono" => array(),
		"Prosto+One" => array(),
		"Puritan" => array( '400', '400italic', '700', '700italic' ),
		"Quando" => array(),
		"Quantico" => array( '400', '400italic', '700', '700italic' ),
		"Quattrocento" => array( '400', '700' ),
		"Quattrocento+Sans" => array( '400', '400italic', '700', '700italic' ),
		"Questrial" => array(),
		"Quicksand" => array( '300', '400', '700' ),
		"Qwigley" => array(),
		"Racing+Sans+One" => array(),
		"Radley" => array( '400', '400italic', '100', '200' ),
		"Raleway" => array( '300', '400', '500', '600', '700', '800', '900' ),
		"Raleway+Dots" => array(),
		"Rammetto+One" => array(),
		"Ranchers" => array(),
		"Rancho" => array(),
		"Rationale" => array(),
		"Redressed" => array(),
		"Reenie+Beanie" => array(),
		"Revalia" => array(),
		"Ribeye" => array(),
		"Ribeye+Marrow" => array(),
		"Righteous" => array(),
		"Roboto" => array( '100', '100italic', '300', '300italic', '400', '400italic', '500', '500italic', '700', '700italic', '900', '900italic' ),
		"Roboto+Condensed" => array( '300', '300italic', '400', '400italic', '700', '700italic' ),
		"Roboto+Slab" => array( '100', '300', '400', '700' ),
		"Rochester" => array(),
		"Rock+Salt" => array(),
		"Rokkitt" => array( '400', '700' ),
		"Romanesco" => array(),
		"Ropa+Sans" => array( '400', '400italic' ),
		"Rosario" => array( '400', '400italic', '700', '700italic' ),
		"Rosarivo" => array( '400', '400italic' ),
		"Rouge+Script" => array(),
		"Ruda" => array( '400', '700', '900' ),
		"Ruge+Boogie" => array(),
		"Ruluko" => array(),
		"Ruslan+Display" => array(),
		"Russo+One" => array(),
		"Ruthie" => array(),
		"Rye" => array(),
		"Sail" => array(),
		"Salsa" => array(),
		"Sanchez" => array( '400', '400italic' ),
		"Sancreek" => array(),
		"Sansita+One" => array(),
		"Sarina" => array(),
		"Satisfy" => array(),
		"Scada" => array( '400', '400italic', '700', '700italic' ),
		"Schoolbell" => array(),
		"Seaweed+Script" => array(),
		"Sevillana" => array(),
		"Seymour+One" => array(),
		"Shadows+Into+Light" => array(),
		"Shadows+Into+Light+Two" => array(),
		"Shanti" => array(),
		"Share" => array( '400', '400italic', '700', '700italic' ),
		"Share+Tech" => array(),
		"Share+Tech+Mono" => array(),
		"Shojumaru" => array(),
		"Short+Stack" => array(),
		"Siemreap" => array(),
		"Sigmar+One" => array(),
		"Signika" => array( '300', '400', '600', '700' ),
		"Signika+Negative" => array( '300', '400', '600', '700' ),
		"Simonetta" => array( '400', '400italic', '900', '900italic' ),
		"Sirin+Stencil" => array(),
		"Six+Caps" => array(),
		"Skranji" => array( '400', '700' ),
		"Slackey" => array(),
		"Smokum" => array(),
		"Smythe" => array(),
		"Sniglet" => array( '800' ),
		"Snippet" => array(),
		"Sofadi+One" => array(),
		"Sofia" => array(),
		"Sonsie+One" => array(),
		"Sorts+Mill+Goudy" => array( '400', '400italic', '200' ),
		"Source+Code+Pro" => array( '300', '400', '600', '700', '900', '200', '200italic' ),
		"Source+Sans+Pro" => array( '300', '300italic', '400', '400italic', '600', '600italic', '700', '700italic', '900', '900italic' ),
		"Special+Elite" => array(),
		"Spicy+Rice" => array(),
		"Spinnaker" => array(),
		"Spirax" => array(),
		"Squada+One" => array(),
		"Stalinist+One" => array(),
		"Stardos+Stencil" => array( '400', '700' ),
		"Stint+Ultra+Condensed" => array(),
		"Stint+Ultra+Expanded" => array(),
		"Stoke" => array( '300', '400' ),
		"Strait" => array(),
		"Sue+Ellen+Francisco" => array(),
		"Sunshiney" => array(),
		"Supermercado+One" => array(),
		"Suwannaphum" => array(),
		"Swanky+and+Moo+Moo" => array(),
		"Syncopate" => array( '400', '700' ),
		"Tangerine" => array( '400', '700' ),
		"Taprom" => array(),
		"Telex" => array(),
		"Tenor+Sans" => array(),
		"The+Girl+Next+Door" => array(),
		"Tienne" => array( '400', '700', '900' ),
		"Tinos" => array( '400', '400italic', '700', '700italic' ),
		"Titan+One" => array(),
		"Titillium+Web" => array( '200', '200italic', '300', '300italic', '400', '400italic', '600', '600italic', '700', '700italic', '900' ),
		"Trade+Winds" => array(),
		"Trocchi" => array(),
		"Trochut" => array( '400', '400italic', '700' ),
		"Trykker" => array(),
		"Tulpen+One" => array(),
		"Ubuntu" => array( '300', '300italic', '400', '400italic', '500', '500italic', '700', '700italic' ),
		"Ubuntu+Condensed" => array(),
		"Ubuntu+Mono" => array( '400', '400italic', '700', '700italic' ),
		"Ultra" => array(),
		"Uncial+Antiqua" => array(),
		"Underdog" => array(),
		"Unica+One" => array(),
		"UnifrakturCook" => array( '700' ),
		"UnifrakturMaguntia" => array(),
		"Unkempt" => array( '400', '700' ),
		"Unlock" => array(),
		"Unna" => array(),
		"VT323" => array(),
		"Varela" => array(),
		"Varela+Round" => array(),
		"Vast+Shadow" => array(),
		"Vibur" => array(),
		"Vidaloka" => array(),
		"Viga" => array(),
		"Voces" => array(),
		"Volkhov" => array( '400', '400italic', '700', '700italic' ),
		"Vollkorn" => array( '400', '400italic', '700', '700italic' ),
		"Voltaire" => array(),
		"Waiting+for+the+Sunrise" => array(),
		"Wallpoet" => array(),
		"Walter+Turncoat" => array(),
		"Warnes" => array(),
		"Wellfleet" => array(),
		"Wire+One" => array(),
		"Yanone+Kaffeesatz" => array( '200', '300', '400', '700' ),
		"Yellowtail" => array(),
		"Yeseva+One" => array(),
		"Yesteryear" => array(),
		"Zeyada" => array()
	);

	return $googlefonts;
}




/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
function lbmn_customizer( $wp_customize ) {
	/**
	 * Get object of HeaderDesignPreset class with predesigned header settings
	 * TODO: it's a bad practice to transmit object like this.
	 * See: http://stackoverflow.com/a/889870
	 */
	global $header_design_presets;


	$color_opacity_options = array(
		'0'    => '0%',
		'0.03' => '3%',
		'0.05' => '5%',
		'0.07' => '7%',
		'0.1'  => '10%',
		'0.2'  => '20%',
		'0.3'  => '30%',
		'0.4'  => '40%',
		'0.5'  => '50%',
		'0.6'  => '60%',
		'0.7'  => '70%',
		'0.8'  => '80%',
		'0.9'  => '90%',
		'1'    => '100%',
	);

	$bg_image_repeat_options = array(
		'repeat'    => 'Repeat',
		'repeat-x'  => 'Repeat Horizontal',
		'repeat-y'  => 'Repeat Vertical',
		'no-repeat' => 'Do NOT Repeat',
	);

	$bg_image_position_options = array(
		'left top'      => 'Top Left',
		'center top'    => 'Top Center',
		'right top'     => 'Top Right',

		'left center'   => 'Center Left',
		'center center' => 'Center',
		'right center'  => 'Center Right',

		'left bottom'   => 'Bottom Left',
		'center bottom' => 'Bottom Center',
		'right bottom'  => 'Bottom Right',
	);

	$bg_image_attachment_options = array(
		'scroll'   => 'Scroll',
		'fixed'    => 'Fixed',
		// 'parallax' => 'Parallax', TODO: implement this in the future
	);

	$bg_image_size_options = array(
		'original' => 'Original',
		'cover'    => 'Cover',
		'contain'  => 'Contain',
	);

	/**
	 * ----------------------------------------------------------------------
	 * Custom control types classes
	 */

	/**
	 * Adds textarea support to the theme customizer
	 */
	class LBMN_Customize_Textarea_Control extends WP_Customize_Control {
		public $type = 'textarea';

		public function render_content() {
?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
				</label>
			<?php
		}
	}


	/**
	 * Adds subheader control to the theme customizer
	 */
	class LBMN_Customize_Subheader_Control extends WP_Customize_Control {
		public $type = 'subheader';

		public function render_content() {
?>
						<h3 class="customizer-subheader"><?php echo esc_html( $this->label ); ?></h3>
					<?php
		}
	}

	/**
	 * Adds custom color picker sliders support to the theme customizer
	 */
	class LBMN_Customize_Colorsliders_Control extends WP_Customize_Control {
		public $type = 'colorsliders';

		public function render_content() {
?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				</label>
				<div class="color-picker-slider-wrap">
					<input type="text" class="custom-color-picker-slider" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" data-color-format="rgb" />
				</div>
			<?php
		}
	}

	/**
	 * Adds slider support to the theme customizer
	 *
	 * http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/comment-page-1/#comment-11705
	 * http://pastebin.com/NcHT6RRP
	 */

	class LBMN_Customize_Slider_Control extends WP_Customize_Control {

		// setup control type
		public $type = 'slider';

		// function to enqueue the right jquery scripts and styles
		public function enqueue() {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-slider' );
		}

		// override content render function to output slider HTML
		public function render_content() { ?>

			<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<input type="text" class="customizer-slider__text" id="input_<?php echo $this->id; ?>" value="<?php echo $this->value(); ?>" <?php $this->link(); ?>/>
			</label>
			<div id="slider_<?php echo $this->id; ?>" class="customizer-slider__slider"></div>

			<script>
			jQuery(document).ready(function($) {
				<?php
					// prepare value and max/min values
					$value = floatval( str_replace('px', '', $this->value()) );
					$min   = floatval( str_replace('px', '', $this->choices['min']) );
					$max   = floatval( str_replace('px', '', $this->choices['max']) );
				?>

				$( "#slider_<?php echo $this->id; ?>" ).slider({
					value: <?php echo $value; ?>,
					min:   <?php echo $min; ?>,
					max:   <?php echo $max; ?>,
					step:  <?php echo $this->choices['step']; ?>,
					slide: function( event, ui ) {
						$( "#input_<?php echo $this->id; ?>" ).val(ui.value).keyup();
					}
				});
				$( "#input_<?php echo $this->id; ?>" ).val( $( "#slider_<?php echo $this->id; ?>" ).slider( "value" ) );

			});
			</script>

			<?php

		}
	}




	/**
	 * ----------------------------------------------------------------------
	 * Controls render functions
	 */

	/**
	 * Render section head
	 */
	function render_section_header( $label, $priority, $control_name, $description ) {
		global $wp_customize;
		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = 'customize-section-'.$control_name;
			}
			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_section(
				$control_name,
				array(
					'title' => $label,
					'description' => $description,
					'priority'=> $priority,
				)
			);
		}
	}

	/**
	 * Render subheader control
	 */
	function render_subheader_control( $label, $section, $priority ) {
		global $wp_customize;
		if ( isset( $label ) ) {
			$control_name = strtolower( $label );
			$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
			$control_name = str_replace( $invalid_characters, '', $control_name );
			$control_name = $section.'_'.$control_name;
			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting( $control_name );
			$wp_customize->add_control( new LBMN_Customize_Subheader_Control (
					$wp_customize,
					$control_name,
					array(
						'type'    => 'subheader',
						'label'   => $label,
						'section' => $section,
						'priority'=> $priority,
					)
				)
			);
		}
	}

	/**
	 * Render color picker control
	 */
	function render_colorpicker_control( $label, $section, $priority, $default = null, $control_name = null ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}
			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = '#fff'; }

			$wp_customize->add_setting(
				$control_name,
				array(
					'default' => $default,
					'sanitize_callback' => 'sanitize_hex_color', // sanitize_hex_color is build-in WP function
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$control_name,
					array(
						'label' => $label,
						'section' => $section,
						// 'settings' => $control_name, // by default it is id
						'priority'=> $priority,
					)
				)
			);
		}
	}

	/**
	 * Render custom (JQuery Color Picker Sliders) color picker control
	 */
	function render_colorpickersliders_control( $label, $section, $priority, $default = null, $control_name = null ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}
			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = '#fff'; }

			$wp_customize->add_setting(
				$control_name,
				array(
					'default' => $default,
					// 'sanitize_callback' => 'sanitize_hex_color', // sanitize_hex_color is build-in WP function
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new LBMN_Customize_Colorsliders_Control(
					$wp_customize,
					$control_name,
					array(
						'label' => $label,
						'section' => $section,
						// 'settings' => $control_name, // by default it is id
						'priority'=> $priority,
					)
				)
			);
		}
	}

	/**
	 * Render checkbox control
	 */
	function render_checkbox_control( $label, $section, $priority, $default, $control_name ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = ''; }
			// if ( isset($control_type) ) { // for some reason control type doesn't work for checkbox
			//  switch ( $control_type ) {
			//    case 'option':
			//      break;

			//    case 'theme_mod':
			//      break;

			//    default:
			//      $control_type = ''; // by default control type is 'theme_mod'
			//      break;
			//  }
			// } else {
			//  $control_type = '';
			// }

			$wp_customize->add_setting(
				$control_name,
				array(
					'default' => $default,
					'sanitize_callback' => 'lbmn_sanitize_checkbox',
					// 'type' => $control_type, // TODO: why control type doesn't work with checkbox (doesn't save value)
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'type' => 'checkbox',
					'label' => $label,
					'section' => $section,
					'priority'=> $priority,
				)
			);
		}
	}

	/**
	 * Render slider control
	 */
	function render_slider_control( $label, $section, $priority, $default, $control_name = null, $control_type = null, $choises ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = ''; }
			if ( isset( $control_type ) ) {
				switch ( $control_type ) {
				case 'option':
					break;

				case 'theme_mod':
					break;

				default:
					$control_type = ''; // by default control type is 'theme_mod'
					break;
				}
			} else {
				$control_type = '';
			}

			$wp_customize->add_setting(
				$control_name,
				array(
					'default' => $default,
					'sanitize_callback' => 'lbmn_sanitize_text',
					'transport' => 'postMessage',
					// 'type' => $control_type, // TODO: doesn't work. Don't save
				)
			);

			$wp_customize->add_control( new LBMN_Customize_Slider_Control (
					$wp_customize,
					$control_name,
					array(
						'type'     => 'text',
						'label'    => $label,
						'section'  => $section,
						'priority' => $priority,
						'choices'  => $choises
					)
				)
			);
		}
	}

	/**
	 * Render text control
	 */
	function render_text_control( $label, $section, $priority, $default, $control_name = null, $control_type = null ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = ''; }
			if ( isset( $control_type ) ) {
				switch ( $control_type ) {
				case 'option':
					break;

				case 'theme_mod':
					break;

				default:
					$control_type = ''; // by default control type is 'theme_mod'
					break;
				}
			} else {
				$control_type = '';
			}

			$wp_customize->add_setting(
				$control_name,
				array(
					'default' => $default,
					'sanitize_callback' => 'lbmn_sanitize_text',
					'transport' => 'postMessage',
					// 'type' => $control_type, // TODO: doesn't work. Don't save
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'type' => 'text',
					'label' => $label,
					'section' => $section,
					'priority'=> $priority,
				)
			);
		}
	}

	/**
	 * Render textarea control
	 */
	function render_textarea_control( $label, $section, $priority, $default, $control_name = null, $control_type = null ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }
			if ( !isset( $default ) ) { $default = ''; }
			if ( isset( $control_type ) ) {
				switch ( $control_type ) {
				case 'option':
					break;

				case 'theme_mod':
					break;

				default:
					$control_type = ''; // by default control type is 'theme_mod'
					break;
				}
			} else {
				$control_type = '';
			}

			$wp_customize->add_setting(
				$control_name,
				array(
					'default'           => $default,
					'sanitize_callback' => 'lbmn_sanitize_text',
					'transport'         => 'postMessage',
					// 'type' => $control_type, // TODO: doesn't work. Don't save
				)
			);

			$wp_customize->add_control( new LBMN_Customize_Textarea_Control (
					$wp_customize,
					$control_name,
					array(
						'type'     => 'text',
						'label'    => $label,
						'section'  => $section,
						'priority' => $priority,
					)
				)
			);

		}
	}

	/**
	 * Render menu control
	 */
	function render_menu_control( $label, $section, $priority, $control_name, $default_menu ) {
		global $wp_customize;

		// Generation menu drop down choices
		$menus   = wp_get_nav_menus();
		$menuchoices = array( 0 => __( '&mdash; Select &mdash;' , 'lbmn' ), );
		$default_menu_id = 0;

		foreach ( $menus as $menu ) {
			$menu_name = wp_html_excerpt( $menu->name, 40, '&hellip;' );
			$menuchoices[ $menu->term_id ] = $menu_name;

			if ( $default_menu == $menu_name ) { // get menu id for default icons menu
				$default_menu_id = $menu->term_id;
			}
		}

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				$control_name,
				array(
					'sanitize_callback' => 'absint', // it's a standard WP function
					'theme_supports'    => 'menus',
					'default'           => $default_menu_id,
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'label'    => $label,
					'type'     => 'select',
					'choices'  => $menuchoices,
					'section'  => $section,
					'priority' => $priority,
				)
			);
		}
	}

	/**
	 * Render select control
	 */
	function render_select_control( $label, $section, $priority, $control_name, $select_options, $default_select_option = '' ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				$control_name,
				array(
					// 'sanitize_callback' => 'absint', // TODO: how to sanitize it?
					'default' => $default_select_option,
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'label'    => $label,
					'type'     => 'select',
					'section'  => $section,
					'priority' => $priority,
					'choices'  => $select_options,
				)
			);
		}
	}

	function render_live_select_control( $label, $section, $priority, $control_name, $select_options, $default_select_option = '' ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				$control_name,
				array(
					// 'sanitize_callback' => 'absint', // TODO: how to sanitize it?
					'default' => $default_select_option,
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'label'    => $label,
					'type'     => 'select',
					'section'  => $section,
					'priority' => $priority,
					'choices'  => $select_options,
				)
			);
		}
	}

	/**
	 * Render Google Fonts selector
	 */
	function render_googlefonts_control( $label, $section, $priority, $control_name, $default_font ) {
		global $wp_customize;
		$googlefonts = lbmn_get_goolefonts ();

		// $fontchoices = array( '&mdash; None &mdash;' => '&mdash; None &mdash;');
		$fontchoices = array();

		foreach ( $googlefonts as $font => $font_ext ) {
			// Prepare font name
			$fontname = str_replace( '+', ' ', $font );
			// $fontname = str_replace(':', ': ', $fontname);
			// $fontname = str_replace('00', '00 ', $fontname);

			if ( count( $font_ext ) && $font != $font_ext ) {

				$fontname .= ': ';
				$first_custom_weight = true;

				foreach ( $font_ext as $weight ) {
					// if ( $weight == 'regular' ) {
					//  $weight = '400';
					// }

					$custom_font_style = $weight;

					if ( !stristr( $weight, 'italic' ) ) {
						$custom_font_weight = preg_replace( "/.*(\d{3}).*/", "$1", $weight );
						$custom_font_weight = intval( $custom_font_weight );

						if ( $custom_font_weight == 0 ) {
							$custom_font_weight = '400';
						}

						if ( !$first_custom_weight ) {
							$fontname .= ', ';
						}

						$fontname .= $custom_font_weight;
					} else {
						// $fontname .= $weight . ' ';
						$fontname .= '+i';
					}

					$first_custom_weight = false;
				}
			}

			$fontchoices[ $font ] = $fontname;
		}

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				$control_name,
				array(
					// 'sanitize_callback' => 'absint', // it's a standard WP function TODO: add sanitization here?
					// 'theme_supports'    => 'menus',
					'default'           => $default_font,
				)
			);

			$wp_customize->add_control(
				$control_name,
				array(
					'label'    => $label,
					'type'     => 'select',
					'choices'  => $fontchoices,
					'section'  => $section,
					'priority' => $priority,
				)
			);
		}
	}


	/**
	 * Render image upload
	 */
	class MediaLibraryCallClosure
	{
		 private $control_name;

		 function __construct( $control_name ) {
			  $this->control_name = $control_name;
		 }

		 function call() {
			  ?>
					<a class="choose-from-library-link button"
						 data-controller = "<?php esc_attr_e( $this->control_name ); ?>">
						 <?php _e( 'Open Library', 'lbmn' );?>
					</a>
			  <?php
			  // return term_meta_cmp($a, $b, $this->meta);
		 }
	}

	function render_image_control( $label, $section, $priority, $control_name, $default_image = '' ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				'lbmn_theme_options['.$control_name.']',
				array(
					'default'    => $default_image,
					'capability' => 'edit_theme_options',
					'type'       => 'option',
					// 'transport'  => 'postMessage', // live update
					// TODO: how to sanitize?
				) );

			$control = new WP_Customize_Image_Control(
				$wp_customize,
				$control_name,
				array(
					'label'    => $label,
					'section'  => $section,
					'priority' => $priority,
					'settings' => 'lbmn_theme_options['.$control_name.']',
				)
			);

			$control->add_tab(
				'library',   __( 'Media Library', 'lbmn' ),
				array(new MediaLibraryCallClosure($control_name), "call")
			);

			$wp_customize->add_control($control);


		}
	}

	/**
	 * Render file select
	 */
	function render_file_control( $label, $section, $priority, $control_name, $default = '' ) {
		global $wp_customize;

		if ( isset( $label ) ) {
			if ( !isset( $control_name ) ) {
				$control_name = strtolower( $label );
				$invalid_characters = array( "$", "%", "#", "<", ">", "|", " " );
				$control_name = str_replace( $invalid_characters, '', $control_name );
				$control_name = $section.'_'.$control_name;
			}

			if ( !isset( $priority ) ) { $priority = 20; }

			$wp_customize->add_setting(
				'lbmn_theme_options['.$control_name.']',
				array(
					'default'    => $default,
					'capability' => 'edit_theme_options',
					'type'       => 'option',
					// TODO: how to sanitize?
				) );

			$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize,
					$control_name,
					array(
						'label'    => $label,
						'section'  => $section,
						'priority' => $priority,
						'settings' => 'lbmn_theme_options['.$control_name.']',
					) ) );
		}
	}

	// Prepare font preset title
	function prepareFontPresetTitle( $preset_no ) {
		$font_preset_title =  'Font preset ' . $preset_no;

		if ( get_theme_mod( 'lbmn_font_preset_usegooglefont_' . $preset_no, 1 ) ) { // if Google Font activated

			if ( get_theme_mod( 'lbmn_font_preset_googlefont_' . $preset_no, '' ) ) {
				$font_preset_title .= ': ' . get_theme_mod( 'lbmn_font_preset_googlefont_' . $preset_no, '' );
			} else {
				$font_preset_title .= ': ' . get_theme_mod( 'lbmn_font_preset_googlefont_' . $preset_no, constant('LBMN_FONT_PRESET_GOOGLEFONT_'.$preset_no.'_DEFAULT') );
			}

		} elseif ( get_theme_mod( 'lbmn_font_preset_webfont_' . $preset_no, '' ) ) {
			$font_preset_title .= ': ' . get_theme_mod( 'lbmn_font_preset_webfont_' . $preset_no, '' );
		} else {
			$font_preset_title .= ': ' . get_theme_mod( 'lbmn_font_preset_standard_' . $preset_no, '' );
		}

		return $font_preset_title;
	}

	/**
	* ----------------------------------------------------------------------
	* Predefined arrays
	*/


	$font_size_options = array(
		'8px'  => '8px',
		'9px'  => '9px',
		'10px' => '10px',
		'11px' => '11px',
		'12px' => '12px',
		'13px' => '13px',
		'14px' => '14px',
		'15px' => '15px',
		'16px' => '16px',
		'18px' => '18px',
		'21px' => '21px',
		'24px' => '24px',
		'27px' => '27px',
		'32px' => '32px',
		'36px' => '36px',
		'48px' => '48px',
		'60px' => 'Oh man...',
	);

	$font_weight_options = array(
		'100' => '100 Thin',
		'200' => '200 Light',
		'300' => '300 Book',
		'400' => '400 Regular',
		'500' => '500 Medium',
		'600' => '600 DemiBold',
		'700' => '700 Bold',
		'800' => '800 ExtraBold',
		'900' => '900 Heavy',
	);

	$font_styling_options = array(
		'small'       => __( 'Small', 'lbmn' ),
		'medium'      => __( 'Medium', 'lbmn' ),
		'large'       => __( 'Large', 'lbmn' ),
		'divider-1'   => '&nbsp;',
		'caps-small'  => __( 'UPPERCASE: SMALL', 'lbmn' ),
		'caps-medium' => __( 'UPPERCASE: MEDIUM', 'lbmn' ),
		'caps-large'  => __( 'UPPERCASE: LARGE', 'lbmn' ),
	);

	$panel_height_options = array(
		'small'  => __( 'Small', 'lbmn' ),
		'medium' => __( 'Medium', 'lbmn' ),
		'large'  => __( 'Large', 'lbmn' ),
	);

	$font_preset_options = array(
		'1' => prepareFontPresetTitle( '1' ),
		'2' => prepareFontPresetTitle( '2' ),
		'3' => prepareFontPresetTitle( '3' ),
		'4' => prepareFontPresetTitle( '4' ),
	);

	$menu_align_options = array(
		'left'   => 'Left',
		'center' => 'Center',
		'right'  => 'Right',
	);

	$menu_iconposition_options = array(
		'left'    => 'Left',
		'top'     => 'Above',
		'right'   => 'Right',
		'disable_first_lvl' => 'Disable',
		'disable_globally' => 'Disable Icons Globaly',
	);

	$menu_separator_options = array(
		'none'   => 'None',
		'smooth' => 'Smooth',
		'sharp'  => 'Sharp',
	);

	$dropdown_animation_options = array(
		'none'   => 'None',
		'anim_1' => 'Unfold',
		'anim_2' => 'Fading',
		'anim_3' => 'Scale',
		'anim_4' => 'Down to Up',
		'anim_5' => 'Dropdown',
	);

	$logo_placement_options = array(
		// 'above-default' =>'Above menu',
		'top-left'     => 'Top-Left',
		'top-center'   => 'Top-Center',
		'top-right'    => 'Top-Right',
		'divider-1'    => '&nbsp;',
		'bottom-left'  => 'Botttom-Left',
		'bottom-right' => 'Botttom-Right',
	);

	/**
	 * ----------------------------------------------------------------------
	 * Notification panel section
	 */

	render_section_header ( 'Notification panel', 40, 'lbmn_notificationpanel', 'Site-wide notification panel settings' );
		render_checkbox_control ( 'Enable', 'lbmn_notificationpanel', 20, 1, 'lbmn_notificationpanel_switch' ); // Notification panel switch
		render_slider_control ( 'Height', 'lbmn_notificationpanel', 22, LBMN_NOTIFICATIONPANEL_HEIGHT_DEFAULT, 'lbmn_notificationpanel_height', null, array('min' => '40','max' => '120','step' => '2') );

		// Notification panel main controls
		// render_text_control ( 'Icon', 'lbmn_notificationpanel', 25, LBMN_NOTIFICATIONPANEL_DEFAULT ); // Notification panel icon
		render_textarea_control ( 'Message', 'lbmn_notificationpanel', 30, LBMN_NOTIFICATIONPANEL_MESSAGE_DEFAULT ); // Notification message
		render_text_control ( 'Button URL', 'lbmn_notificationpanel', 40, LBMN_NOTIFICATIONPANEL_BUTTONURL_DEFAULT );// Call to action button url

		// Colors
		render_colorpickersliders_control ( 'Background Color', 'lbmn_notificationpanel', 200, LBMN_NOTIFICATIONPANEL_BACKGROUNDCOLOR_DEFAULT ); // Top panel bg color
		render_colorpickersliders_control ( 'Text Color', 'lbmn_notificationpanel', 300, LBMN_NOTIFICATIONPANEL_TXTCOLOR_DEFAULT ); // Top panel text color
		render_colorpickersliders_control ( 'Background Color', 'lbmn_notificationpanel', 320, LBMN_NOTIFICATIONPANEL_BACKGROUNDCOLOR_HOVER_DEFAULT, 'lbmn_notificationpanel_backgroundcolor_hover' );
		render_colorpickersliders_control ( 'Text Color', 'lbmn_notificationpanel', 340, LBMN_NOTIFICATIONPANEL_TXTCOLOR_HOVER_DEFAULT, 'lbmn_notificationpanel_textcolor_hover' );

	/**
	 * ----------------------------------------------------------------------
	 * Top panel section
	 */

	render_section_header ( 'Top bar', 50, 'lbmn_topbar', 'Site-wide top panel settings' );
	render_checkbox_control ( 'Enable', 'lbmn_topbar', 20, 1, 'lbmn_topbar_switch' ); // Top panel switch
	render_slider_control ( 'Height', 'lbmn_topbar', 25, LBMN_TOPBAR_HEIGHT_DEFAULT, 'lbmn_topbar_height', null, array('min' => '30','max' => '80','step' => '2') );

	render_colorpickersliders_control ( 'Background Color', 'lbmn_topbar', 100, LBMN_TOPBAR_BACKGROUNDCOLOR_DEFAULT, 'lbmn_topbar_backgroundcolor' );
	render_colorpickersliders_control ( 'Link Color', 'lbmn_topbar', 110, LBMN_TOPBAR_LINKCOLOR_DEFAULT , 'lbmn_topbar_linkcolor');
	render_colorpickersliders_control ( 'Link Hover Color', 'lbmn_topbar', 120, LBMN_TOPBAR_LINKHOVERCOLOR_DEFAULT, 'lbmn_topbar_linkhovercolor' );
	render_colorpickersliders_control ( 'Link Hover Background Color', 'lbmn_topbar', 130, LBMN_TOPBAR_LINKHOVERBGCOLOR_DEFAULT, 'lbmn_topbar_linkhoverbackgroundcolor' );
	render_colorpickersliders_control ( 'Text Lines Color', 'lbmn_topbar', 140, LBMN_TOPBAR_TEXTCOLOR_DEFAULT, 'lbmn_topbar_textlinescolor' );

	render_live_select_control ( 'Font Family', 'lbmn_topbar', 310, 'lbmn_topbar_firstlevelitems_font', $font_preset_options, LBMN_TOPBAR_FIRSTLEVELITEMS_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_topbar', 330, 'lbmn_topbar_firstlevelitems_fontweight', $font_weight_options, LBMN_TOPBAR_FIRSTLEVELITEMS_FONTWEIGHT_DEFAULT ); //$font_weight_options // get_theme_mod( 'lbmn_headertop_firstlevelitems_font')
	render_slider_control ( 'Font Size (px)', 'lbmn_topbar', 340, LBMN_TOPBAR_FIRSTLEVELITEMS_FONTSIZE_DEFAULT, 'lbmn_topbar_firstlevelitems_fontsize', null, array('min' => '10','max' => '80','step' => '1') );

	render_live_select_control ( 'First Level Items Align', 'lbmn_topbar', 410, 'lbmn_topbar_firstlevelitems_align', $menu_align_options, LBMN_TOPBAR_FIRSTLEVELITEMS_ALIGN_DEFAULT );
	render_live_select_control ( 'Icon Position', 'lbmn_topbar', 420, 'lbmn_topbar_firstlevelitems_iconposition', $menu_iconposition_options, LBMN_TOPBAR_FIRSTLEVELITEMS_ICONPOSITION_DEFAULT );
	render_slider_control ( 'Icon Size (px)', 'lbmn_topbar', 430, LBMN_TOPBAR_FIRSTLEVELITEMS_ICONSIZE_DEFAULT, 'lbmn_topbar_firstlevelitems_iconsize', null, array('min' => '5','max' => '30','step' => '1') );
	render_live_select_control ( 'Items Separator', 'lbmn_topbar', 440, 'lbmn_topbar_firstlevelitems_separator', $menu_separator_options, LBMN_TOPBAR_FIRSTLEVELITEMS_SEPARATOR_DEFAULT );
	render_slider_control ( 'Items Separator Opacity', 'lbmn_topbar', 450, LBMN_TOPBAR_FIRSTLEVELITEMS_SEPARATOR_OPACITY_DEFAULT, 'lbmn_topbar_firstlevelitems_separator_opacity', null, array('min' => '0','max' => '1','step' => '0.01' ) );


	/**
	 * ----------------------------------------------------------------------
	 * Header top section
	 */

	render_section_header ( 'Header design', 63, 'lbmn_headertop', 'Site-wide header settings' );

	$header_design_preset_options = $header_design_presets->get_header_preset_list();
	// render_select_control ( 'Header design', 'lbmn_headertop', 10, 'lbmn_header_design', $header_design_preset_options, LBMN_HEADER_DESIGN_DEFAULT );

	render_colorpickersliders_control ( 'Background Color', 'lbmn_headertop', 185, LBMN_HEADERTOP_BACKGROUNDCOLOR_DEFAULT );
	render_slider_control ( 'Header Height', 'lbmn_headertop', 210, LBMN_HEADERTOP_HEIGHT_DEFAULT, 'lbmn_headertop_height', null, array('min' => '30','max' => '200','step' => '2') );
	render_slider_control ( 'Menu Height', 'lbmn_headertop', 215, LBMN_HEADERTOP_MENUHEIGHT_DEFAULT, 'lbmn_headertop_menu_height', null, array('min' => '30','max' => '100','step' => '2')  );

	render_checkbox_control ( 'Stick the menu on scroll', 'lbmn_headertop', 220, LBMN_HEADERTOP_STICK_SWITCH_DEFAULT, 'lbmn_headertop_stick' );
	render_colorpickersliders_control ( 'Background Color', 'lbmn_headertop', 225, LBMN_HEADERTOP_STICK_BACKGROUNDCOLOR_DEFAULT, 'lbmn_headertop_stick_backgroundcolor' );
	render_slider_control ( 'Sticky/Mobile Menu Height', 'lbmn_headertop', 230, LBMN_HEADERTOP_STICK_DEFAULT, 'lbmn_headertop_stick_height', null, array('min' => '30','max' => '100','step' => '2')  );
	render_slider_control ( 'Sticky/Mobile Menu Padding', 'lbmn_headertop', 234, LBMN_HEADERTOP_STICKY_PADDING_DEFAULT, 'lbmn_headertop_sticky_padding', null, array('min' => '0','max' => '60','step' => '2')  );
	render_slider_control ( 'Sticky Scroll Offset (save to apply)', 'lbmn_headertop', 240, LBMN_HEADERTOP_STICKYOFFSET_DEFAULT, 'lbmn_headertop_stickyoffset', null, array('min' => '0','max' => '400','step' => '2')  );


	/**
	 * ----------------------------------------------------------------------
	 * Website Logo section
	 */

	render_section_header ( 'Logo', 65, 'lbmn_logo', 'Website logotype settings' );
	render_live_select_control ( 'Placement:', 'lbmn_logo', 20, 'lbmn_logo_placement', $logo_placement_options, LBMN_LOGO_PLACEMENT_DEFAULT );
	render_image_control ( 'Normal Image', 'lbmn_logo', 30, 'lbmn_logo_image', LBMN_LOGO_IMAGE_DEFAULT ); //$default_image - optional parameter

	render_slider_control ( 'Maximum Logo Height (% to the height of the top header)', 'lbmn_logo', 50, LBMN_LOGO_IMAGE_HEIGHT_DEFAULT, 'lbmn_logo_height', null, array('min' => '10','max' => '100','step' => '1' ) );
	render_slider_control ( 'Margin top (optional)', 'lbmn_logo', 60, '', 'lbmn_logo_margin_top', null, array('min' => '-100','max' => '100','step' => '2' ) );
	render_slider_control ( 'Margin left (optional)', 'lbmn_logo', 65, '', 'lbmn_logo_margin_left', null, array('min' => '-100','max' => '100','step' => '2' ) );
	render_slider_control ( 'Margin right (optional)', 'lbmn_logo', 70, '', 'lbmn_logo_margin_right', null, array('min' => '-100','max' => '100','step' => '2' ) );

	/**
	 * ----------------------------------------------------------------------
	 * Mega Menu Settings
	 */

	render_section_header ( 'Mega Menu', 75, 'lbmn_megamenu', 'Site-wide header settings' );

	render_colorpickersliders_control ( 'Link Color', 'lbmn_megamenu', 110, LBMN_HEADERTOP_LINKCOLOR_DEFAULT , 'lbmn_megamenu_linkcolor');
	render_colorpickersliders_control ( 'Link Hover Color', 'lbmn_megamenu', 120, LBMN_HEADERTOP_LINKHOVERCOLOR_DEFAULT, 'lbmn_megamenu_linkhovercolor' );
	render_colorpickersliders_control ( 'Link Hover Background Color', 'lbmn_megamenu', 130, LBMN_MEGAMENU_LINKHOVERBACKGROUNDCOLOR_DEFAULT, 'lbmn_megamenu_linkhoverbackgroundcolor' );
	render_colorpickersliders_control ( 'Text Lines Color', 'lbmn_megamenu', 140, LBMN_HEADERTOP_TEXTCOLOR_DEFAULT, 'lbmn_megamenu_textlinescolor' );

	render_live_select_control ( 'Font Family', 'lbmn_megamenu', 310, 'lbmn_megamenu_firstlevelitems_font', $font_preset_options, LBMN_MEGAMENU_FIRSTLEVELITEMS_FONT_DEFAULT );
	render_live_select_control ( 'Font Weight', 'lbmn_megamenu', 320, 'lbmn_megamenu_firstlevelitems_fontweight', $font_weight_options, LBMN_MEGAMENU_FIRSTLEVELITEMS_FONTWEIGHT_DEFAULT ); //$font_weight_options // get_theme_mod( 'lbmn_headertop_firstlevelitems_font')
	render_slider_control ( 'Font Size (px)', 'lbmn_megamenu', 330, LBMN_MEGAMENU_FIRSTLEVELITEMS_FONTSIZE_DEFAULT, 'lbmn_megamenu_firstlevelitems_fontsize', null, array('min' => '10','max' => '36','step' => '1') );

	render_live_select_control ( 'First Level Items Align', 'lbmn_megamenu', 410, 'lbmn_megamenu_firstlevelitems_align', $menu_align_options, LBMN_MEGAMENU_FIRSTLEVELITEMS_ALIGN_DEFAULT );
	render_slider_control ( 'Hover/Active Border Radius', 'lbmn_megamenu', 420, LBMN_HEADERTOP_LINKHOVERBORDERRADIUS_DEFAULT, 'lbmn_megamenu_linkhoverborderradius', null, array('min' => '0','max' => '30','step' => '1') );
	render_slider_control ( 'First Level Items Outer Spacing', 'lbmn_megamenu', 430, LBMN_MEGAMENU_FIRSTLEVELITEMS_SPACING_DEFAULT, 'lbmn_megamenu_firstlevelitems_spacing', null, array('min' => '0','max' => '20','step' => '1') );
	render_slider_control ( 'First Level Items Inner Spacing', 'lbmn_megamenu', 440, LBMN_MEGAMENU_FIRSTLEVELITEMS_INNERSPACING_DEFAULT, 'lbmn_megamenu_firstlevelitems_innerspacing', null, array('min' => '0','max' => '20','step' => '1') );

	render_live_select_control ( 'Icon Position', 'lbmn_megamenu', 450, 'lbmn_megamenu_firstlevelitems_iconposition', $menu_iconposition_options, LBMN_MEGAMENU_FIRSTLEVELITEMS_ICONPOSITION_DEFAULT );
	render_slider_control ( 'Icon Size (px)', 'lbmn_megamenu', 460, LBMN_MEGAMENU_FIRSTLEVELITEMS_ICONSIZE_DEFAULT, 'lbmn_megamenu_firstlevelitems_iconsize', null, array('min' => '5','max' => '30','step' => '1') );
	render_live_select_control ( 'Items Separator', 'lbmn_megamenu', 470, 'lbmn_megamenu_firstlevelitems_separator', $menu_separator_options, LBMN_MEGAMENU_FIRSTLEVELITEMS_SEPARATOR_DEFAULT );
	render_slider_control ( 'Items Separator Opacity', 'lbmn_megamenu', 480, LBMN_MEGAMENU_FIRSTLEVELITEMS_SEPARATOR_OPACITY_DEFAULT, 'lbmn_megamenu_firstlevelitems_separator_opacity', null, array('min' => '0','max' => '1','step' => '.05' ) );
	render_slider_control ( 'Items Separator Opacity', 'lbmn_topbar', 490, LBMN_TOPBAR_FIRSTLEVELITEMS_SEPARATOR_OPACITY_DEFAULT, 'lbmn_topbar_firstlevelitems_separator_opacity', null, array('min' => '0','max' => '1','step' => '0.01' ) );

	/**
	 * ----------------------------------------------------------------------
	 * Mega Menu – Dropdown Settings
	 */

	render_section_header ( 'Mega Menu: Dropdown', 80, 'lbmn_megamenu_dropdown', 'Site-wide header settings' );

	render_colorpickersliders_control ( 'Text Color', 'lbmn_megamenu_dropdown', 140, LBMN_MEGAMENU_DROPDOWN_TEXTCOLOR_DEFAULT, 'lbmn_megamenu_dropdown_textcolor' );
	render_colorpickersliders_control ( 'Link Color', 'lbmn_megamenu_dropdown', 155, LBMN_MEGAMENU_DROPDOWN_LINKCOLOR_DEFAULT, 'lbmn_megamenu_dropdown_linkcolor' );
	render_colorpickersliders_control ( 'Link Hover Color', 'lbmn_megamenu_dropdown', 160, LBMN_MEGAMENU_DROPDOWN_LINKHOVERCOLOR_DEFAULT, 'lbmn_megamenu_dropdown_linkhovercolor' );
	render_colorpickersliders_control ( 'Link Hover Background Color', 'lbmn_megamenu_dropdown', 165, LBMN_MEGAMENU_DROPDOWN_LINKHOVERBACKGROUNDCOLOR_DEFAULT, 'lbmn_megamenu_dropdown_linkhoverbackgroundcolor' );
	render_colorpickersliders_control ( 'Dropdown Background Color', 'lbmn_megamenu_dropdown', 170, LBMN_MEGAMENU_DROPDOWN_BACKGROUND_DEFAULT, 'lbmn_megamenu_dropdown_background' );
	render_colorpickersliders_control ( 'Menu Items Divider Color', 'lbmn_megamenu_dropdown', 175, LBMN_MEGAMENU_DROPDOWN_MENUITEMSDIVIDERCOLOR_DEFAULT, 'lbmn_megamenu_dropdown_menuitemsdividercolor' );



	// render_subheader_control ( 'Fonts', 'lbmn_megamenu_dropdown', 300 );
	render_live_select_control ( 'Font Family', 'lbmn_megamenu_dropdown', 360, 'lbmn_megamenu_dropdown_font',       $font_preset_options, LBMN_MEGAMENU_DROPDOWN_FONT_DEFAULT );
	render_live_select_control ( 'Font Weight', 'lbmn_megamenu_dropdown', 370, 'lbmn_megamenu_dropdown_fontweight', $font_weight_options, LBMN_MEGAMENU_DROPDOWN_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_megamenu_dropdown', 380, LBMN_MEGAMENU_DROPDOWN_FONTSIZE_DEFAULT, 'lbmn_megamenu_dropdown_fontsize', null, array('min' => '10','max' => '30','step' => '1') );
	render_slider_control ( 'Icon Size (px)', 'lbmn_megamenu_dropdown', 390, LBMN_MEGAMENU_DROPDOWN_ICONSIZE_DEFAULT, 'lbmn_megamenu_dropdown_iconsize', null, array('min' => '10','max' => '30','step' => '1') );


	// render_subheader_control ( 'Menu settings', 'lbmn_megamenu', 400 );
	render_live_select_control ( 'Dropdowns Animation', 'lbmn_megamenu_dropdown', 415, 'lbmn_megamenu_dropdown_animation', $dropdown_animation_options, LBMN_MEGAMENU_DROPDOWN_ANIMATION_DEFAULT );
	render_slider_control ( 'Dropdown Panel Radius', 'lbmn_megamenu_dropdown', 420, LBMN_MEGAMENU_DROPDOWNRADIUS_DEFAULT, 'lbmn_megamenu_dropdownradius', null, array('min' => '0','max' => '20','step' => '1') );
	render_slider_control ( 'Dropdown Marker Opacity', 'lbmn_megamenu_dropdown', 430, LBMN_MEGAMENU_DROPDOWN_MARKEROPACITY_DEFAULT, 'lbmn_megamenu_dropdown_markeropacity', null, array('min' => '0','max' => '1','step' => '.01') );

	/**
	 * ----------------------------------------------------------------------
	 * Search Block
	 */

	$searchblock_placement_options = array(
		'topbar-default'     => __( 'Top panel:', 'lbmn' ),
		'topbar-left'        => __( '&mdash; Left', 'lbmn' ),
		'topbar-right'       => __( '&mdash; Right', 'lbmn' ),
		'divider-1'            => '&nbsp;',
		'headertop-default'    => __( 'Header top:', 'lbmn' ),
		'headertop-left'       => __( '&mdash; Left', 'lbmn' ),
		'headertop-right'      => __( '&mdash; Right', 'lbmn' ),
		'divider-2'            => '&nbsp;',
		'headerbottom-default' => __( 'Header bottom:', 'lbmn' ),
		'headerbottom-left'    => __( '&mdash; Left', 'lbmn' ),
		'headerbottom-right'   => __( '&mdash; Right', 'lbmn' ),
	);

	$searchblock_shadow_options = array(
		'inside'  => 'Inner shadow',
		'outside' => 'Outer shadow',
		'none'    => 'None'
	);

	render_section_header ( 'Search field', 90, 'lbmn_searchblock', 'Settings for the search block in the site header' );
	render_checkbox_control ( 'Enable', 'lbmn_searchblock', 20, 1, 'lbmn_searchblock_switch' ); // Top panel switch
	render_slider_control ( 'Input Field Width (px)', 'lbmn_searchblock', 110, LBMN_SEARCHBLOCK_INPUTFIELDWIDTH_DEFAULT, 'lbmn_searchblock_inputfieldwidth', null, array('min' => '100','max' => '300','step' => '2') );
	render_slider_control ( 'Input Field Radius (px)', 'lbmn_searchblock', 115, LBMN_SEARCHBLOCK_INPUTFIELDRADIUS_DEFAULT, 'lbmn_searchblock_inputfieldradius', null, array('min' => '0','max' => '100','step' => '1') );
	render_live_select_control ( 'Input Field Shadow', 'lbmn_searchblock', 120, 'lbmn_searchblock_shadow', $searchblock_shadow_options, LBMN_SEARCHBLOCK_SHADOW_DEFAULT );
	render_colorpickersliders_control ( 'Input Background Color', 'lbmn_searchblock', 200, LBMN_SEARCHBLOCK_INPUTBACKGROUNDCOLOR_DEFAULT );
	render_colorpickersliders_control ( 'Text and Icon Color', 'lbmn_searchblock', 210, LBMN_SEARCHBLOCK_TEXTANDICONCOLOR_DEFAULT, 'lbmn_searchblock_textandiconcolor' );


	/**
	 * ----------------------------------------------------------------------
	 * Sidebar settings (not needed since using Live Composer)
	 */
/*
	$active_customposttypes_options = array(
		"none"  => "No sidebar (full width)",
		"right" => "Sidebar on the right",
		"left"  => "Sidebar on the left",
	);

	render_section_header ( 'Sidebar', 200, 'lbmn_sidebar', 'Here you can set default sidebar position for the post types available.' );

	$post_types = get_post_types( '', 'objects' ); // output all the custom post types

	// remove some standard WordPress post types we are not interested in
	unset ( $post_types['revision'] );
	unset ( $post_types['nav_menu_item'] );
	unset ( $post_types['attachment'] );

	// remove standard WooCommerce post types
	unset ( $post_types['product'] );
	unset ( $post_types['shop_coupon'] );
	unset ( $post_types['shop_order'] );
	unset ( $post_types['product_variation'] );

	// remove WooSidebars content type
	unset ( $post_types['sidebar'] );

	// remove our "Section Slider" post type
	unset ( $post_types['section_slider'] );

	foreach ( $post_types as $post_type ) {
		// only blog posts has sidebar right enabled by default
		if ( $post_type->name == 'post' ) {
			render_select_control ( $post_type->label, 'lbmn_sidebar', 20, 'lbmn_sidebar_list'.$post_type->name, $active_customposttypes_options, 'right' );
		} else {
			render_select_control ( $post_type->label, 'lbmn_sidebar', 20, 'lbmn_sidebar_list'.$post_type->name, $active_customposttypes_options, 'none' );
		}
	}
*/

	// -------------------------------------------------------------------------
	// Page Layout Section
	// -------------------------------------------------------------------------

	render_section_header         ( 'Page Layout and Background', 240, 'page_layout', 'Here you can customize colors.' );

		render_colorpickersliders_control ( 'Content Background Color', 'page_layout', 10, LBMN_CONTENT_BACKGROUND_COLOR_DEFAULT, 'lbmn_content_background_color' );
		render_checkbox_control    ( 'Make Page Layout Boxed ', 'page_layout', 15, LBMN_PAGELAYOUTBOXED_SWITCH_DEFAULT, 'lbmn_pagelayoutboxed_switch'); // Botttom panel switch
		render_colorpickersliders_control ( 'Background Color', 'page_layout', 20, LBMN_PAGEBACKGROUNDCOLOR_DEFAULT, 'lbmn_page_background_color' );
		render_image_control       ( 'Background Image', 'page_layout', 30, 'lbmn_page_background_image' ); //$default_image - optional parameter
		render_slider_control 		( 'Background Image Opacity', 'page_layout', 40, '1', 'lbmn_page_background_image_opacity', null, array('min' => '0','max' => '1','step' => '.01') );
		render_live_select_control ( 'Background Image Repeat', 'page_layout', 50, 'lbmn_page_background_image_repeat', $bg_image_repeat_options, 'repeat');
		render_live_select_control ( 'Background Image Position', 'page_layout', 60, 'lbmn_page_background_image_position', $bg_image_position_options, 'center top');
		render_live_select_control ( 'Background Image Attachment', 'page_layout', 70, 'lbmn_page_background_image_attachment', $bg_image_attachment_options, 'scroll');
		render_live_select_control ( 'Background Image Size', 'page_layout', 80, 'lbmn_page_background_image_size', $bg_image_size_options, 'original');

	/**
	 * ----------------------------------------------------------------------
	 * Typography section
	 */

	render_section_header ( 'Typography and Text Colors', 250, 'lbmn_typography', 'All the setings to make your text looks awesome.' );

	render_colorpickersliders_control ( 'Link Color', 'lbmn_typography', 10, LBMN_TYPOGRAPHY_LINK_COLOR_DEFAULT, 'lbmn_typography_link_color' );
	render_colorpickersliders_control ( 'Link Hover Color', 'lbmn_typography', 20, LBMN_TYPOGRAPHY_LINK_HOVER_COLOR_DEFAULT, 'lbmn_typography_link_hover_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 110, 'lbmn_typography_p_font', $font_preset_options, LBMN_TYPOGRAPHY_P_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 130, 'lbmn_typography_p_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_P_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 140, LBMN_TYPOGRAPHY_P_FONTSIZE_DEFAULT, 'lbmn_typography_p_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 150, LBMN_TYPOGRAPHY_P_LINEHEIGHT_DEFAULT, 'lbmn_typography_p_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 160, LBMN_TYPOGRAPHY_P_MARGINBOTTOM_DEFAULT, 'lbmn_typography_p_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 170, LBMN_TYPOGRAPHY_P_COLOR_DEFAULT, 'lbmn_typography_p_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 210, 'lbmn_typography_h1_font', $font_preset_options, LBMN_TYPOGRAPHY_H1_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 230, 'lbmn_typography_h1_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H1_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 240, LBMN_TYPOGRAPHY_H1_FONTSIZE_DEFAULT, 'lbmn_typography_h1_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 250, LBMN_TYPOGRAPHY_H1_LINEHEIGHT_DEFAULT, 'lbmn_typography_h1_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 260, LBMN_TYPOGRAPHY_H1_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h1_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 270, LBMN_TYPOGRAPHY_H1_COLOR_DEFAULT, 'lbmn_typography_h1_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 310, 'lbmn_typography_h2_font', $font_preset_options, LBMN_TYPOGRAPHY_H2_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 330, 'lbmn_typography_h2_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H2_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 340, LBMN_TYPOGRAPHY_H2_FONTSIZE_DEFAULT, 'lbmn_typography_h2_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 350, LBMN_TYPOGRAPHY_H2_LINEHEIGHT_DEFAULT, 'lbmn_typography_h2_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 360, LBMN_TYPOGRAPHY_H2_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h2_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 370, LBMN_TYPOGRAPHY_H2_COLOR_DEFAULT, 'lbmn_typography_h2_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 410, 'lbmn_typography_h3_font', $font_preset_options, LBMN_TYPOGRAPHY_H3_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 430, 'lbmn_typography_h3_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H3_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 440, LBMN_TYPOGRAPHY_H3_FONTSIZE_DEFAULT, 'lbmn_typography_h3_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 450, LBMN_TYPOGRAPHY_H3_LINEHEIGHT_DEFAULT, 'lbmn_typography_h3_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 460, LBMN_TYPOGRAPHY_H3_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h3_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 470, LBMN_TYPOGRAPHY_H3_COLOR_DEFAULT, 'lbmn_typography_h3_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 510, 'lbmn_typography_h4_font', $font_preset_options, LBMN_TYPOGRAPHY_H4_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 530, 'lbmn_typography_h4_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H4_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 540, LBMN_TYPOGRAPHY_H4_FONTSIZE_DEFAULT, 'lbmn_typography_h4_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 550, LBMN_TYPOGRAPHY_H4_LINEHEIGHT_DEFAULT, 'lbmn_typography_h4_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 560, LBMN_TYPOGRAPHY_H4_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h4_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 570, LBMN_TYPOGRAPHY_H4_COLOR_DEFAULT, 'lbmn_typography_h4_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 610, 'lbmn_typography_h5_font', $font_preset_options, LBMN_TYPOGRAPHY_H5_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 630, 'lbmn_typography_h5_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H5_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 640, LBMN_TYPOGRAPHY_H5_FONTSIZE_DEFAULT, 'lbmn_typography_h5_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 650, LBMN_TYPOGRAPHY_H5_LINEHEIGHT_DEFAULT, 'lbmn_typography_h5_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 660, LBMN_TYPOGRAPHY_H5_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h5_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 670, LBMN_TYPOGRAPHY_H5_COLOR_DEFAULT, 'lbmn_typography_h5_color' );

	render_live_select_control ( 'Font Family', 'lbmn_typography', 710, 'lbmn_typography_h6_font', $font_preset_options, LBMN_TYPOGRAPHY_H6_FONT_DEFAULT);
	render_live_select_control ( 'Font Weight', 'lbmn_typography', 730, 'lbmn_typography_h6_fontweight', $font_weight_options, LBMN_TYPOGRAPHY_H6_FONTWEIGHT_DEFAULT );
	render_slider_control ( 'Font Size (px)', 'lbmn_typography', 740, LBMN_TYPOGRAPHY_H6_FONTSIZE_DEFAULT, 'lbmn_typography_h6_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
	render_slider_control ( 'Line Height (px)', 'lbmn_typography', 750, LBMN_TYPOGRAPHY_H6_LINEHEIGHT_DEFAULT, 'lbmn_typography_h6_lineheight', null, array('min' => '5','max' => '100','step' => '1') );
	render_slider_control ( 'Margin Bottom (px)', 'lbmn_typography', 760, LBMN_TYPOGRAPHY_H6_MARGINBOTTOM_DEFAULT, 'lbmn_typography_h6_marginbottom', null, array('min' => '0','max' => '100','step' => '1') );
	render_colorpickersliders_control ( 'Font Color', 'lbmn_typography', 770, LBMN_TYPOGRAPHY_H6_COLOR_DEFAULT, 'lbmn_typography_h6_color' );

	/**
	 * ----------------------------------------------------------------------
	 * Font Sets
	 */

	/**
	 * http://www.google.com/fonts/#ReviewPlace:refine/Collection:Merriweather+Sans|Roboto+Condensed|Roboto|Oxygen|Dosis|Titillium+Web|Ubuntu|Lato|Raleway|Signika+Negative|Kreon|Open+Sans
	 * http://www.google.com/fonts/#ReviewPlace:refine/Collection:Merriweather:400,300|Lora|Rufina|Playfair+Display|Libre+Baskerville|Domine|Noto+Serif
	 */

	$typography_standard_fonts = array(
		'arial'               => 'Sans-serif > Standard: Arial',
		'helvetica'           => 'Sans-serif > Standard: Helvetica',
		'lucida-sans-unicode' => 'Sans-serif > Standard: Lucida Sans Unicode',
		'century-gothic'      => 'Sans-serif > Modern: Century Gothic',
		'divider-1'           => '&nbsp;',
		'arial-narrow'        => 'Sans-serif > Narrow: Arial Narrow',
		'impact'              => 'Sans-serif > Narrow Heavy: Impact',
		'arial-black'         => 'Sans-serif > Heavy: Arial Black',
		'divider-2'           => '&nbsp;',
		'cambria'             => 'Serif > Standard: Cambria',
		'verdana'             => 'Serif > Standard: Verdana',
		'constantia'          => 'Serif > Modern: Constantia',
		'bookman-old-style'   => 'Serif > Old Style: Bookman Old Style',
	);

	render_section_header ( 'Font Presets', 255, 'lbmn_fonts', 'All the setings to make your website looks awesome.' );

	// Web font preset 1
	render_subheader_control ( 'Font Preset 1', 'lbmn_fonts', 100 );
	render_select_control ( 'Standard Font', 'lbmn_fonts', 110, 'lbmn_font_preset_standard_1', $typography_standard_fonts, LBMN_FONT_PRESET_STANDARD_1_DEFAULT );
	render_googlefonts_control ( 'Google Web Font', 'lbmn_fonts', 120, 'lbmn_font_preset_googlefont_1', LBMN_FONT_PRESET_GOOGLEFONT_1_DEFAULT );
	render_text_control ( 'Custom Web Font Name', 'lbmn_fonts', 130, '', 'lbmn_font_preset_webfont_1' );
	render_checkbox_control ( 'Use Google Web Fonts', 'lbmn_fonts', 140, 1, 'lbmn_font_preset_usegooglefont_1' );

	// Web font preset 2
	render_subheader_control ( 'Font Preset 2', 'lbmn_fonts', 200 );
	render_select_control ( 'Standard Font', 'lbmn_fonts', 210, 'lbmn_font_preset_standard_2', $typography_standard_fonts, LBMN_FONT_PRESET_STANDARD_2_DEFAULT );
	render_googlefonts_control ( 'Google Web Font', 'lbmn_fonts', 220, 'lbmn_font_preset_googlefont_2', LBMN_FONT_PRESET_GOOGLEFONT_2_DEFAULT );
	render_text_control ( 'Custom Web Font Name', 'lbmn_fonts', 230, '', 'lbmn_font_preset_webfont_2' );
	render_checkbox_control ( 'Use Google Web Fonts', 'lbmn_fonts', 240, 1, 'lbmn_font_preset_usegooglefont_2' );

	// Web font preset 3
	render_subheader_control ( 'Font Preset 3', 'lbmn_fonts', 300 );
	render_select_control ( 'Standard Font', 'lbmn_fonts', 310, 'lbmn_font_preset_standard_3', $typography_standard_fonts, LBMN_FONT_PRESET_STANDARD_3_DEFAULT );
	render_googlefonts_control ( 'Google Web Font', 'lbmn_fonts', 320, 'lbmn_font_preset_googlefont_3', LBMN_FONT_PRESET_GOOGLEFONT_3_DEFAULT );
	render_text_control ( 'Custom Web Font Name', 'lbmn_fonts', 330, '', 'lbmn_font_preset_webfont_3' );
	render_checkbox_control ( 'Use Google Web Fonts', 'lbmn_fonts', 340, 1, 'lbmn_font_preset_usegooglefont_3' );

	// Web font preset 4
	render_subheader_control ( 'Font Preset 4', 'lbmn_fonts', 400 );
	render_select_control ( 'Standard Font', 'lbmn_fonts', 410, 'lbmn_font_preset_standard_4', $typography_standard_fonts, LBMN_FONT_PRESET_STANDARD_4_DEFAULT );
	render_googlefonts_control ( 'Google Web Font', 'lbmn_fonts', 420, 'lbmn_font_preset_googlefont_4', LBMN_FONT_PRESET_GOOGLEFONT_4_DEFAULT );
	render_text_control ( 'Custom Web Font Name', 'lbmn_fonts', 430, '', 'lbmn_font_preset_webfont_4' );
	render_checkbox_control ( 'Use Google Web Fonts', 'lbmn_fonts', 440, 1, 'lbmn_font_preset_usegooglefont_4' );

	// Advanced font settings
	render_subheader_control ( 'Additional character sets', 'lbmn_fonts', 500 );
	render_checkbox_control ( 'Latin Extended', 'lbmn_fonts', 510, 0, 'lbmn_font_characterset_latinextended' );
	render_checkbox_control ( 'Cyrillic', 'lbmn_fonts', 530, 0, 'lbmn_font_characterset_cyrillic' );
	render_checkbox_control ( 'Cyrillic Extended', 'lbmn_fonts', 540, 0, 'lbmn_font_characterset_cyrillicextended' );
	render_checkbox_control ( 'Greek', 'lbmn_fonts', 550, 0, 'lbmn_font_characterset_greek' );
	render_checkbox_control ( 'Greek Extended', 'lbmn_fonts', 560, 0, 'lbmn_font_characterset_greekextended' );
	render_checkbox_control ( 'Vietnamese', 'lbmn_fonts', 570, 0, 'lbmn_font_characterset_vietnamese' );

	/**
	* ----------------------------------------------------------------------
	* "Call to action" section
	*/

	render_section_header      ( 'Call-to-action area', 260, 'lbmn_calltoaction', 'Site-wide call to action area settings' );
		render_checkbox_control ( 'Enable', 'lbmn_calltoaction', 20, 1, 'lbmn_calltoaction_switch' ); // Top panel switch
		render_slider_control ( 'Height', 'lbmn_calltoaction', 22, LBMN_CALLTOACTION_HEIGHT_DEFAULT, 'lbmn_calltoaction_height', null, array('min' => '60','max' => '200','step' => '2') );

		// Message elements
		render_text_control ( 'Call to action Message', 'lbmn_calltoaction', 40, LBMN_CALLTOACTION_MESSAGE_DEFAULT, 'lbmn_calltoaction_message' );// Call to action
		render_text_control ( 'Link', 'lbmn_calltoaction', 70, LBMN_CALLTOACTION_URL_DEFAULT, 'lbmn_calltoaction_url' );
		// Font
		render_live_select_control ( 'Font Family', 'lbmn_calltoaction', 80, 'lbmn_calltoaction_font', $font_preset_options, LBMN_CALLTOACTION_FONT_DEFAULT );
		render_live_select_control ( 'Font Weight', 'lbmn_calltoaction', 90,'lbmn_calltoaction_fontweight', $font_weight_options, LBMN_CALLTOACTION_FONTWEIGHT_DEFAULT );
		render_slider_control ( 'Font Size (px)', 'lbmn_calltoaction', 100, LBMN_CALLTOACTION_FONTSIZE_DEFAULT, 'lbmn_calltoaction_fontsize', null, array('min' => '10','max' => '80','step' => '1') );
		// Colors
		render_colorpickersliders_control ( 'Background Color', 'lbmn_calltoaction', 110, LBMN_CALLTOACTION_BACKGROUNDCOLOR_DEFAULT );
		render_colorpickersliders_control ( 'Text Color', 'lbmn_calltoaction', 120, LBMN_CALLTOACTION_TXTCOLOR_DEFAULT );
		render_colorpickersliders_control ( 'Background Color', 'lbmn_calltoaction', 130, LBMN_CALLTOACTION_BACKGROUNDCOLOR_HOVER_DEFAULT, 'lbmn_calltoaction_backgroundcolor_hover' );
		render_colorpickersliders_control ( 'Text Color', 'lbmn_calltoaction', 140, LBMN_CALLTOACTION_TXTCOLOR_HOVER_DEFAULT, 'lbmn_calltoaction_textcolor_hover' );

	/**
	 * ----------------------------------------------------------------------
	 * Website footer section
	 */

		// Set array with all the footers listed
		// See /inc/functions-extras.php for lbmn_return_all_footers()
		$footer_designs_options = lbmn_return_all_footers();
		$footer_designs_options = array('disable' => __('Disable','lbmn')) + $footer_designs_options;
		// array_unshift($footer_designs_options, 'disabled' => __('Disable','lbmn'));
		// $footer_designs_options['disabled'] = __('Disable','lbmn');


		// Get default footer custom post id
		$footer_design_default = get_page_by_title(LBMN_FOOTER_DESIGN_TITLE_DEFAULT, 'ARRAY_A', 'lbmn_footer');
		if ( isset($footer_design_default) ) {
			$footer_design_default = $footer_design_default['ID'];
		} else {
			$footer_design_default = '';
		}

	render_section_header ( 'Website Footer', 620, 'lbmn_footer', 'All the setings to make your footer looks awesome.' );
		render_select_control ( 'Default Footer Design', 'lbmn_footer', 10, 'lbmn_footer_design', $footer_designs_options, $footer_design_default );

	/**
	 * ----------------------------------------------------------------------
	 * Other settings
	 */

	// $footer_designs_options = array('disable' => __('Disable','lbmn'));

	// Prepare array of all stystem page templates
	$system_page_templates = array();

		//WordPress loop for custom post type
		$system_pages_request = new WP_Query('post_type=lbmn_archive&posts_per_page=-1');
		while ($system_pages_request->have_posts()) {
			$system_pages_request->the_post();
			$system_page_templates[ get_the_ID() ] = get_the_title();
		}  wp_reset_query();

	render_section_header ( 'System Page Templates', 700, 'lbmn_more', 'Site-wide call to action area settings' );
		render_live_select_control ( '404 Error: Page Not Found', 'lbmn_more', 5,'lbmn_systempage_404', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Search: Results List', 'lbmn_more', 10,'lbmn_systempage_searchresults', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Search: No Results', 'lbmn_more', 20,'lbmn_systempage_nosearchresults', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_NOSEARCHRESULTS_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Archive: Category', 'lbmn_more', 30,'lbmn_systempage_category', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_CATEGORY_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Archive: Tag', 'lbmn_more', 40,'lbmn_systempage_tag', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_TAG_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Archive: Date', 'lbmn_more', 50,'lbmn_systempage_date', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_DATE_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Archive: Authors', 'lbmn_more', 60,'lbmn_systempage_authors', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_AUTHORS_DEFAULT, 'lbmn_archive' ) );
		render_live_select_control ( 'Front Page: Posts List', 'lbmn_more', 70,'lbmn_systempage_frontpage_posts', $system_page_templates, lbmn_get_page_by_title( LBMN_SYSTEMPAGE_FRONTPAGE_POSTS_DEFAULT, 'lbmn_archive' ) );

	/**
	 * ----------------------------------------------------------------------
	 * Remove some standard WP sections from Theme Customizer
	 */

	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'nav' );
	$wp_customize->remove_section( 'static_front_page' );
	$wp_customize->remove_section( 'background_image' );
}
add_action( 'customize_register', 'lbmn_customizer' );


/**
 * ----------------------------------------------------------------------
 * Customizer data sanitization
 */

function lbmn_sanitize_text( $input ) {
	return wp_kses_post( force_balance_tags( $input ) );
	// Sanitize content for allowed HTML tags for post content.
}

function lbmn_sanitize_checkbox( $input ) {
	if ( $input == 1 ) {
		return 1;
	} else {
		return '';
	}
}

function lbmn_sanitize_sidebar_position( $input ) {
	$valid = array( 'left', 'none', 'right', );

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function lbmn_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'lbmn_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function lbmn_customize_preview_js() {
	wp_enqueue_script( 'lbmn_customizer_preview', get_template_directory_uri() . '/inc/customizer/customizer-preview.js', array( 'lbmn-custom-js' ), '20140520', true );

	// Send object 'customizerDataSent' with font presets sets
	$customizerData = array(
		'fontPresetsNames' => lbmn_return_font_presets_names(),
		'headerTopHeight'  => intval( str_replace( 'px', '', get_theme_mod( 'lbmn_headertop_height', LBMN_HEADERTOP_HEIGHT_DEFAULT ) ) ),
		'megaMenuHeight'   => intval( str_replace( 'px', '', get_theme_mod( 'lbmn_headertop_menu_height', LBMN_HEADERTOP_MENUHEIGHT_DEFAULT ) ) ),
	);
	wp_localize_script( 'lbmn_customizer_preview', 'customizerDataSent', $customizerData );
}
add_action( 'customize_preview_init', 'lbmn_customize_preview_js' );

/**
 * Add more JavaScript files to customizer admin
 */
function lbmn_add_customizer_js() {

	wp_enqueue_media(); // need this to make WP Media Library to work

	wp_enqueue_script( 'lbmn_customizer_tinycolor_js', get_template_directory_uri() . '/inc/customizer/jquery-colorpickersliders/jquery.colorpickersliders.js', array( 'jquery'), '20140303', true );
	wp_enqueue_script( 'lbmn_customizer_customcolorpicker_js', get_template_directory_uri() . '/inc/customizer/jquery-colorpickersliders/tinycolor.js', array( 'jquery', 'lbmn_customizer_tinycolor_js'), '20140303', true );
	wp_enqueue_style( 'lbmn_customizer_customcolorpicker_css', get_template_directory_uri() . '/inc/customizer/jquery-colorpickersliders/jquery.colorpickersliders.css' );

	wp_enqueue_script( 'lbmn_customizer_adminjs', get_template_directory_uri() . '/inc/customizer/customizer-admin.js', array( 'jquery' ), '20141107-2', true ); //  array( 'jquery', 'iris', 'wp-color-picker' )
	wp_enqueue_style( 'lbmn_customizer_adminstyle', get_template_directory_uri() . '/inc/customizer/customizer-admin.css' );

	// Prepare some data to be transmitted to JS
	$customizerData = array( 'fontPresetsWeights' => lbmn_return_font_presets_weights() );

	// MegaMainMenu isn't installed - show a message using JS
	if(!is_plugin_active('mega_main_menu/mega_main_menu.php')) {
		$customizerData['notInstalled_MegaMainMenu'] = 1;
	}

	// LiveComposer isn't installed - show a message using JS
	if(!is_plugin_active('mega_main_menu/mega_main_menu.php') ||
		!is_plugin_active('ds-live-composer/ds-live-composer.php') ||
		!is_plugin_active('meta-box/meta-box.php') ) {

		$customizerData['notInstalled_requiredPlugin'] = 1;
	}

	// No menu assigned to the location 'header-menu'
	if ( !has_nav_menu( 'header-menu' ) ) {
		$customizerData['notAssigned_HeaderMenu'] = 1;
	}

	if ( !has_nav_menu( 'topbar' ) ) {
		$customizerData['notAssigned_TopBar'] = 1;
	}

	// Send data to JS
	wp_localize_script( 'lbmn_customizer_adminjs', 'customizerDataSent', $customizerData );

	// Chosen Jquery selector with search by HARVEST
	// used fot Google Fonts selectors
	wp_enqueue_script( 'lbmn_chosen_selectorjs', get_template_directory_uri() . '/javascripts/chosen/chosen.jquery.min.js', array( 'jquery' ), '20131215', true );
	wp_enqueue_style( 'lbmn_chosen_selectorcss', get_template_directory_uri() . '/javascripts/chosen/chosen.css' );

	// Include Mega Main Menu scripts and css to make iconpciker work
	// DOESN'T WORK WITH UPDATED MEGA MAIN MENU PLUGIN ANYMORE
	/*
	if(is_plugin_active('mega_main_menu/mega_main_menu.php')) {
		global $mega_main_menu;
		// Get array of all the scripts to load defined in
		// plugins/mega_main_menu/extensions/array_src/array_src.php
		$ware_src_array = mega_main_menu__array_src();
		// $ware_src_array = $ware_src_array['backend'];


		foreach ( $ware_src_array[ 'backend' ][ 'css' ] as $key => $value ) {
			wp_register_style( $key, $mega_main_menu->constant[ 'MM_WARE_CSS_URL' ] . $value );
			wp_enqueue_style( $key );
		}
		foreach ( $ware_src_array[ 'backend' ][ 'js' ] as $key => $value ) {
			if ( $value != '' ) {
				wp_register_script( $key, $mega_main_menu->constant[ 'MM_WARE_JS_URL' ] . $value, 'jquery', false, true );
			}
			wp_enqueue_script( $key );
		}

	}
	*/

}
// Include the custom js and css needed for Theme Customizer work
add_action( 'customize_controls_enqueue_scripts', 'lbmn_add_customizer_js' );

/**
 * ----------------------------------------------------------------------
 * For the future
 */


/**
 * ----------------------------------------------------------------------
 * Header Design Presets
 * In this fucntion we make possible to apply multiply header-design changes
 * based on "Header Design Preset" selected
 *
 * Preset number gets transmited through URL get variable
 */


function lbmn_header_presets() {

	$header_preset = '';

	// get selected header design preset
	$header_preset = get_theme_mod( 'lbmn_header_design', LBMN_HEADER_DESIGN_DEFAULT);

	global $header_design_presets;
	// TODO: before applying new design save the current as 'User Header Design 1'...

	// Change customizer controls based on presset number
	$header_preset_settings = $header_design_presets->get_header_preset( $header_preset );

	foreach ( $header_preset_settings as $setting_key => $setting_value ) {
		// set_theme_mod(  $setting_key, $setting_value );
		// it works fine but overrides my current settings on every page refresh
	}

	/* set_theme_mod( 'lbmn_header_design', 'header-design-custom' ); */
}
add_action( 'customize_register', 'lbmn_header_presets' );

/*
function lbmn_customizer_live_preview() {

	if ( get_theme_mod( 'lbmn_header_design' ) != 'header-design-custom' ) {
?>
			<script type='text/javascript'>
				// Clear URL params if any
				var urlparts= top.location.href.split('?');
				top.location.href = urlparts[0] + '?header-preset=<?php echo get_theme_mod( 'lbmn_header_design' ) ?>';
			</script>
		<?php
	}
}
add_action( 'customize_preview_init', 'lbmn_customizer_live_preview' );
*/

/*
function lbmn_customizer_live_preview() {


		?>
			<script type='text/javascript'>
				// Clear URL params if any
				var urlparts= top.location.href.split('?');
				top.location.href = urlparts[0] + '?header-preset=<?php echo get_theme_mod( 'lbmn_header_design' ) ?>';
			</script>
		<?php

}
add_action( 'customize_preview_init', 'lbmn_customizer_live_preview' );
*/
