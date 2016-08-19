<?php
ini_set('display_errors', 1);

error_reporting(E_ERROR | E_PARSE);

ini_set('PHP_FCGI_MAX_REQUESTS', 999999999999);
ini_set('suhosin.post.max_array_depth',100000);
ini_set('suhosin.post.max_array_index_length',100000);
ini_set('suhosin.post.max_name_length',100000);
ini_set('suhosin.post.max_totalname_length',100000);
ini_set('suhosin.post.max_value_length',100000);

ini_set('suhosin.request.max_array_depth',100000);
ini_set('suhosin.request.max_array_index_length',100000);
ini_set('suhosin.request.max_name_length',100000);
ini_set('suhosin.request.max_totalname_length',100000);
ini_set('suhosin.request.max_value_length',100000);

ini_set('suhosin.get.max_array_depth',100000);
ini_set('suhosin.get.max_array_index_length',100000);
ini_set('suhosin.get.max_name_length',100000);
ini_set('suhosin.get.max_totalname_length',100000);
ini_set('suhosin.get.max_value_length',100000);

ini_set('suhosin.cookie.max_array_depth',100000);
ini_set('suhosin.cookie.max_array_index_length',100000);
ini_set('suhosin.cookie.max_name_length',100000);
ini_set('suhosin.cookie.max_totalname_length',100000);
ini_set('suhosin.cookie.max_value_length',100000);

ini_set('post.max_array_depth',100000);
ini_set('post.max_array_index_length',100000);
ini_set('post.max_name_length',100000);
ini_set('post.max_totalname_length',100000);
ini_set('post.max_value_length',100000);

ini_set('request.max_array_depth',100000);
ini_set('request.max_array_index_length',100000);
ini_set('request.max_name_length',100000);
ini_set('request.max_totalname_length',100000);
ini_set('request.max_value_length',100000);

ini_set('get.max_array_depth',100000);
ini_set('get.max_array_index_length',100000);
ini_set('get.max_name_length',100000);
ini_set('get.max_totalname_length',100000);
ini_set('get.max_value_length',100000);

ini_set('cookie.max_array_depth',100000);
ini_set('cookie.max_array_index_length',100000);
ini_set('cookie.max_name_length',100000);
ini_set('cookie.max_totalname_length',100000);
ini_set('cookie.max_value_length',100000);

ini_set('max_execution_time',0);
ini_set('memory_limit', '2048M');
ini_set('upload_max_filesize', '200M');
ini_set('max_input_time',"-1");

define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

define("SITE_URL","http://clients.edit-place.com");

define("LACITY_IMAGE_PATH", ROOT_PATH."/CLIENTS/LACITY");
define("LACITY_PATH", ROOT_PATH."/excel-devs/lacity");
define("LACITY_SOURCE_PATH", LACITY_PATH."/source-files");
define("LACITY_WRITER_FILE_PATH", LACITY_PATH."/writer-files");
define("LACITY_CONFIG_FILE", LACITY_PATH."/config.txt");

define("K2_LACITY_PATH", ROOT_PATH."/excel-devs/korben2/lacity");
define("K2_LACITY_SOURCE_PATH", K2_LACITY_PATH."/source-files");
define("K2_LACITY_WRITER_FILE_PATH", K2_LACITY_PATH."/writer-files");
define("K2_LACITY_CONFIG_FILE", K2_LACITY_PATH."/config.txt");

define("KORBEN_IMAGE_PATH", ROOT_PATH."/CLIENTS/KORBEN");

define("CACHECACHE_PATH", ROOT_PATH."/excel-devs/korben/cachecache");
define("CACHECACHE_SOURCE_PATH", CACHECACHE_PATH."/source-files");
define("CACHECACHE_WRITER_FILE_PATH", CACHECACHE_PATH."/writer-files");
define("CACHECACHE_CONFIG_FILE", CACHECACHE_PATH."/config.txt");

define("K2_CACHECACHE_PATH", ROOT_PATH."/excel-devs/korben2/cachecache");
define("K2_CACHECACHE_SOURCE_PATH", K2_CACHECACHE_PATH."/source-files");
define("K2_CACHECACHE_WRITER_FILE_PATH", K2_CACHECACHE_PATH."/writer-files");
define("K2_CACHECACHE_CONFIG_FILE", K2_CACHECACHE_PATH."/config.txt");

define("BREAL_PATH", ROOT_PATH."/excel-devs/korben/breal");
define("BREAL_SOURCE_PATH", BREAL_PATH."/source-files");
define("BREAL_WRITER_FILE_PATH", BREAL_PATH."/writer-files");
define("BREAL_CONFIG_FILE", BREAL_PATH."/config.txt");

define("BONOBO_PATH", ROOT_PATH."/excel-devs/korben/bonobo");
define("BONOBO_PATH1", ROOT_PATH."/excel-devs/bonobo");
define("BONOBO_SOURCE_PATH", BONOBO_PATH."/source-files");
define("BONOBO_WRITER_FILE_PATH", BONOBO_PATH."/writer-files");
define("BONOBO_FILE_PATH", BONOBO_PATH1."/files");
define("BONOBO_CONFIG_FILE", BONOBO_PATH."/config.txt");

define("K2_BONOBO_PATH", ROOT_PATH."/excel-devs/korben2/bonobo");
define("K2_BONOBO_SOURCE_PATH", K2_BONOBO_PATH."/source-files");
define("K2_BONOBO_WRITER_FILE_PATH", K2_BONOBO_PATH."/writer-files");
define("K2_BONOBO_CONFIG_FILE", K2_BONOBO_PATH."/config.txt");


define("BONOBO_UK_PATH", ROOT_PATH."/excel-devs/korben/bonobo-uk");
define("BONOBO_UK_SOURCE_PATH", BONOBO_UK_PATH."/source-files");
define("BONOBO_UK_WRITER_FILE_PATH", BONOBO_UK_PATH."/writer-files");
define("BONOBO_UK_CONFIG_FILE", BONOBO_UK_PATH."/config.txt");

define("K2_BONOBO_UK_PATH", ROOT_PATH."/excel-devs/korben2/bonobo-uk");
define("K2_BONOBO_UK_SOURCE_PATH", K2_BONOBO_UK_PATH."/source-files");
define("K2_BONOBO_UK_WRITER_FILE_PATH", K2_BONOBO_UK_PATH."/writer-files");
define("K2_BONOBO_UK_CONFIG_FILE", K2_BONOBO_UK_PATH."/config.txt");

