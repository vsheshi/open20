<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 02/10/2018
 * Time: 15:33
 */

namespace pcd20\import\models;


use lispa\amos\community\models\Community;
use lispa\amos\documenti\models\Documenti;

class ReportNode extends \yii\base\Model
{
    const COMMUNITY = 'community';
    const DIRECTORY = 'directory';
    const FILE = 'file';

    /** @var array Is the full path that is bulit */
    public static $lastDir = [""];
    public static $filenameLog = [""];
    public $logfile = '';


    public $type;
    public $id;
    public $name;

    public $humanReadablePath;
    public $error = false;
    public $errorMessage = '';


    /**
     *
     */
    public function init()
    {
        parent::init();
        if(!empty($this->logfile)) {
            ReportNode::$filenameLog = $this->logfile;
        }
        if($this->type == self::COMMUNITY){
            $community = Community::findOne($this->id);
            $this->humanReadablePath = ReportNode::getStringPathLastDir() .'\\'. $this->name. "\\";
            ReportNode::$lastDir  []=  $this->name;
            if(empty($community)){
                $this->error = true;
                $this->errorMessage = 'Community not created';
            }
        }
        else if($this->type == self::DIRECTORY){
            $documenti = Documenti::findOne($this->id);
            $this->humanReadablePath = ReportNode::getStringPathLastDir() .'\\'. $this->name. "\\";
            ReportNode::$lastDir []=   $this->name;
            if(empty($documenti)){
                $this->error = true;
                $this->errorMessage = 'Directory not created';
            }
        }
        else {
            $documenti = Documenti::findOne($this->id);
            if($documenti){
                $this->name = $documenti->titolo;
                $file = $documenti->getDocumentMainFile();
                if($file){
                    $file->getPath();
                    if(!file_exists($file->getPath())){
                        $this->error = true;
                        $this->errorMessage = 'File non found';
                    }
                }
                if(empty($file)){
                    $this->error = true;
                    $this->errorMessage = 'File not created';
                }
            }else {
                $this->error = true;
                $this->errorMessage = 'Document not created';
            }
            $this->humanReadablePath = ReportNode::getStringPathLastDir() .'\\'. $this->name;
        }
        $this->writeLog();
    }

    /**
     *
     */
    public function writeLog(){
        $logPath = self::$filenameLog;
        $fp = fopen($logPath, 'a+');
        fwrite($fp, $this->type."\t".$this->humanReadablePath."\t".$this->error."\t".$this->errorMessage."\t"."\n");
        fclose($fp);
    }


    /**
     * @return string
     */
    public static function getStringPathLastDir(){
        return implode("\\", ReportNode::$lastDir);

    }

    /**
     *
     */
    public static function popDirectoryPathOneLevel(){
        if(count(ReportNode::$lastDir) > 0) {
            array_pop(ReportNode::$lastDir);
        }
        return true;
    }


    /**
     * @param $currentName
     * @return bool
     */
    public static function popDirectoryPath($currentName){
        if(end(ReportNode::$lastDir) == $currentName){
            array_pop(ReportNode::$lastDir);
            return true;
        }
        else {
            array_pop(ReportNode::$lastDir);
            ReportNode::popDirectoryPath($currentName);
        }
    }



    public static function generateExcellFromFile($filename){
        $nameFile = 'Report_import_aree.xls';
        //array per il file
        $xlsData = [];
        $xlsData[] = ["Type", "Path", "Error", "Error message"];

        $handle = fopen($filename, "r");
        $reports = [];
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $xlsData []= preg_split('/[\t]/', $line);
            }

            fclose($handle);
        } else {
            // error opening the file.
        }

        //inizializza l'oggetto excel
        $objPHPExcel = new \PHPExcel();

        //li pone nella tab attuale del file xls
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('d')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->fromArray($xlsData, NULL, 'A1');
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray(
            [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,],
                'borders' => [
                    'top' => [
                        'style' => \PHPExcel_Style_Border::BORDER_DOUBLE
                    ]
                ],
                'fill' => [
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => [
                        'rgb' => 'cccccc'
                    ],
                    'endcolor' => [
                        'rgb' => 'cccccc'
                    ]
                ]
            ]
        );

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$nameFile.'"');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    /**
     * @param $reports
     */
//    public static function generateExcellReport($reports){
//        $nameFile = 'Report_import_aree.xls';
//        //array per il file
//        $xlsData = [];
//        $xlsData[] = ["Type", "Path", "Error", "Error message"];
//
//        //recupera i dati
//
//        /** @var  $report ReportNode */
//        foreach($reports as $report){
//            $row = [
//                $report->type,
//                $report->humanReadablePath,
//                $report->error,
//                $report->errorMessage
//            ];
//
//            $xlsData[] = $row;
//        }
//
//        //inizializza l'oggetto excel
//        $objPHPExcel = new \PHPExcel();
//
//        //li pone nella tab attuale del file xls
//        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('d')->setAutoSize(true);
//
//        $objPHPExcel->getActiveSheet()->fromArray($xlsData, NULL, 'A1');
//        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray(
//            [
//                'font' => ['bold' => true],
//                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,],
//                'borders' => [
//                    'top' => [
//                        'style' => \PHPExcel_Style_Border::BORDER_DOUBLE
//                    ]
//                ],
//                'fill' => [
//                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
//                    'startcolor' => [
//                        'rgb' => 'cccccc'
//                    ],
//                    'endcolor' => [
//                        'rgb' => 'cccccc'
//                    ]
//                ]
//            ]
//        );
//
//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save($nameFile);
//        return $nameFile;
//    }

//    public function formatCommunityWithBold($string){
//        $objRichText = new \PHPExcel_RichText();
//
//        $stringSplit = preg_split("/(\{\{|\}\})/", $string);
//        foreach ($stringSplit as $elem){
//            if(strpos($elem, "//") < 0 ){
//                $objBold = $objRichText->createTextRun($elem);
//                $objBold->getFont()->setBold(true);
//            }else {
//                $objRichText->createText($elem);
//            }
//            return $objRichText;
//        }
//        return $stringSplit;
//    }


}