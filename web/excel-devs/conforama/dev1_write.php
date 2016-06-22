<?php
/**
 * Split XLSX file into multiple column based on json
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 May 13 2016
 */
 

ob_start();

mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(INCLUDE_PATH."/PHPWord.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], CONFORAMA_WRITER_FILE_PATH."/dev1/", "dev1.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
         
    $file2  =   pathinfo($_FILES['userfile2']['name']) ;
    $ext2 = $file2['extension'];
    
    if($ext2 == 'xlsx')
    {
	
		/* XSLX READ */
		   $xls1Arr  = $basiclib->xlsx_read($_FILES['userfile2']['tmp_name']) ;
			$x=1;           
			while($x<sizeof($xls1Arr[0][0])) {
					$y=1;  
					while($y<=sizeof($xls1Arr[0][0][$x])) {
                        if(1){ // $y == 1 || $y == 2 || $y == 4 || $y == 8 || $y == 11 || $y == 13 || $y == 18 
							$xls1Arr[0][0][$x][$y]=str_replace("�","'",$xls1Arr[0][0][$x][$y]);
							$final_array[$x][$y-1]=isset($xls1Arr[0][0][$x][$y]) ? $xls1Arr[0][0][$x][$y] : '';
						}
						$y++;
				} 
				$final_array[$x]=array_values($final_array[$x]);
				$x++;			
			}
		/* CLOSE XSLX READ */
		
		/* XSLX READ FOR TEMPLATE OF XSLX */
		   $xls1Arr  = $basiclib->xlsx_read(CONFORAMA_PATH."/"."output_header_dev1.xlsx") ;
			$x=0;           
			while($x<sizeof($xls1Arr[0][0])) {
					$y=1;  
					while($y<=sizeof($xls1Arr[0][0][$x])) {
                        if(1){ // $y == 1 || $y == 2 || $y == 4 || $y == 8 || $y == 11 || $y == 13 || $y == 18 
							$xls1Arr[0][0][$x][$y]=str_replace("�","'",$xls1Arr[0][0][$x][$y]);
							$final_array_out[$x+1][$y-1]=isset($xls1Arr[0][0][$x][$y]) ? $xls1Arr[0][0][$x][$y] : '';
						}
						$y++;
				} 
				$final_array_out[$x+1]=array_values($final_array_out[$x+1]);
				$x++;			
			}
		/* CLOSE XSLX READ */
		
		//echo "<pre>";print_r ($final_array); echo "</pre>"; //exit;
		

		if(sizeof($final_array)>0)    
        { 
			$rand="Conforama-split".uniqid();
    	    $srcPath=CONFORAMA_WRITER_FILE_PATH."/dev1/".$rand.".xlsx";
    	    $outputData = process_data($final_array,$final_array_out);
			writeXlsxConforamaNewDev1($outputData,$srcPath);
			
			header("Location:dev1.php?msg=success&file=".$rand.".xlsx");
			
		}else{
			header("Location:dev1.php?msg=error");
		}		  
            
          
        }
        else
        {
            header("Location:dev1.php?msg=file_error");
        }
    	
}
else
    header("Location:dev1.php");


  /* process_data function
   * 
   *  create array with modify data from input
   *  @param $data - input file data in array
   *  @param $final_array_out - modified array with final data to write
   *  
   */

	function process_data($data,$final_array_out){
	
		//echo "Debug 3<pre>"; print_r ($data); echo "</pre>";
		//echo "Debug 3<pre>"; print_r ($final_array_out); echo "</pre>";	//exit;
	    $i=5;

		foreach ($data as $k=>$row){
			
			if(getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                $jsonData = json_decode(utf8_encode($row[15]),true);
            else
                $jsonData = json_decode(utf8_decode(utf8_encode($row[15])),true);
                
			$jsonData = call_user_func_array('array_merge', $jsonData);
			//echo "<pre>"; print_r ($jsonData); //exit;
			
			//echo "---------------------";
			//echo "Aménagement intérieur- ".$jsonData["Aménagement intérieur"]; 
			//echo "---------------------";
			
			$final_array_out[$i][0] = $row[0]; 
			$final_array_out[$i][1] = $row[1];
			$final_array_out[$i][2] = $row[3];
			$final_array_out[$i][3] = $row[7];
			$final_array_out[$i][4] = $row[17];
			$final_array_out[$i][5] = $row[10];
			$final_array_out[$i][6] = $row[12];
			$final_array_out[$i][7] = $row[13];
			$final_array_out[$i][8] = "";
			$final_array_out[$i][9] = $row[14];
			$final_array_out[$i][10] = $row[18];
			$final_array_out[$i][11] = isset($jsonData["Dimension couchage (cm : larg. x long. x epaiss.)"]) ? $jsonData["Dimension couchage (cm : larg. x long. x epaiss.)"] : "NA";
			
			$final_array_out[$i][12] = isset($jsonData["Largeur"]) ? $jsonData["Largeur"] : "NA";
			$final_array_out[$i][13] = isset($jsonData["Hauteur"]) ? $jsonData["Hauteur"] : "NA";
			$final_array_out[$i][14] = isset($jsonData["Epaisseur"]) ? $jsonData["Epaisseur"] : "NA";
			$final_array_out[$i][15] = isset($jsonData["Profondeur"]) ? $jsonData["Profondeur"] : "NA";
			$final_array_out[$i][16] = isset($jsonData["Epaisseur matelas"]) ? $jsonData["Epaisseur matelas"] : "NA";
			$final_array_out[$i][17] = isset($jsonData["Nombre de tiroirs"]) ? $jsonData["Nombre de tiroirs"] : "NA";
			$final_array_out[$i][18] = isset($jsonData["Nombre de niches"]) ? $jsonData["Nombre de niches"] : "NA";
			$final_array_out[$i][19] = isset($jsonData["Informations complémentaires"]) ? $jsonData["Informations complémentaires"] : "NA";
			$final_array_out[$i][20] = isset($jsonData["Nombre étagères / tablettes"]) ? $jsonData["Nombre étagères / tablettes"] : "NA";
			$final_array_out[$i][21] = isset($jsonData["Type de meuble tv"]) ? $jsonData["Type de meuble tv"] : "NA";
			
			$final_array_out[$i][22] = isset($jsonData["Supporte un tv de (maximum)"]) ? $jsonData["Supporte un tv de (maximum)"] : "NA";
			$final_array_out[$i][23] = isset($jsonData["Hauteur minimum"]) ? $jsonData["Hauteur minimum"] : "NA";
			$final_array_out[$i][24] = isset($jsonData["Hauteur maximum"]) ? $jsonData["Hauteur maximum"] : "NA";
			$final_array_out[$i][25] = isset($jsonData["Longueur"]) ? $jsonData["Longueur"] : "NA";
			$final_array_out[$i][26] = isset($jsonData["Longueur minimum"]) ? $jsonData["Longueur minimum"] : "NA";
			$final_array_out[$i][27] = isset($jsonData["Longueur maximum"]) ? $jsonData["Longueur maximum"] : "NA";
			$final_array_out[$i][28] = isset($jsonData["Type d'allonge"]) ? $jsonData["Type d'allonge"] : "NA";
			$final_array_out[$i][29] = isset($jsonData["Fixe ou pas"]) ? $jsonData["Fixe ou pas"] : "NA";
			$final_array_out[$i][30] = isset($jsonData["Monte et baisse"]) ? $jsonData["Monte et baisse"] : "NA";
			$final_array_out[$i][31] = isset($jsonData["Evolutif"]) ? $jsonData["Evolutif"] : "NA";
			
			$final_array_out[$i][32] = isset($jsonData["Plateau desserte intégré"]) ? $jsonData["Plateau desserte intégré"] : "NA";
			$final_array_out[$i][33] = isset($jsonData["Nombre de plateaux / étagères / tablettes"]) ? $jsonData["Nombre de plateaux / étagères / tablettes"] : "NA";
			$final_array_out[$i][34] = isset($jsonData["Matière / technologie"]) ? $jsonData["Matière / technologie"] : "NA";
			$final_array_out[$i][35] = isset($jsonData["2 faces de couchage"]) ? $jsonData["2 faces de couchage"] : "NA";
			$final_array_out[$i][36] = isset($jsonData["Matière 1"]) ? $jsonData["Matière 1"] : "NA";
			$final_array_out[$i][37] = isset($jsonData["Matière 2"]) ? $jsonData["Matière 2"] : "NA";
			$final_array_out[$i][38] = isset($jsonData["Matière du coutil"]) ? $jsonData["Matière du coutil"] : "NA";
			$final_array_out[$i][39] = isset($jsonData["Soutien"]) ? $jsonData["Soutien"] : "NA";
			$final_array_out[$i][40] = isset($jsonData["Type de structure"]) ? $jsonData["Type de structure"] : "NA";
			$final_array_out[$i][41] = isset($jsonData["Matière principale"]) ? $jsonData["Matière principale"] : "NA"; 
			
			$final_array_out[$i][42] = isset($jsonData["Façade"]) ? $jsonData["Façade"] : "NA"; 
			$final_array_out[$i][43] = isset($jsonData["Aménagement intérieur"]) ? $jsonData["Aménagement intérieur"] : "NA"; 
			$final_array_out[$i][44] = isset($jsonData["Miroir"]) ? $jsonData["Miroir"] : "NA"; 
			$final_array_out[$i][45] = isset($jsonData["Type de porte"]) ? $jsonData["Type de porte"] : "NA"; 
			$final_array_out[$i][46] = isset($jsonData["Nombre de porte"]) ? $jsonData["Nombre de porte"] : "NA"; 
			$final_array_out[$i][47] = isset($jsonData["Poignées"]) ? $jsonData["Poignées"] : "NA"; 
			$final_array_out[$i][48] = isset($jsonData["Piètement"]) ? $jsonData["Piètement"] : "NA"; 
			$final_array_out[$i][49] = isset($jsonData["Matière structure"]) ? $jsonData["Matière structure"] : "NA"; 
			$final_array_out[$i][50] = isset($jsonData["Matière"]) ? $jsonData["Matière"] : "NA";
			$final_array_out[$i][51] = isset($jsonData["Tiroir de lit"]) ? $jsonData["Tiroir de lit"] : "NA";
		
			$final_array_out[$i][52] = isset($jsonData["Eclairage intégré"]) ? $jsonData["Eclairage intégré"] : "NA";
			$final_array_out[$i][53] = isset($jsonData["Informations complémentaires"]) ? $jsonData["Informations complémentaires"] : "NA";
			$final_array_out[$i][54] = isset($jsonData["Matière piètement"]) ? $jsonData["Matière piètement"] : "NA";
			$final_array_out[$i][55] = isset($jsonData["Matière secondaire"]) ? $jsonData["Matière secondaire"] : "NA";
			$final_array_out[$i][56] = isset($jsonData["Finition"]) ? $jsonData["Finition"] : "NA";
			$final_array_out[$i][57] = isset($jsonData["Vendu"]) ? $jsonData["Vendu"] : "NA";
			$final_array_out[$i][58] = isset($jsonData["Revêtement couette/assise"]) ? $jsonData["Revêtement couette/assise"] : "NA";
			$final_array_out[$i][59] = isset($jsonData["Revêtement"]) ? $jsonData["Revêtement"] : "NA";
			$final_array_out[$i][60] = isset($jsonData["Matière garnissage"]) ? $jsonData["Matière garnissage"] : "NA";
			$final_array_out[$i][61] = isset($jsonData["Matière pieds"]) ? $jsonData["Matière pieds"] : "NA";
			
			$final_array_out[$i][62] = isset($jsonData["Coffre de rangement"]) ? $jsonData["Coffre de rangement"] : "NA";
			$final_array_out[$i][63] = isset($jsonData["Coussin(s) cale-reins"]) ? $jsonData["Coussin(s) cale-reins"] : "NA";
			$final_array_out[$i][64] = isset($jsonData["Accoudoirs réglables"]) ? $jsonData["Accoudoirs réglables"] : "NA";
			$final_array_out[$i][65] = isset($jsonData["Tétières / appuie-têtes ajustables"]) ? $jsonData["Tétières / appuie-têtes ajustables"] : "NA";
			$final_array_out[$i][66] = isset($jsonData["Liseuse"]) ? $jsonData["Liseuse"] : "NA";
			$final_array_out[$i][67] = isset($jsonData["Type de poignée"]) ? $jsonData["Type de poignée"] : "NA";
			$final_array_out[$i][68] = isset($jsonData["Monté sur roulettes"]) ? $jsonData["Monté sur roulettes"] : "NA";
			$final_array_out[$i][69] = isset($jsonData["Plateau supérieur"]) ? $jsonData["Plateau supérieur"] : "NA";
			$final_array_out[$i][70] = isset($jsonData["Passe cables"]) ? $jsonData["Passe cables"] : "NA";
			$final_array_out[$i][71] = isset($jsonData["Meuble sur roulettes"]) ? $jsonData["Meuble sur roulettes"] : "NA";
			
			$final_array_out[$i][72] = isset($jsonData["Finition piètement"]) ? $jsonData["Finition piètement"] : "NA";
			$final_array_out[$i][73] = isset($jsonData["Type de sommier"]) ? $jsonData["Type de sommier"] : "NA";
			$final_array_out[$i][74] = isset($jsonData["Nombre de lattes"]) ? $jsonData["Nombre de lattes"] : "NA";
			$final_array_out[$i][75] = isset($jsonData["Matière suspension"]) ? $jsonData["Matière suspension"] : "NA";
			//$final_array_out[$i][75] = isset($jsonData["Vendu"]) ? $jsonData["Vendu"] : "NA";
			$final_array_out[$i][76] = isset($jsonData["Nombre de colis"]) ? $jsonData["Nombre de colis"] : "NA";
			$final_array_out[$i][77] = isset($jsonData["Dimension colis"]) ? $jsonData["Dimension colis"] : "NA";
			$final_array_out[$i][78] = isset($jsonData["Dimension colis 2"]) ? $jsonData["Dimension colis 2"] : "NA";
			$final_array_out[$i][79] = isset($jsonData["Poids colis"]) ? $jsonData["Poids colis"] : "NA";
			$final_array_out[$i][80] = isset($jsonData["Poids total colis"]) ? $jsonData["Poids total colis"] : "NA";	 
			$final_array_out[$i][81] = '';	 

		  $i++;
		}	
		
	//echo "Debug 3<pre>"; print_r ($final_array_out); echo "</pre>"; exit;	 
		
		return $final_array_out;
	}


	function checkArrayValueExist(){
		
	}	


  /* writeXlsxConforamaDev1 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */
	 
	 
	  function writeXlsxConforamaNewDev1($data,$file_path)
    {
		
	
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        /* Create new PHPExcel object*/
        $objPHPExcel = new PHPExcel();
        
        /* Set properties*/
        $objPHPExcel->getProperties()->setCreator("Edit-Place");
        
        /* Add some data */
        $objPHPExcel->setActiveSheetIndex(0);


        $rowCount=0;
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            $header_colors=array();
            

		   //echo "Debug 4 $k<pre>"; print_r ($colors[$k]); echo "</pre>";	exit;
	       $header_colors = $colors[$k];
            
            
            foreach ($row as $key => $value)
            {	/* Based on OS Apply Encoding */
				if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                {      
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("", "'", $value) ;
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}
				//$value=str_replace("_x0019_","'",$value);
								
				//echo $value."-".$col."-".$rowCount;
				
				if($rowCount < 4){
				if(($col == 'A' || $col == 'B' || $col == 'C' || $col == 'D' || $col == 'E' || $col == 'F' || $col == 'G' || $col == 'H')){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'fbe5d6')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}				
				elseif(($col == 'I')){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'e2f0d9')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
				elseif($col == 'J' || $col == 'K'){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'bdd7ee')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
				else{
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'deebf7')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
			    }else{
					if($value == "NA"){
						  $stylArr1 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffc7ce')));
						  $objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
						}  
				}
				
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);

                $col++;
                
            }
            $rowCount++;
        }
        //exit;

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
       
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('4')->getFont()->setBold(true);
        
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
 
   
    
?>