define("SCOTTAGE_PATH", ROOT_PATH."/excel-devs/korben/scottage");
define("SCOTTAGE_SOURCE_PATH", SCOTTAGE_PATH."/source-files");
define("SCOTTAGE_WRITER_FILE_PATH", SCOTTAGE_PATH."/writer-files");
define("SCOTTAGE_CONFIG_FILE", SCOTTAGE_PATH."/config.txt");

define("SCOTTAGE2_PATH", ROOT_PATH."/excel-devs/korben/scottage2");
define("SCOTTAGE2_SOURCE_PATH", SCOTTAGE2_PATH."/source-files");
define("SCOTTAGE2_WRITER_FILE_PATH", SCOTTAGE2_PATH."/writer-files");
define("SCOTTAGE2_CONFIG_FILE", SCOTTAGE2_PATH."/config.txt");

define("K2_SCOTTAGE2_PATH", ROOT_PATH."/excel-devs/korben2/scottage2");
define("K2_SCOTTAGE2_SOURCE_PATH", K2_SCOTTAGE2_PATH."/source-files");
define("K2_SCOTTAGE2_WRITER_FILE_PATH", K2_SCOTTAGE2_PATH."/writer-files");
define("K2_SCOTTAGE2_CONFIG_FILE", K2_SCOTTAGE2_PATH."/config.txt");

define("MORGAN_PATH", ROOT_PATH."/excel-devs/korben/morgan");
define("MORGAN_SOURCE_PATH", MORGAN_PATH."/source-files");
define("MORGAN_WRITER_FILE_PATH", MORGAN_PATH."/writer-files");
define("MORGAN_CONFIG_FILE", MORGAN_PATH."/config.txt");

define("K2_MORGAN_PATH", ROOT_PATH."/excel-devs/korben2/morgan");
define("K2_MORGAN_SOURCE_PATH", K2_MORGAN_PATH."/source-files");
define("K2_MORGAN_WRITER_FILE_PATH", K2_MORGAN_PATH."/writer-files");
define("K2_MORGAN_CONFIG_FILE", K2_MORGAN_PATH."/config.txt");

define("MORGAN_UK_PATH", ROOT_PATH."/excel-devs/korben/morgan-uk");
define("MORGAN_UK_SOURCE_PATH", MORGAN_UK_PATH."/source-files");
define("MORGAN_UK_WRITER_FILE_PATH", MORGAN_UK_PATH."/writer-files");
define("MORGAN_UK_CONFIG_FILE", MORGAN_UK_PATH."/config.txt");

define("K2_MORGAN_UK_PATH", ROOT_PATH."/excel-devs/korben2/morgan-uk");
define("K2_MORGAN_UK_SOURCE_PATH", K2_MORGAN_UK_PATH."/source-files");
define("K2_MORGAN_UK_WRITER_FILE_PATH", K2_MORGAN_UK_PATH."/writer-files");
define("K2_MORGAN_UK_CONFIG_FILE", K2_MORGAN_UK_PATH."/config.txt");

define("K2_MORGAN_HOF_PATH", ROOT_PATH."/excel-devs/korben2/morgan-hof");
define("K2_MORGAN_HOF_SOURCE_PATH", K2_MORGAN_HOF_PATH."/source-files");
define("K2_MORGAN_HOF_WRITER_FILE_PATH", K2_MORGAN_HOF_PATH."/writer-files");
define("K2_MORGAN_HOF_CONFIG_FILE", K2_MORGAN_HOF_PATH."/config.txt");

define("ARMAND_THIERY_WRITER_FILE_PATH1", ROOT_PATH."/excel-devs/armand_thiery/writer-files");
define("ARMAND_THIERY_PATH", ROOT_PATH."/excel-devs/korben/armand_thiery");
define("ARMAND_THIERY_SOURCE_PATH", ARMAND_THIERY_PATH."/source-files");
define("ARMAND_THIERY_WRITER_FILE_PATH", ARMAND_THIERY_PATH."/writer-files");
define("ARMAND_THIERY_CONFIG_FILE", ARMAND_THIERY_PATH."/config.txt");

define("TOSCANE_PATH", ROOT_PATH."/excel-devs/korben/toscane");
define("TOSCANE_SOURCE_PATH", TOSCANE_PATH."/source-files");
define("TOSCANE_WRITER_FILE_PATH", TOSCANE_PATH."/writer-files");
define("TOSCANE_CONFIG_FILE", TOSCANE_PATH."/config.txt");

define("ANDRE_URL", SITE_URL."/excel-devs/andre");
define("ANDRE_CLIENT_URL", SITE_URL.'/andre');
define("ANDRE_CLIENT_REF_URL", ANDRE_URL.'/view-pictures.php?client=ANDRE&reference=');
define("ANDRE_IMAGE_PATH", ROOT_PATH."/CLIENTS/ANDRE");
define("ANDRE_PATH", ROOT_PATH."/excel-devs/andre");
define("ANDRE_WRITER_FILE_PATH", ANDRE_PATH."/writer-files/references");
define("ANDRE_REF_WRITER_FILE_PATH", ANDRE_PATH."/ref-files");
define("ANDRE_TMP_WRITER_FILE_PATH", ANDRE_PATH."/tmp_ref");


//LBM paths
define("LBM_URL", SITE_URL."/excel-devs/lebonmarche");
define("LBM_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEBONMARCHE");
define("LBM_PATH", ROOT_PATH."/excel-devs/lebonmarche");
define("LBM_EP_SOURCE_PATH", LBM_PATH."/ep-source-files");
define("LBM_EP_CONFIG_FILE", LBM_PATH."/ep-config.txt");
define("LBM_REF_SOURCE_PATH", LBM_PATH."/lbm-source-files");
define("LBM_REF_CONFIG_FILE", LBM_PATH."/lbm-config.txt");
define("LBM_REF_XLS_FILE", LBM_PATH."/lbm_reference_global.xls");
define("LBM_WRITER_FILE_PATH", LBM_PATH."/writer-files");
define("LBM_REF_WRITER_FILE_PATH", LBM_PATH."/lebon/writer-files");
define("LBM_REF_SEARCH_PATH", LBM_PATH."/reference-search");
define("LBM_WRITER_FILE_CONFIG", LBM_PATH."/writer-files-config.txt");
define("LBM_XLS3_WRITER_FILE_CONFIG", LBM_PATH."/xls3-writer-files-config.txt");
define("LBM_ARRAY_SRCXLS", LBM_PATH."/array-srcxls.txt");
define("LBM_ARRAY_MRGXLS", LBM_PATH."/array-mrgxls.txt");
define("LBM_CSV_XLSX_SOURCE", LBM_PATH."/source");
define("LBM_CHECK_REF_NEW", LBM_PATH."/new_ref_check");
//LBM TEST PATHS 
define("LBMTEST_URL", SITE_URL."/excel-devs/lbmtest");
define("LBMTEST_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEBONMARCHE");
define("LBMTEST_PATH", ROOT_PATH."/excel-devs/lbmtest");
define("LBMTEST_EP_SOURCE_PATH", LBMTEST_PATH."/pdn");
define("LBMTEST_SOURCE_PATH", LBMTEST_PATH."/ref");
define("LBMTEST_WRITER_PATH", LBMTEST_PATH."/writer");
define("LBMTEST_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEBONMARCHE");

