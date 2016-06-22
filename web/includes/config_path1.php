<?php
ini_set('display_errors', 1);

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

ini_set('memory_limit', '500M');
ini_set('suhosin.memory_limit', '500M');
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');
ini_set('max_execution_time',5000);
//ini_set('max_input_time',"-1");

//define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
//define("INCLUDE_PATH",ROOT_PATH."/includes");

define("SITE_URL","http://clients.edit-place.com");

define("KORBEN_IMAGE_PATH", ROOT_PATH."/CLIENTS/KORBEN");

define("CACHECACHE_PATH", ROOT_PATH."/excel-devs/korben/cachecache");
define("CACHECACHE_SOURCE_PATH", CACHECACHE_PATH."/source-files");
define("CACHECACHE_WRITER_FILE_PATH", CACHECACHE_PATH."/writer-files");
define("CACHECACHE_CONFIG_FILE", CACHECACHE_PATH."/config.txt");

define("BREAL_PATH", ROOT_PATH."/excel-devs/korben/breal");
define("BREAL_SOURCE_PATH", BREAL_PATH."/source-files");
define("BREAL_WRITER_FILE_PATH", BREAL_PATH."/writer-files");
define("BREAL_CONFIG_FILE", BREAL_PATH."/config.txt");

define("BONOBO_PATH", ROOT_PATH."/excel-devs/korben/bonobo");
define("BONOBO_SOURCE_PATH", BONOBO_PATH."/source-files");
define("BONOBO_WRITER_FILE_PATH", BONOBO_PATH."/writer-files");
define("BONOBO_CONFIG_FILE", BONOBO_PATH."/config.txt");

define("BONOBO_UK_PATH", ROOT_PATH."/excel-devs/korben/bonobo-uk");
define("BONOBO_UK_SOURCE_PATH", BONOBO_UK_PATH."/source-files");
define("BONOBO_UK_WRITER_FILE_PATH", BONOBO_UK_PATH."/writer-files");
define("BONOBO_UK_CONFIG_FILE", BONOBO_UK_PATH."/config.txt");

define("SCOTTAGE_PATH", ROOT_PATH."/excel-devs/korben/scottage");
define("SCOTTAGE_SOURCE_PATH", SCOTTAGE_PATH."/source-files");
define("SCOTTAGE_WRITER_FILE_PATH", SCOTTAGE_PATH."/writer-files");
define("SCOTTAGE_CONFIG_FILE", SCOTTAGE_PATH."/config.txt");

define("SCOTTAGE2_PATH", ROOT_PATH."/excel-devs/korben/scottage2");
define("SCOTTAGE2_SOURCE_PATH", SCOTTAGE2_PATH."/source-files");
define("SCOTTAGE2_WRITER_FILE_PATH", SCOTTAGE2_PATH."/writer-files");
define("SCOTTAGE2_CONFIG_FILE", SCOTTAGE2_PATH."/config.txt");

define("MORGAN_PATH", ROOT_PATH."/excel-devs/korben/morgan");
define("MORGAN_SOURCE_PATH", MORGAN_PATH."/source-files");
define("MORGAN_WRITER_FILE_PATH", MORGAN_PATH."/writer-files");
define("MORGAN_CONFIG_FILE", MORGAN_PATH."/config.txt");

define("MORGAN_UK_PATH", ROOT_PATH."/excel-devs/korben/morgan-uk");
define("MORGAN_UK_SOURCE_PATH", MORGAN_UK_PATH."/source-files");
define("MORGAN_UK_WRITER_FILE_PATH", MORGAN_UK_PATH."/writer-files");
define("MORGAN_UK_CONFIG_FILE", MORGAN_UK_PATH."/config.txt");

define("ARMAND_THIERY_PATH", ROOT_PATH."/excel-devs/korben/armand_thiery");
define("ARMAND_THIERY_SOURCE_PATH", ARMAND_THIERY_PATH."/source-files");
define("ARMAND_THIERY_WRITER_FILE_PATH", ARMAND_THIERY_PATH."/writer-files");
define("ARMAND_THIERY_CONFIG_FILE", ARMAND_THIERY_PATH."/config.txt");

define("TOSCANE_PATH", ROOT_PATH."/excel-devs/korben/toscane");
define("TOSCANE_SOURCE_PATH", TOSCANE_PATH."/source-files");
define("TOSCANE_WRITER_FILE_PATH", TOSCANE_PATH."/writer-files");
define("TOSCANE_CONFIG_FILE", TOSCANE_PATH."/config.txt");

