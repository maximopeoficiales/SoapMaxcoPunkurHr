<?php
class WebservicesCredentials
{
    public $PRODUDCTION = TRUE;
    public $HOST_DB = "localhost";

    public $PRECOR_URL = $PRODUDCTION ? "https://tiendaqa.precor.pe/" : "http://tiendaqa.test/";
    public $MAXCO_URL = "https://maxco.punkuhr.com/";

    public $DB_PRECOR_USER = $PRODUDCTION ?  "clg_wp_3oxdh" : "root";
    public $DB_PRECOR_PASS = $PRODUDCTION ? "Iz3r_0!Pe4faK2d&" : "";
    public $DB_PRECOR_DBNAME = $PRODUDCTION ? "clg_wp_retpq" : "tiendaqa_precor";

    public $DB_MAXCO_USER = "i5142852_wp4";
    public $DB_MAXCO_PASS = "F.L7tJxfhTbrfbpP7Oe41";
    public $DB_MAXCO_DBNAME = "i5142852_wp4";

    // woocommerce 

    public $WOO_PRECOR_CK = "ck_82458af7253f4bbd4bd0941f5487323f31b23cdf";
    public $WOO_PRECOR_CS = "cs_b0aa70d51f7757485699c97342568b85e9922af2";

    public $WOO_MAXCO_CK = "ck_0157c4f5fbc72b4a71161b929dea276a81006fd9";
    public $WOO_MAXCO_CS = "cs_b575ce513cbaf2478ca0d06c2d0dd64699ec642d";
}