//Caroll
define("CAROLL_URL", SITE_URL."/excel-devs/caroll");
define("CAROLL_IMAGE_PATH", ROOT_PATH."/CLIENTS/CAROLL");
define("CAROLL_PATH", ROOT_PATH."/excel-devs/caroll");
define("CAROLL_PATH2", ROOT_PATH."/excel-devs/caroll2");

define("CAROLL_EP_SOURCE_PATH", CAROLL_PATH."/ep-source-files");
define("CAROLL_EP_CONFIG_FILE", CAROLL_PATH."/ep-config.txt");
define("CAROLL_EP_CONFIG_FILE2", CAROLL_PATH2."/ep-config.txt");

define("CAROLL_REF_SOURCE_PATH", CAROLL_PATH."/caroll-source-files");
define("CAROLL_REF_CONFIG_FILE", CAROLL_PATH."/caroll-config.txt");
define("CAROLL_REF_CONFIG_FILE2", CAROLL_PATH2."/caroll-config.txt");

define("CAROLL_WRITER_FILE_PATH", CAROLL_PATH."/writer-files");
define("CAROLL_WRITER_FILE_PATH2", CAROLL_PATH."/writer-files2");

define("CAROLL_EXCEL_TIME_CONFIG", CAROLL_PATH."/caroll_excel_time.txt");
define("CAROLL_EXCEL_TIME_CONFIG2", CAROLL_PATH2."/caroll_excel_time.txt");

define("CAROLL_WRITER_FILE_CONFIG_PATH", CAROLL_PATH."/writer-files-config");
define("CAROLL_WRITER_FILE_CONFIG_PATH2", CAROLL_PATH2."/writer-files-config");

define("CAROLL_XML_PATH", CAROLL_PATH."/XML");
define("CAROLL_REF_SEARCH_PATH", CAROLL_PATH."/reference-search");



//Translation
define("TRANSLATION_URL", SITE_URL."/excel-devs/translation");
define("TRANSLATION_PATH", ROOT_PATH."/excel-devs/translation");
define("TRANSLATION_WRITER_FILE_PATH", TRANSLATION_PATH."/writer-files");
define("TRANSLATION_WRITER_FILE_TMP_PATH", TRANSLATION_PATH."/writer-files/tmp");

//CDC
define("CDC_URL", SITE_URL."/excel-devs/cdc");
define("CDC_PATH", ROOT_PATH."/excel-devs/cdc");
define("CDC_WRITER_FILE_PATH", CDC_PATH."/writer-files");

//RDC
define("RDC_URL", SITE_URL."/excel-devs/rdc");
define("RDC_PATH", ROOT_PATH."/excel-devs/rdc");
define("RDC_XML_FILE_PATH", RDC_PATH."/xml-files");
define("RDC_XML_UPLOAD_FILE_PATH", RDC_PATH."/xml-uploads");

//EBOOKERS
define("EBOOKERS_URL", SITE_URL."/excel-devs/ebookers");
define("EBOOKERS_PATH", ROOT_PATH."/excel-devs/ebookers");
define("EBOOKERS_WRITER_FILE_PATH", EBOOKERS_PATH."/writer-files");
define("EBOOKERS_WRITER_FILE_PATH_DEV1", EBOOKERS_PATH."/writer-files/dev1");
define("EBOOKERS_WRITER_FILE_PATH_DEV3", EBOOKERS_PATH."/writer-files/dev3");
define("EBOOKERS_WRITER_FILE_PATH1", EBOOKERS_PATH."/writer-files1");

//NAKAMURA
define("NAKAMURA_URL", SITE_URL."/excel-devs/nakamura");
define("NAKAMURA_IMAGE_PATH", ROOT_PATH."/CLIENTS/NAKAMURA");
define("NAKAMURA_PATH", ROOT_PATH."/excel-devs/nakamura");
define("NAKAMURA_CLIENT_REF_URL", NAKAMURA_URL.'/view-pictures.php?client=NAKAMURA&reference=');
define("NAKAMURA_CLIENT_URL", SITE_URL.'/nakamura');
define("NAKAMURA_WRITER_FILE_PATH", NAKAMURA_PATH."/writer-files");
define("NAKAMURA_MERGE_WRITER_FILE_PATH", NAKAMURA_PATH."/merge-writer-files");

//NAFNAF
define("NAFNAF_URL", SITE_URL."/excel-devs/nafnaf");
define("NAFNAF_IMAGE_PATH", ROOT_PATH."/CLIENTS/NAFNAF");
define("NAFNAF_PATH", ROOT_PATH."/excel-devs/nafnaf");
define("NAFNAF_CLIENT_REF_URL", NAFNAF_URL.'/view-pictures.php?client=NAFNAF&reference=');
define("NAFNAF_CLIENT_URL", SITE_URL.'/nafnaf');
define("NAFNAF_WRITER_FILE_PATH", NAFNAF_PATH."/writer-files");
define("NAFNAF_WRITER_SUB_FILE_PATH", NAFNAF_PATH."/writer-sub-files");

//BOULANGER
define("BOULANGER_URL", SITE_URL."/excel-devs/boulanger");
define("BOULANGER_PATH", ROOT_PATH."/excel-devs/boulanger");
define("BOULANGER_WRITER_FILE_PATH", BOULANGER_PATH."/writer-files");
define("BOULANGER_WRITER_TXT_FILE_PATH", BOULANGER_PATH."/txt");
define("BOULANGER_IMG_WRITER_FILE_PATH", BOULANGER_PATH."/writer-files_img");
define("BOULANGER_IMG_WRITER_TXT_FILE_PATH", BOULANGER_PATH."/txt_img");

//FTV
define("FTV_PATH", ROOT_PATH."/excel-devs/ftv");
define("FTV_WRITER_FILE_PATH", FTV_PATH."/writer-files");

//BURTON
define("BURTON_URL", SITE_URL."/excel-devs/burton");
define("BURTON_URL_PATH", BURTON_URL."/writer-files");
define("BURTON_PATH", ROOT_PATH."/excel-devs/burton");
define("BURTON_WRITER_FILE_PATH", BURTON_PATH."/writer-files");
define("BURTON_CLIENTS_PATH", BURTON_PATH."/clients");

