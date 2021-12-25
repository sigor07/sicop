<?php
class Breadcrumb {
    /*
     * Breadcrumb navigation class
     * Mick Sear
     * http://www.ecreate.co.uk
     *
     * The key to using this is to decide on a $level for each page.  (array, starting position 0)
     * This determines where in the trail a link will be placed.  So, I normally make the homepage level 0,
     * then every page that can be accessed from the top level nav becomes level 1, and every page
     * from that second level becomes level 2, and so on.  When users return to a higher level (e.g. level 1)
     * the surplus links are removed.  Only one page can occupy a $level in the crumb trail.
     * There might be several routes to a page.  In which case, the trail will reflect the route that the
     * user actually took to get to that page.
     */

    var $output;
    var $crumbs = array( );
    var $location;

    /*
     * Constructor
     */

    function Breadcrumb() {

        if ( !isset( $_SESSION ) ) session_start();

        if ( isset( $_SESSION['breadcrumb'] ) && $_SESSION['breadcrumb'] != NULL ) {
            $this->crumbs = $_SESSION['breadcrumb'];
        }
    }

    /*
     * Add a crumb to the trail:
     * @param $label - The string to display
     * @param $url - The url underlying the label
     * @param $level - The level of this link.
     *
     */

    function add( $label, $url, $level ) {

        $crumb = array( );
        $crumb['label'] = $label;
        $crumb['url'] = $url;

        if ( $crumb['label'] != NULL && $crumb['url'] != NULL && isset( $level ) ) {

            if ( $level > 0 ) {

                while ( count( $this->crumbs ) >= $level ) {

                    array_pop( $this->crumbs ); //prune until we reach the $level we've allocated to this page
                }
            } else {

                while ( count( $this->crumbs ) > $level ) {

                    array_pop( $this->crumbs ); //prune until we reach the $level we've allocated to this page
                }
            }

            if ( !isset( $this->crumbs[0] ) && $level > 0 ) { //If there's no session data yet, assume a homepage link
                $this->crumbs[0]['url'] = '/sicop/home.php';
                $this->crumbs[0]['label'] = 'Home';
            }

            $this->crumbs[$level] = $crumb;
        }

        $_SESSION['breadcrumb'] = $this->crumbs; //Persist the data
        $this->crumbs[$level]['url'] = NULL; //Ditch the underlying url for the current page.
    }

    /*
     * Output a semantic list of links.  See above for sample CSS.  Modify this to suit your design.
     */

    function output() {

        //echo "<div id='breadcrumb'><ul><li>Click trail: </li>";
        echo '<p class="breadcrumb_output"> >';
        foreach ( $this->crumbs as $crumb ) {

            if ( $crumb['url'] != NULL ) {

                //echo "<li> > <a href='".$crumb['url']."' title='".$crumb['label']."'>".$crumb['label']."</a></li> ";
                echo ' > <a href="' . $crumb['url'] . '" title="' . $crumb['label'] . '">' . $crumb['label'] . '</a>';
            } else {

                echo ' > ' . $crumb['label'];
            }
        }

        echo '</p>';

        //echo "</ul></div>";
    }

}
?>