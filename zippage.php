 <?php
	defined('_JEXEC') or die('Access deny');
	
	class plgContentZipPage extends JPlugin 
	{
		function onContentPrepare($content, $article, $params, $limit){	
		
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins/content/zippage/style.css');			
			$re = '/href\s*?=\s*?(.*pdf|PDF|pDf|pdF|Pdf|pDF|PdF|PDf).*data-type\s*?=\s*?"(.*)">(.*)<\/a>/i';
			preg_match_all($re, $article->text, $matches, PREG_SET_ORDER, 0);
			$zip = new ZipArchive();		
			$filename = $article->id."-".$article->alias.".zip";
			if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
				exit("cannot open <$filename>\n");
			}
			foreach($matches as $fichier)
			{
				if (empty($fichier[2]))
				{
					$zip->addFromString('VRAC/'.substr($fichier[1],1), "");
				}
				else
				{
					$zip->addFromString($fichier[2].'/'.substr($fichier[1],1), "");
				}
			}
			$zip->setArchiveComment("Article ".$article->id.":".$article->title);
			$machaine = '<div class="zip-download"><a href="'.basename($zip->filename).'" target="_blank"><img src="plugins/content/zippage/zip.png">Zipper les PDF de cette page</a></div>';
			$zip->close();
			$article->text = str_replace('{zippage}',$machaine,$article->text);
		}	
	}
?>