//SANMARINA
define("SANMARINA_URL", SITE_URL."/excel-devs/sanmarina");
//define("SANMARINA_IMAGE_PATH", ROOT_PATH."/CLIENTS/SANMARINA/visuels PE14");
define("SANMARINA_IMAGE_PATH", ROOT_PATH."/CLIENTS/SANMARINA");
define("SANMARINA_PATH", ROOT_PATH."/excel-devs/sanmarina");
define("SANMARINA_CLIENT_REF_URL", SANMARINA_URL.'/view-pictures.php?client=SANMARINA&reference=');
define("SANMARINA_CLIENT_URL", SITE_URL.'/sanmarina');
define("SANMARINA_WRITER_FILE_PATH", SANMARINA_PATH."/writer-files");
define("SANMARINA_WRITER_FILE_PATH1", SANMARINA_PATH."/writer-files-with-description");

//NEWMAN
define("NEWMAN_URL", SITE_URL."/excel-devs/newman");
define("NEWMAN_PATH", ROOT_PATH."/excel-devs/newman");
define("NEWMAN_IMAGE_PATH", ROOT_PATH."/CLIENTS/NEWMAN");
define("NEWMAN_CLIENT_REF_URL", NEWMAN_URL.'/view-pictures.php?client=NEWMAN&reference=');
define("NEWMAN_CLIENT_URL", SITE_URL.'/newman');
define("NEWMAN_WRITER_FILE_PATH", NEWMAN_PATH."/writer-files");

//IKKS
define("IKKS_URL", SITE_URL."/excel-devs/ikks");
define("IKKS_PATH", ROOT_PATH."/excel-devs/ikks");
define("IKKS_IMAGE_PATH", ROOT_PATH."/CLIENTS/IKKS");
define("IKKS_CLIENT_REF_URL", IKKS_URL.'/view-pictures.php?client=IKKS&reference=');
define("IKKS_CLIENT_URL", SITE_URL.'/ikks');
define("IKKS_WRITER_FILE_PATH", IKKS_PATH."/writer-files");
define("IKKS_WRITER_FILE_PATH2", IKKS_PATH."/writer-files2");
define("IKKS_REF_CONFIG", IKKS_PATH."/doublondata.txt");

//MONOPRIX
define("MONOPRIX_PATH", ROOT_PATH."/excel-devs/monoprix");
define("MONOPRIX_URL", SITE_URL."/excel-devs/monoprix");
define("MONOPRIX_IMAGE_PATH", ROOT_PATH."/CLIENTS/MONOPRIX");
define("MONOPRIX_CLIENT_URL", MONOPRIX_URL.'/view-pictures.php?client=MONOPRIX&reference=');
define("MONOPRIX_WRITER_FILE_PATH", MONOPRIX_PATH."/writer-files");

//CLARKS
define("CLARKS_PATH", ROOT_PATH."/excel-devs/clarks");
define("CLARKS_URL", SITE_URL."/excel-devs/clarks");
define("CLARKS_URI", 'http://www.clarks.');
define("CLARKS_IMAGE_PATH", ROOT_PATH."/CLIENTS/clarks-");
define("CLARKS_CLIENT_URL", SITE_URL.'/clarks');
define("CLARKS_CLIENT_REF_URL", CLARKS_URL.'/view-pictures.php?client=CLARKS&reference=');
define("CLARKS_WRITER_FILE_PATH", CLARKS_PATH."/writer-files/");
define("CLARKS_REF_WRITER_FILE_PATH", CLARKS_PATH."/ref-files");
define("CLARKS_URL_WRITER_FILE_PATH", CLARKS_PATH."/url-files");
define("CLARKS_URL2_WRITER_FILE_PATH", CLARKS_PATH."/url2-files");

//COSMOPARIS
define("COSMOPARIS_URL", SITE_URL."/excel-devs/cosmoparis");
define("COSMOPARIS_PATH", ROOT_PATH."/excel-devs/cosmoparis");
define("COSMOPARIS_IMAGE_PATH", ROOT_PATH."/CLIENTS/COSMOPARIS");
define("COSMOPARIS_CLIENT_REF_URL", COSMOPARIS_URL.'/view-pictures.php?client=COSMOPARIS&reference=');
define("COSMOPARIS_CLIENT_URL", SITE_URL.'/cosmoparis');
define("COSMOPARIS_WRITER_FILE_PATH", COSMOPARIS_PATH."/writer-files");
define("COSMOPARIS_CONFIG_FILE", COSMOPARIS_WRITER_FILE_PATH."/products.txt");

//KUJJUK
define("KUJJUK_URL", SITE_URL."/excel-devs/kujjuk");
define("KUJJUK_PATH", ROOT_PATH."/excel-devs/kujjuk");
define("KUJJUK_IMAGE_PATH", ROOT_PATH."/CLIENTS/KUJJUK");
define("KUJJUK_CLIENT_REF_URL", KUJJUK_URL.'/view-pictures.php?client=KUJJUK&reference=');
define("KUJJUK_CLIENT_URL", SITE_URL.'/kujjuk');
define("KUJJUK_WRITER_FILE_PATH", KUJJUK_PATH."/writer-files");
define("KUJJUK_CONFIG_FILE", KUJJUK_WRITER_FILE_PATH."/products.txt");

//KOOKAI
define("KOOKAI_URL", SITE_URL."/excel-devs/kookai");
define("KOOKAI_PATH", ROOT_PATH."/excel-devs/kookai");
define("KOOKAI_IMAGE_PATH", ROOT_PATH."/CLIENTS/KOOKAI");
define("KOOKAI_CLIENT_REF_URL", KOOKAI_URL.'/view-pictures.php?client=KOOKAI&reference=');
define("KOOKAI_CLIENT_URL", SITE_URL.'/kookai');
define("KOOKAI_WRITER_FILE_PATH", KOOKAI_PATH."/writer-files");
define("KOOKAI_MWRITER_FILE_PATH", KOOKAI_PATH."/mwriter-files");

//LAHALLE
define("LAHALLE_URL", SITE_URL."/excel-devs/la-halle");
define("LAHALLE_PATH", ROOT_PATH."/excel-devs/la-halle");
define("LAHALLE_IMAGE_PATH", ROOT_PATH."/CLIENTS/LAHALLE");
define("LAHALLE_CLIENT_SH_URL", LAHALLE_URL.'/shoe-pictures.php?client=LAHALLE&reference=');
define("LAHALLE_CLIENT_CL_URL", LAHALLE_URL.'/clothe-pictures.php?client=LAHALLE&reference=');
define("LAHALLE_CLIENT_URL", SITE_URL.'/la-halle');
define("LAHALLE_SH_WRITER_FILE_PATH", LAHALLE_PATH."/writer-files/shoe");
define("LAHALLE_CL_WRITER_FILE_PATH", LAHALLE_PATH."/writer-files/clothe");