define("ANDRE_URL", SITE_URL."/excel-devs/andre");
define("ANDRE_IMAGE_PATH", ROOT_PATH."/CLIENTS/ANDRE");
define("ANDRE_PATH", ROOT_PATH."/excel-devs/andre");
define("ANDRE_WRITER_FILE_PATH", ANDRE_PATH."/writer-files");

//MCHE paths
define("MCHE_URL", SITE_URL."/excel-devs/mche");
define("MCHE_IMAGE_PATH", ROOT_PATH."/CLIENTS/LEBONMARCHE");
define("MCHE_PATH", ROOT_PATH."/excel-devs/mche");
define("MCHE_EP_SOURCE_PATH", MCHE_PATH."/ep-source-files");
define("MCHE_EP_CONFIG_FILE", MCHE_PATH."/ep-config.txt");
define("MCHE_REF_SOURCE_PATH", MCHE_PATH."/lbm-source-files");
define("MCHE_REF_CONFIG_FILE", MCHE_PATH."/lbm-config.txt");
define("MCHE_WRITER_FILE_PATH", MCHE_PATH."/writer-files");
define("MCHE_REF_WRITER_FILE_PATH", MCHE_PATH."/lebon/writer-files");
define("MCHE_REF_SEARCH_PATH", MCHE_PATH."/reference-search");
define("MCHE_WRITER_FILE_CONFIG", MCHE_PATH."/writer-files-config.txt");
define("MCHE_ARRAY_SRCXLS", MCHE_PATH."/array-srcxls.txt");
define("MCHE_ARRAY_MRGXLS", MCHE_PATH."/array-mrgxls.txt");
define("MCHE_XLS3_WRITER_FILE_CONFIG", MCHE_PATH."/xls3-writer-files-config.txt");

//Caroll
define("CAROLL_URL", SITE_URL."/excel-devs/caroll");
define("CAROLL_IMAGE_PATH", ROOT_PATH."/CLIENTS/CAROLL");
define("CAROLL_PATH", ROOT_PATH."/excel-devs/caroll");
define("CAROLL_EP_SOURCE_PATH", CAROLL_PATH."/ep-source-files");
define("CAROLL_EP_CONFIG_FILE", CAROLL_PATH."/ep-config.txt");
define("CAROLL_REF_SOURCE_PATH", CAROLL_PATH."/caroll-source-files");
define("CAROLL_REF_CONFIG_FILE", CAROLL_PATH."/caroll-config.txt");
define("CAROLL_WRITER_FILE_PATH", CAROLL_PATH."/writer-files");
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

//EBOOKERS
define("EBOOKERS_URL", SITE_URL."/excel-devs/ebookers");
define("EBOOKERS_PATH", ROOT_PATH."/excel-devs/ebookers");
define("EBOOKERS_WRITER_FILE_PATH", EBOOKERS_PATH."/writer-files");
define("EBOOKERS_WRITER_FILE_PATH_DEV1", EBOOKERS_PATH."/writer-files/dev1");
define("EBOOKERS_WRITER_FILE_PATH1", EBOOKERS_PATH."/writer-files1");

//EBOOKERS
define("NAFNAF_URL", SITE_URL."/excel-devs/nafnaf");
define("NAFNAF_IMAGE_PATH", ROOT_PATH."/CLIENTS/NAFNAF");
define("NAFNAF_PATH", ROOT_PATH."/excel-devs/nafnaf");
define("NAFNAF_CLIENT_REF_URL", NAFNAF_URL.'/view-pictures.php?client=NAFNAF&reference=');
define("NAFNAF_CLIENT_URL", SITE_URL.'/nafnaf');
define("NAFNAF_WRITER_FILE_PATH", NAFNAF_PATH."/writer-files");

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
define("SANMARINA_IMAGE_PATH", ROOT_PATH."/CLIENTS/SANMARINA/visuels PE14");
define("SANMARINA_PATH", ROOT_PATH."/excel-devs/sanmarina");
define("SANMARINA_CLIENT_REF_URL", SANMARINA_URL.'/view-pictures.php?client=SANMARINA&reference=');
define("SANMARINA_CLIENT_URL", SITE_URL.'/sanmarina');
define("SANMARINA_WRITER_FILE_PATH", SANMARINA_PATH."/writer-files");

