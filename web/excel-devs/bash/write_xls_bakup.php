<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']) && isset($_GET['ref']))
    odownloadXLS($_GET['file'], BASH_WRITER_FILE_PATH . "/" . $refs[$_GET['ref']-1], "index.php") ;

if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ref = $_REQUEST['index_reference'];

    if($file1['extension']=='xls' || $file1['extension']=='xlsx')
    {
        $ext = $file1['extension'];

        if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
        {
            if($file1['extension'] == 'xlsx')
            {
                $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
                //echo '<pre>'; print_r($xls1Arr[0]);exit;
                $results  = process($xls1Arr[0], 2, 0, $ref) ;
            }
            else
            {
                $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
                //echo '<pre>'; print_r($xls1Arr[0]);exit;
                $results  = process($xls1Arr[0], 2, 1, $ref) ;
            }   //echo '<pre>'; print_r($results);exit;
            
            $file_name = uniqid() . "_" . "bash" . ".xls" ;
            $file_path = BASH_WRITER_FILE_PATH . "/" . $refs[$ref-1] . "/" . $file_name ;

            if (oWriteXLS($results[0][0], $file_path))
                header("Location:index.php?msg=success&file=".$file_name."&ref=".$ref);
            else
                header("Location:index.php?msg=error");
        }
    }
}
else
    header("Location:index.php");

    function process($data, $start, $xls, $ref)
    {
        $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $colors  =  array(
        'aqua'=>'00FFFF',
        'black'=>'000000',
        'blue'=>'0000FF',
        'brown'=>'A52A2A',
        'cyan'=>'00FFFF',
        'gray'=>'808080',
        'green'=>'008000',
        'grey'=>'808080',
        'lime'=>'00FF00',
        'magenta'=>'FF00FF',
        'maroon'=>'800000',
        'orange'=>'FFA500',
        'purple'=>'800080',
        'red'=>'FF0000',
        'silver'=>'C0C0C0',
        'yellow'=>'FFFF00');
        
        $refdir = BASH_WRITER_FILE_PATH . "/" . $refs[$ref-1] . "/" ;
        if (!is_dir($refdir)) {
            mkdir($refdir);
            chmod($refdir, 0777);
        }
        $configFile = BASH_CONFIG_FILE ;
        if (!file_exists($configFile)) {
            $products = array();
            
            $fp=fopen($configFile, 'w');
            fwrite($fp, '');
            fclose($fp);
            chmod($configFile, 0777);
        }
        else if(filesize($configFile)>0)
        {
            $handle = fopen($configFile, "r");
            $products = array_unique(unserialize(fread($handle, filesize($configFile))));
            fclose($handle);
        }
        else
        {
            $products = array();
        }
        
        $sheetcount = 1;//echo '<pre>';
        foreach ($data as $xlsArr1) {
            $key = $xls ; 
            foreach ( $xlsArr1 as $col ) :
                /*echo $col[1].'<br>';print_r($col);
                if(strstr($col[1], 'korben.edit-place.com'))
                    $arr2[] = $col[3];*/
                
                if($key>$xls)// && (1==2)
                {
                    $col[$ref] = trim($col[$ref]);
                    $pdct = explode("/", $col[$ref]);
                    $pattern = BASH_IMAGE_PATH.'/*/'.$pdct[0].'*.*';
                    $arraySource = glob($pattern,GLOB_BRACE);
                    sort($arraySource);
                    $arraySource = str_replace(BASH_IMAGE_PATH, BASH_CLIENT_URL, $arraySource) ;
                    //echo '<pre>'; print_r($arraySource);//exit($pdct[0]);
                    
                    if(count($arraySource)>0)
                    {
                        $path = pathinfo($arraySource[0]) ;//echo '<br>^^'.$pdct[0];
                        if(!in_array($pdct[0], $products))
                        {
                            $pColors[$pdct[0]][] = $key ;
                            array_push($products, $pdct[0]);//print_r($products);echo '<br>$$'.$pdct[0];
                            $data[$sheetcount-1][$key][0] = $path['dirname'].'/'.$pdct[0] ;
                        }
                        elseif(in_array($pdct[0], $products))
                        {//print_r($products);echo '<br>--'.$col[$ref].'<br>';
                            $pColors[$pdct[0]][] = $key ;
                            //$data[$sheetcount-1][$key][1] = 'Doublon-'.($pColors[$pdct[0]][0] + 1) ;
                            $data[$sheetcount-1][$key][0] = 'Doublon' ;
                        }
                        $pdct_id = $pColors[$pdct[0]][0];//echo '<br>|'.$pdct_id.'|<br>';
                    }
                    elseif($pdct_id && empty($col[$ref]))
                        $data[$sheetcount-1][$key][0] = 'Doublon-'.($pdct_id + 1);
                    else
                    {
                        $data[$sheetcount-1][$key][0] = 'NA';
                        $pdct_id = '';
                    }
                }
                else {
                	$data[$sheetcount-1][$key][0] = 'Url' ;
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }

//print_r($arr2);
//exit('edn..'.(serialize(array_filter(array_unique($arr2)))));
        foreach($pColors as $pColors_)
        {
            //$color = usercolor(rand());
            $rand_color_key = array_rand($colors);
            //echo $input[$rand_keys[0]] . "\n";
            foreach($pColors_ as $pColor)
            {
                $colorcode[$pColor] = $rand_color_key ;
            }
            //usercolor(rand())
        }
        //print_r($colorcode);exit;
        $fp=fopen($configFile, 'w');
        fwrite($fp, serialize($products));
        fclose($fp);
        chmod($configFile, 0777);//exit('###');
        
        $sheetcount = 1;
        foreach ($data as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                foreach ( $dataArr as $idx=>$col ) :
                    $results[$sheetcount-1][$key][$idx] = $col ;
                endforeach ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        return array($results, $colorcode) ;
    }
?>