//LAHALLE2
define("LAHALLE2_URL", SITE_URL."/excel-devs/la-halle2");
define("LAHALLE2_PATH", ROOT_PATH."/excel-devs/la-halle2");
define("LAHALLE2_IMAGE_PATH", ROOT_PATH."/CLIENTS/LAHALLE");
define("LAHALLE2_CLIENT_SH_URL", LAHALLE2_URL.'/shoe-pictures.php?client=LAHALLE2&reference=');
define("LAHALLE2_CLIENT_CL_URL", LAHALLE2_URL.'/clothe-pictures.php?client=LAHALLE2&reference=');
define("LAHALLE2_CLIENT_URL", SITE_URL.'/la-halle');
define("LAHALLE2_SH_INPUT_FILE_PATH", LAHALLE2_PATH."/writer-files/shoe");
define("LAHALLE2_CL_INPUT_FILE_PATH", LAHALLE2_PATH."/writer-files/clothe");
define("LAHALLE2_SH_WRITER_FILE_PATH", LAHALLE2_PATH."/writer-files/shoe");
define("LAHALLE2_CL_WRITER_FILE_PATH", LAHALLE2_PATH."/writer-files/clothe");

define("LAHALLE2_CL_DELIVERY_FILE_PATH", LAHALLE2_PATH."/dev1/delivery-files/");
define("LAHALLE2_EXTRACT_FILE_PATH", LAHALLE2_PATH."/writer-files/extract");

define("LAHALLE2_SH_DELIVERY_FILE_PATH", ROOT_PATH."/admin/delivery-files/150128184617815");
define("LAHALLE2_SH_DELIVERY_CHECK_FILE_PATH", LAHALLE2_PATH."/writer-files/delivery-check");

//PERNODRICARD
define("PERNODRICARD_URL", SITE_URL."/excel-devs/pernod_ricard");
define("PERNODRICARD_PATH", ROOT_PATH."/excel-devs/pernod_ricard");
define("PERNODRICARD_WRITER_FILE_PATH", PERNODRICARD_PATH."/writer-files");

//LEROYMERLIN
define("LEROYMERLIN_URL", SITE_URL."/excel-devs/leroymerlin");
define("LEROYMERLIN_PATH", ROOT_PATH."/excel-devs/leroymerlin");
define("LEROYMERLIN_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEROYMERLIN");
define("LEROYMERLIN_CLIENT_REF_URL", LEROYMERLIN_URL.'/view-pictures.php?client=LEROYMERLIN&reference=');
define("LEROYMERLIN_CLIENT_URL", SITE_URL.'/leroymerlin');
define("LEROYMERLIN_WRITER_FILE_PATH", LEROYMERLIN_PATH."/writer-files");
define("LEROYMERLIN_CONFIG_FILE", LEROYMERLIN_WRITER_FILE_PATH."/products.txt");

//ACCESSORIE_DIFFUSION
define("ACCESSORIE_DIFFUSION_URL", SITE_URL."/excel-devs/accessoire-diffusion");
define("ACCESSORIE_DIFFUSION_PATH", ROOT_PATH."/excel-devs/accessoire-diffusion");
define("ACCESSORIE_DIFFUSION_IMAGE_PATH", ROOT_PATH."/CLIENTS/ACCESSORIE_DIFFUSION");
define("ACCESSORIE_DIFFUSION_CLIENT_REF_URL", ACCESSORIE_DIFFUSION_URL.'/view-pictures.php?client=ACCESSORIE_DIFFUSION&reference=');
define("ACCESSORIE_DIFFUSION_CLIENT_URL", SITE_URL.'/accessoire-diffusion');
define("ACCESSORIE_DIFFUSION_WRITER_FILE_PATH", ACCESSORIE_DIFFUSION_PATH."/writer-files");

//COPY PASTE
define("COPY_PASTE_PATH", ROOT_PATH."/excel-devs/copypaste");
define("COPY_PASTE_WRITER_FILE_PATH", COPY_PASTE_PATH."/writer-files");

//SEM
define("SEM_PATH", ROOT_PATH."/excel-devs/sem");
define("SEM_WRITER_FILE_PATH", SEM_PATH."/writer-files");

//NETBOOSTER
define("NETBOOSTER_PATH", ROOT_PATH."/excel-devs/netbooster");
define("NETBOOSTER_WRITER_FILE_PATH", NETBOOSTER_PATH."/writer-files");

//VIAPRESSE
define("VIAPRESSE_PATH", ROOT_PATH."/excel-devs/viapresse");
define("VIAPRESSE_WRITER_FILE_PATH", VIAPRESSE_PATH."/writer-files");

//PAGES JAUNES
define("PAGESJAUNES_PATH", ROOT_PATH."/excel-devs/pagesjaunes");
define("PAGESJAUNES_WRITER_FILE_PATH", PAGESJAUNES_PATH."/writer-files");

//INLINE COMPARE
define("INLINE_COMPARE_PATH", ROOT_PATH."/excel-devs/inline-compare");
define("INLINE_COMPARE_WRITER_FILE_PATH", INLINE_COMPARE_PATH."/writer-files");

//MORGAN1
define("MORGANCSV_URL", SITE_URL."/excel-devs/morgan");
define("MORGANCSV_PATH", ROOT_PATH."/excel-devs/morgan");
define("MORGANCSV_WRITER_FILE_PATH", MORGANCSV_PATH."/writer-files");
define("MORGANCSV_UPLOAD_FILE_PATH", MORGANCSV_PATH."/uploads");
define("MORGANCSV_UPLOAD_URL", MORGANCSV_URL."/uploads");

//ODIGEO
define("ODIGEO_PATH", ROOT_PATH."/excel-devs/odigeo");
define("ODIGEO_WRITER_FILE_PATH", ODIGEO_PATH."/writer-files");
define("ODIGEO_TEMPLATE_FILE_PATH", ODIGEO_WRITER_FILE_PATH."/template1");

//MISTER AUTO
define("MISTER_AUTO_PATH", ROOT_PATH."/excel-devs/mister-auto");
define("MISTER_AUTO_WRITER_FILE_PATH", MISTER_AUTO_PATH."/writer-files");
define("MISTER_AUTO_STRUCTURE_FILE_PATH", MISTER_AUTO_WRITER_FILE_PATH."/structure_dev");

//EXPEDIA
define("EXPEDIA_PATH", ROOT_PATH."/excel-devs/expedia");
define("EXPEDIA_WRITER_FILE_PATH", EXPEDIA_PATH."/writer-files");

