<?php
/*
Plugin Name: Bitcoin ILS
Plugin URI: http://taxreport.org.il
Description: Display the Israeli exchange rate for Bitcoin.
Author: OK Digital LTD.
Version: 0.1

*/

/**
 * Register style sheet.
 */
add_action( 'wp_enqueue_scripts', 'BILS_register_style' );
function BILS_register_style() {
    wp_register_style( 'BILS_table_style', plugin_dir_url( __FILE__ ) . 'style.css' );
    wp_enqueue_style( 'BILS_table_style' );
}


/**
 * echos shortcode
 */
function BILS_Exchange_Shortcode() {

    $curl = wp_remote_get( 'https://api.coindesk.com/v1/bpi/currentprice/ils.json',
        array(
        'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0'
        )
    );

    $table = BILS_ParseExchangeRate( wp_remote_retrieve_body($curl) );
    $info = '<div id="BILSexchangeRate">'.$table.'</div>';
    return $info;
}
add_shortcode( 'BILS', 'BILS_Exchange_Shortcode' );

/**
 * @param $data
 * @return string
 */
function BILS_ParseExchangeRate( $data ){
    $arr = json_decode($data);

    $last_update = $arr->time->updated;
    $ILS = $arr->bpi->ILS->rate;
    $USD = $arr->bpi->USD->rate;

    $html = "<div class='BILS_inner'>";
    $html .= "<span>עודכן לאחרונה ב-".date("d/m/Y", strtotime($last_update))."</span>";
    $html .= "<table class='table table-hover'>
                    <thead class='thead-dark'>
                        <tr>
                            <th>מטבע</th>
                            <th>שער</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>ש\"ח</td>
                        <td>$ILS</td>
                   </tr>
                   <tr>
                        <td>USD</td>
                        <td>$USD</td>
                   </tr>";
    $html .= "</tbody></table></div>";
    return $html;
}