//NEWMAN
define("NEWMAN_URL", SITE_URL."/excel-devs/newman");
define("NEWMAN_PATH", ROOT_PATH."/excel-devs/newman");
define("NEWMAN_IMAGE_PATH", ROOT_PATH."/CLIENTS/NEWMAN");
define("NEWMAN_CLIENT_REF_URL", NEWMAN_URL.'/view-pictures.php?client=NEWMAN&reference=');
define("NEWMAN_CLIENT_URL", SITE_URL.'/newman');
define("NEWMAN_WRITER_FILE_PATH", NEWMAN_PATH."/writer-files");

//MONOPRIX
define("MONOPRIX_PATH", ROOT_PATH."/excel-devs/monoprix");
define("MONOPRIX_IMAGE_PATH", ROOT_PATH."/CLIENTS/MONOPRIX");
define("MONOPRIX_CLIENT_URL", SITE_URL.'/monoprix');
define("MONOPRIX_WRITER_FILE_PATH", MONOPRIX_PATH."/writer-files");

//COSMOPARIS
define("COSMOPARIS_URL", SITE_URL."/excel-devs/cosmoparis");
define("COSMOPARIS_PATH", ROOT_PATH."/excel-devs/cosmoparis");
define("COSMOPARIS_IMAGE_PATH", ROOT_PATH."/CLIENTS/COSMOPARIS");
define("COSMOPARIS_CLIENT_REF_URL", COSMOPARIS_URL.'/view-pictures.php?client=COSMOPARIS&reference=');
define("COSMOPARIS_CLIENT_URL", SITE_URL.'/cosmoparis');
define("COSMOPARIS_WRITER_FILE_PATH", COSMOPARIS_PATH."/writer-files");
define("COSMOPARIS_CONFIG_FILE", COSMOPARIS_WRITER_FILE_PATH."/products.txt");

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

//MISC
define("MISC_PATH", ROOT_PATH."/excel-devs/miscellaneous");
define("MISC_URL", SITE_URL."/excel-devs/miscellaneous");
define("MISC_XLSCOMPARE_FILE_PATH", MISC_PATH."/xls-compare-files");
define("MISC_XLSCOMPARE_FILE_REL_PATH", MISC_URL."/xls-compare-files/");
define("MISC_XLSCOMPARE_FILE_UPLOAD_PATH", MISC_PATH."/xls-compare-files/uploads");
define("MISC_XLSCOMPARE_WEB_UPLOAD_PATH", MISC_URL."/xls-compare-files/uploads");

//ARMAND_THIERY 1
define("ARMAND_THIERY1_PATH", ROOT_PATH."/excel-devs/armand_thiery");
define("ARMAND_THIERY1_WRITER_FILE_PATH", ARMAND_THIERY1_PATH."/writer-files");

//CELIO
define("CELIO_URL", SITE_URL."/excel-devs/celio");
define("CELIO_WRITER_FILE_URL", CELIO_URL."/writer-files");
define("CELIO_LOOKBOOK_WRITER_FILE_URL", CELIO_URL."/lookbook-writer-files");
define("CELIO_PATH", ROOT_PATH."/excel-devs/celio");
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

//LIVRAISON LP
define("LIVRAISON_LP_URL", SITE_URL."/excel-devs/livraisonlp");
define("LIVRAISON_LP_PATH", ROOT_PATH."/excel-devs/livraisonlp");
define("LIVRAISON_LP_WRITER_FILE_PATH", LIVRAISON_LP_PATH."/writer-files");

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
define("LBMCHE_REF_XLS_FILE", LBMCHE_PATH."/lbm_reference_global.xls");
define("LBMCHE_REF_TXT_FILE", LBMCHE_PATH."/lbm_reference_global.txt");
define("LBMCHE_WRITER_FILE_PATH", LBMCHE_PATH."/writer-files");
define("LBMCHE_REF_WRITER_FILE_PATH", LBMCHE_PATH."/lebon/writer-files");
define("LBMCHE_REF_SEARCH_PATH", LBMCHE_PATH."/reference-search");

//MENU CONFIG *****Added by Anoop*****
$client_menu_style = (str_replace("-","",$_REQUEST['client']))."_MENU";
$$client_menu_style = ' style="display:block;"';
$carbon_clients = array('CACHECACHE','BREAL','BONOBO','BONOBO-UK','SCOTTAGE','SCOTTAGE2','MORGAN','MORGAN-UK','ARMAND_THIERY','TOSCANE');
if(in_array($_REQUEST['client'],$carbon_clients))
   $carbon_menu_style = ' style="display:block;"';
  