//HOTELS.COM
define("HOTELS_PATH", ROOT_PATH."/excel-devs/hotels");
define("HOTELS_WRITER_FILE_PATH", HOTELS_PATH."/writer-files");

//HOTELS2.COM
define("HOTELS_PATH2", ROOT_PATH."/excel-devs/hotels2");
define("HOTELS_WRITER_FILE_PATH2", HOTELS_PATH2."/writer-files");

//VENERE
define("VENERE_PATH", ROOT_PATH."/excel-devs/venere");
define("VENERE_WRITER_FILE_PATH", VENERE_PATH."/writer-files");

//VENERE NEW
define("VENERE_NEW_PATH", ROOT_PATH."/excel-devs/venere-new");
define("VENERE_NEW_WRITER_FILE_PATH", VENERE_NEW_PATH."/writer-files");

//VENERE NEW
define("VENERE_THEME_PATH", ROOT_PATH."/excel-devs/venere_theme");
define("VENERE_THEME_WRITER_FILE_PATH", VENERE_THEME_PATH."/writer-files");

//BEST WESTERN 
define("BESTWESTERN_PATH", ROOT_PATH."/excel-devs/bestwestern");
define("BESTWESTERN_WRITER_FILE_PATH", BESTWESTERN_PATH."/writer-files");

//COTWOLDS
define("COTWOLDS_PATH", ROOT_PATH."/excel-devs/cotswold");
define("COTWOLDS_WRITER_FILE_PATH", COTWOLDS_PATH."/writer-files");

//CLARINS
define("CLARINS_PATH", ROOT_PATH."/excel-devs/clarins");
define("CLARINS_WRITER_FILE_PATH", CLARINS_PATH."/writer-files");
define("CLARINS_TAG_WRITER_FILE_PATH", CLARINS_PATH."/tag_writer-files");
define("CLARINS_KW_UPDATE_FILE_PATH", CLARINS_PATH."/kw_update-files");

//COMPTOIR
define("COMPTOIR_PATH", ROOT_PATH."/excel-devs/comptoir");
define("COMPTOIR_WRITER_FILE_PATH", COMPTOIR_PATH."/writer-files");

//AGATHA
define("AGATHA_URL", SITE_URL."/excel-devs/agatha");
define("AGATHA_PATH", ROOT_PATH."/excel-devs/agatha");
define("AGATHA_WRITER_FILE_PATH", AGATHA_PATH."/agathasourcefiles");
define("AGATHA_CONFIG_FILE", AGATHA_PATH."/agatha_config.txt");

//MENLOOK
define("MENLOOK_PATH", ROOT_PATH."/excel-devs/menlook");
define("MENLOOK_WRITER_FILE_PATH", MENLOOK_PATH."/writer-files");
define("MENLOOK_SITE_URL", "http://www.menlook.com/fr/maroquinerie-homme/");

//CDISCOUNT
define("CDISCOUNT_PATH", ROOT_PATH."/excel-devs/cdiscount");
define("CDISCOUNT_WRITER_FILE_PATH", CDISCOUNT_PATH."/writer-files");

//KIOSKEA
define("KIOSKEA_PATH", ROOT_PATH."/excel-devs/kioskea");
define("KIOSKEA_WRITER_FILE_PATH", KIOSKEA_PATH."/writer-files");

//TRUFFAUT
define("TRUFFAUT_PATH", ROOT_PATH."/excel-devs/truffaut");
define("TRUFFAUT_WRITER_FILE_PATH", TRUFFAUT_PATH."/writer-files");
define("TRUFFAUT_WRITERFILE_PATH", TRUFFAUT_PATH."/writerfiles");
define("TRUFFAUT_URL", SITE_URL."/excel-devs/truffaut");
define("TRUFFAUT_IMAGE_PATH", ROOT_PATH."/CLIENTS/Truffaut");
define("TRUFFAUT_CLIENT_REF_URL", TRUFFAUT_URL.'/view-pictures.php?client=TRUFFAUT&reference=');
define("TRUFFAUT_CLIENT_URL", SITE_URL.'/truffaut');

//MISC
define("MISC_PATH", ROOT_PATH."/excel-devs/miscellaneous");
define("MISC_URL", SITE_URL."/excel-devs/miscellaneous");
define("MISC_XLSCOMPARE_FILE_PATH", MISC_PATH."/xls-compare-files");
define("MISC_COPIERCOLLER_FILE_PATH", MISC_PATH."/copiercoller");
define("MISC_CREATELOT_FILE_PATH", MISC_PATH."/lot-files");
define("MISC_WORD_COUNT_FILE_PATH", MISC_PATH."/word-count-files");
define("MISC_XLSCOMPARE_FILE_REL_PATH", MISC_URL."/xls-compare-files/");
define("MISC_GROUP_TOOL_PATH", MISC_URL."/grouptool_files/");
define("MISC_XLSCOMPARE_FILE_UPLOAD_PATH", MISC_PATH."/xls-compare-files/uploads");
define("MISC_GROUP_TOOL_UPLOAD_PATH", MISC_PATH."/grouptool_files/uploads");
define("MISC_XLSCOMPARE_WEB_UPLOAD_PATH", MISC_URL."/xls-compare-files/uploads");
define("MISC_GROUP_TOOL_WEB_UPLOAD_PATH", MISC_URL."/grouptool_files/uploads");
define("MISC_TAG_UPLOAD_FILE_PATH", MISC_PATH."/tag-files/uploads");
define("MISC_TAG_UPLOAD_URL", MISC_URL."/tag-files/uploads");
define("MISC_TAGVALIDATION_FILE_PATH", MISC_PATH."/tagvalidationtool");

//ARMAND_THIERY 1
define("ARMAND_THIERY1_PATH", ROOT_PATH."/excel-devs/armand_thiery");
define("ARMAND_THIERY1_WRITER_FILE_PATH", ARMAND_THIERY1_PATH."/writer-files");

//CELIO
define("CELIO_URL", SITE_URL."/excel-devs/celio");
define("CELIO_IMAGE_PATH", ROOT_PATH."/CLIENTS/CELIO");
define("CELIO_SOURCE_FILE_URL", CELIO_URL."/source-files");
define("CELIO_WRITER_FILE_URL", CELIO_URL."/writer-files");
define("CELIO_LOOKBOOK_WRITER_FILE_URL", CELIO_URL."/lookbook-writer-files");
define("CELIO_PATH", ROOT_PATH."/excel-devs/celio");
define("CELIO_SOURCE_FILE_PATH", CELIO_PATH."/source-files");
define("CELIO_WRITER_FILE_PATH", CELIO_PATH."/writer-files");
define("CELIO_LOOKBOOK_WRITER_FILE_PATH", CELIO_PATH."/lookbook-writer-files");
define("CELIO_CONFIG_FILE", CELIO_PATH."/config.txt");
define("CELIO_LOOKBOOK_CONFIG_FILE", CELIO_PATH."/config_lookbook.txt");

//LIVRAISON
define("LIVRAISON_URL", SITE_URL."/excel-devs/livraison");
define("LIVRAISON_PATH", ROOT_PATH."/excel-devs/livraison");
define("LIVRAISON_WRITER_FILE_PATH", LIVRAISON_PATH."/writer-files");

//KIABI
define("KIABI_URL", SITE_URL."/excel-devs/kiabi");
define("KIABI_PATH", ROOT_PATH."/excel-devs/kiabi");
define("KIABI_XML_FILE_PATH", KIABI_PATH."/xml-files");

//MISTER SPECX
define("MISTERSPECX_URL", SITE_URL."/excel-devs/misterspecx");
define("MISTERSPECX_PATH", ROOT_PATH."/excel-devs/misterspecx");
define("MISTERSPECX_WRITER_FILE_PATH", MISTERSPECX_PATH."/files");

//GSTK
define("GSTK_URL", SITE_URL."/excel-devs/god-save-the-kids");
define("GSTK_PATH", ROOT_PATH."/excel-devs/god-save-the-kids");
define("GSTK_WRITER_FILE_PATH", GSTK_PATH."/writer-files");

//LEPARISIEN
define("LEPARISIEN_URL", SITE_URL."/excel-devs/le-parisien");
define("LEPARISIEN_PATH", ROOT_PATH."/excel-devs/le-parisien");
define("LEPARISIEN_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files");
define("LEPARISIEN_COPYDATA_FILE_PATH", LEPARISIEN_PATH."/copydata");
define("LEPARISIEN_PRENOMS_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/prenoms");
define("LEPARISIEN_RECETTES_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/recettes");
define("LEPARISIEN_PRENOMS1_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/prenoms1");
define("LEPARISIEN_RECETTES1_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/recettes1");
define("LEPARISIEN_PRENOMS2_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/prenoms2");
define("LEPARISIEN_RECETTES2_WRITER_FILE_PATH", LEPARISIEN_PATH."/writer-files/recettes2");
define("LEPARISIEN_WRITER_FILE_PATH11", LEPARISIEN_PATH."/writer-files/11");

//LIVRAISON LP
define("LIVRAISON_LP_URL", SITE_URL."/excel-devs/livraisonlp");
define("LIVRAISON_LP_PATH", ROOT_PATH."/excel-devs/livraisonlp");
define("LIVRAISON_LP_WRITER_FILE_PATH", LIVRAISON_LP_PATH."/writer-files");

//REZIDOR
define("REZIDOR_URL", SITE_URL."/excel-devs/rezidor");
define("REZIDOR_PATH", ROOT_PATH."/excel-devs/rezidor");
define("REZIDOR_WRITER_FILE_PATH", REZIDOR_PATH."/writer-files");

//LESECHOS
define("LESECHOS_URL", SITE_URL."/excel-devs/lesechos");
define("LESECHOS_PATH", ROOT_PATH."/excel-devs/lesechos");
define("LESECHOS_WRITER_FILE_PATH", LESECHOS_PATH."/writer-files");


//GALERIES LAFAYETTE
define("GALERIES_LAFAYETTE_URL", SITE_URL."/excel-devs/galeries_lafayette");
define("GALERIES_LAFAYETTE_PATH", ROOT_PATH."/excel-devs/galeries_lafayette");
define("GALERIES_LAFAYETTE_IMAGE_PATH", ROOT_PATH."/CLIENTS/GALERIES_LAFAYETTE");
define("GALERIES_LAFAYETTE_CLIENT_REF_URL", GALERIES_LAFAYETTE_URL.'/view-pictures.php?client=GALERIES_LAFAYETTE&reference=');
define("GALERIES_LAFAYETTE_CLIENT_URL", SITE_URL.'/galeries_lafayette');
define("GALERIES_LAFAYETTE_WRITER_FILE_PATH", GALERIES_LAFAYETTE_PATH."/writer-files");

//BASH
define("BASH_URL", SITE_URL."/excel-devs/bash");
define("BASH_PATH", ROOT_PATH."/excel-devs/bash");
define("BASH_IMAGE_PATH", ROOT_PATH."/CLIENTS/BASH");
define("BASH_CLIENT_REF_URL", BASH_URL.'/view-pictures.php?client=BASH&reference=');
define("BASH_CLIENT_URL", SITE_URL.'/bash');
define("BASH_WRITER_FILE_PATH", BASH_PATH."/writer-files");
define("BASH_REF_WRITER_FILE_PATH", BASH_PATH."/ref-files");
define("BASH_SENTENCE_FILE_PATH", BASH_PATH."/sentence-files");
define("BASH_CONFIG_FILE", BASH_WRITER_FILE_PATH."/products.txt");

//BASH NEW
define("BASH_DELIVERY_FILE_PATH_NEW", BASH_PATH."/dev2");
define("BASH_WRITER_FILE_PATH_NEW", BASH_PATH."/dev1");


//CHEVIGNON
define("CHEVIGNON_URL", SITE_URL."/excel-devs/chevignon");
define("CHEVIGNON_PATH", ROOT_PATH."/excel-devs/chevignon");
define("CHEVIGNON_IMAGE_PATH", ROOT_PATH."/CLIENTS/CHEVIGNON");
define("CHEVIGNON_CLIENT_URL", SITE_URL.'/chevignon');
define("CHEVIGNON_CLIENT_REF_URL", CHEVIGNON_URL.'/view-pictures.php?client=CHEVIGNON&reference=');
define("CHEVIGNON_WRITER_FILE_PATH", CHEVIGNON_PATH."/writer-files");
define("CHEVIGNON_CONFIG_FILE", CHEVIGNON_WRITER_FILE_PATH."/products.txt");

//LBMCHE paths
define("LBMCHE_URL", SITE_URL."/excel-devs/lbm");
define("LBMCHE_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEBONMARCHE");
define("LBMCHE_PATH", ROOT_PATH."/excel-devs/lbm");
define("LBMCHE_EP_SOURCE_PATH", LBMCHE_PATH."/ep-source-files");
define("LBMCHE_EP_CONFIG_FILE", LBMCHE_PATH."/ep-config.txt");
define("LBMCHE_REF_SOURCE_PATH", LBMCHE_PATH."/lbm-source-files");
define("LBMCHE_REF_CONFIG_FILE", LBMCHE_PATH."/lbm-config.txt");
define("LBMCHE_WRITER_FILE_PATH", LBMCHE_PATH."/writer-files");
define("LBMCHE_REF_WRITER_FILE_PATH", LBMCHE_PATH."/lebon/writer-files");
define("LBMCHE_REF_SEARCH_PATH", LBMCHE_PATH."/reference-search");

//COPCOPINE
define("COPCOPINE_URL", SITE_URL."/excel-devs/copcopine");
define("COPCOPINE_PATH", ROOT_PATH."/excel-devs/copcopine");
define("COPCOPINE_WRITER_FILE_PATH", COPCOPINE_PATH."/writer-files");
define("COPCOPINE_DOUBLON_FILE_PATH", COPCOPINE_PATH."/doublon-files");

//DECATHLON
define("DECATHLON_URL", SITE_URL."/excel-devs/decathlon");
define("DECATHLON_PATH", ROOT_PATH."/excel-devs/decathlon");
define("DECATHLON_XML_FILE_PATH", DECATHLON_PATH."/xml-files");
define("DECATHLON_XML_FILE_PATH2", DECATHLON_PATH."/xml");
define("DECATHLON_XML_FILE_PATH3", DECATHLON_PATH."/xlsx-files");
define("DECATHLON_WRITER_FILE_PATH", DECATHLON_PATH."/writer-files");

define("MENU_CONFIG_JSON", SITE_URL . "/includes/clientsmenu.json");
define("MENU_URL", SITE_URL . "/excel-devs/");
define("SPEC_URL", MENU_URL . "spec.php?u=");
define("SPEC_FLDR",INCLUDE_PATH."/spec/");

//DISTRICENTER
define("DISTRICENTER_URL", SITE_URL."/excel-devs/districenter");
define("DISTRICENTER_PATH", ROOT_PATH."/excel-devs/districenter");
define("DISTRICENTER_SOURCE_PATH", DISTRICENTER_PATH."/pd-1/source");
define("DISTRICENTER_WRITER_FILE_PATH", DISTRICENTER_PATH."/writer-files");
define("DISTRICENTER_SOURCE_FILE_PATH", DISTRICENTER_PATH."/pd-1/source/districenter.xlsx");
define("DISTRICENTER_CLIENT_REF_URL", DISTRICENTER_URL.'/pictures.php?client=DISTRICENTER&reference=');
define("DISTRICENTER_CONFIG_FILE", DISTRICENTER_PATH."/config.txt");
define("DISTRICENTER_IMAGE_PATH", ROOT_PATH."/CLIENTS/DISTRICENTER");

//OUEST
define("OUEST_URL", SITE_URL."/excel-devs/ouest");
define("OUEST_PATH", ROOT_PATH."/excel-devs/ouest");
define("OUEST_SOURCE_PATH", OUEST_PATH."/pd-1/source");
define("OUEST_WRITER_FILE_PATH", OUEST_PATH."/writer-files");

//M6_WEB.COM
define("M6_WEB_PATH", ROOT_PATH."/excel-devs/m6-web");
define("M6_WEB_WRITER_FILE_PATH", M6_WEB_PATH."/writer-files");

//BORDERLINX
define("BORDERLINX_PATH", ROOT_PATH."/excel-devs/borderlinx");
define("BORDERLINX_WRITER_FILE_PATH", BORDERLINX_PATH."/writer-files");

//SEO_SALOMON
define("SEO_SALOMON_PATH", ROOT_PATH."/excel-devs/seo-salomon");
define("SEO_SALOMON_WRITER_FILE_PATH", SEO_SALOMON_PATH."/writer-files");

//La Redoute
define("LA_REDOUTE_PATH", ROOT_PATH."/excel-devs/la-redoute");
define("LA_REDOUTE_WRITER_FILE_PATH", LA_REDOUTE_PATH."/writer-files");

//TUI
define("TUI_PATH",ROOT_PATH."/excel-devs/tui");
define("TUI_WRITER_FILE_PATH",TUI_PATH."/writer-files");

//VOYAGES
define("VOYAGES_PATH",ROOT_PATH."/excel-devs/voyages");
define("VOYAGES_WRITER_FILE_PATH",VOYAGES_PATH."/writer-files");

//Toyr's Run
define("TOYR_PATH",ROOT_PATH."/excel-devs/toyr");
define("TOYR_WRITER_FILE_PATH",TOYR_PATH."/writer-files");

//BNP
define("BNP_PATH",ROOT_PATH."/excel-devs/bnp");
define("BNP_WRITER_FILE_PATH",BNP_PATH."/writer-files");

//InterRent
define("INTERRENT_PATH", ROOT_PATH."/excel-devs/interrent");
define("INTERRENT_WRITER_FILE_PATH", INTERRENT_PATH."/writer-files");
define("INTERRENT_UPLOAD_FILE_PATH", INTERRENT_PATH."/uploads");
define("INTERRENT_DATA_PATH", INTERRENT_PATH."/data");

//Garnier
define("GARNIER_PATH", ROOT_PATH."/excel-devs/garnier");
define("GARNIER_WRITER_FILE_PATH", GARNIER_PATH."/writer-files");
define("GARNIER_UPLOAD_FILE_PATH", GARNIER_PATH."/uploads");
define("GARNIER_DATA_PATH", GARNIER_PATH."/data");
define("GARNIER_IMAGE_PATH", ROOT_PATH."/CLIENTS/Garnier");
define("GARNIER_URL", SITE_URL."/excel-devs/garnier");

//COLUMBIA
define("COLUMBIA_URL", SITE_URL."/excel-devs/columbia");
define("COLUMBIA_PATH", ROOT_PATH."/excel-devs/columbia");
define("COLUMBIA_WRITER_FILE_PATH", COLUMBIA_PATH."/writer-files");
define("COLUMBIA_IMAGE_PATH", ROOT_PATH."/CLIENTS/COLUMBIA");

//Conforama
define("CONFORAMA_PATH", ROOT_PATH."/excel-devs/conforama");
define("CONFORAMA_WRITER_FILE_PATH", CONFORAMA_PATH."/writer-files");

//MENU CONFIG *****Added by Anoop*****
$client_menu_style = (str_replace("-","",$_REQUEST['client']))."_MENU";
$$client_menu_style = ' style="display:block;"';
$carbon_clients = array('LACITY','CACHECACHE','BREAL','BONOBO','BONOBO-UK','SCOTTAGE','SCOTTAGE2','MORGAN','MORGAN-UK','ARMAND_THIERY','TOSCANE');
if(in_array($_REQUEST['client'],$carbon_clients))
   $carbon_menu_style = ' style="display:block;"';
