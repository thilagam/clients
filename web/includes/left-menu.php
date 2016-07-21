 <div class="span2" style="float:left;">
    <div id="client-nav"><a class="client-nav" href="JavaScript:void(0)"><i class="icon-user"></i> CLIENTS LIST</a></div>
<?php
$jsonData = json_decode(file_get_contents(MENU_CONFIG_JSON)) ;

foreach($jsonData->ul as $json)
	$menuItems[strtolower($json->h3)] = $json ;

ksort($menuItems);

$jMenu = '<div id="nallamenu">
	<ul>';

foreach($menuItems as $menuItem) :

	if($menuItem->ul)
	{
		$jMenu .= '
		<li>
			<h3>' . $menuItem->h3 . '</h3>
			<ul ' . ${$menuItem->menus} . '>';
			
		foreach($menuItem->ul as $subMenuItem) :
		
			$jMenu .= '
				<li>
					<h3 class="sub">' . $subMenuItem->h3 . '</h3>
					<ul class="subitems"' . ${$subMenuItem->menus} . '>';
					
			foreach($subMenuItem->li as $liSubMenuItem)
						$jMenu .= '<li><a class="subitems" href="' . MENU_URL . ($liSubMenuItem->base ? $liSubMenuItem->base : $subMenuItem->base) . '/' . $liSubMenuItem->href . '.php?client=' . $subMenuItem->client . '">' . $liSubMenuItem->label . '</a></li>                    
						';
			$jMenu .= '
					</ul>                       
				</li>';
		endforeach ;
		$jMenu .= '         
			</ul>
		</li>';
		
	}
	else
	{
		$jMenu .= '
		<li>
			<h3>' . $menuItem->h3 . '</h3>
			<ul ' . ${$menuItem->menus} . '>';
		
		foreach($menuItem->li as $liMenuItem)
		$jMenu .= '
				<li><a href="' . MENU_URL . ($liMenuItem->base ? $liMenuItem->base : $menuItem->base) . ((!$liMenuItem->base && !$menuItem->base) ? '' : '/') . $liMenuItem->href . '.php?client=' . $menuItem->client . '">' . $liMenuItem->label . '</a></li>';
				
		$jMenu .= '
			</ul>
		</li>';
	}

endforeach ;

$jMenu .= '
	</ul>
</div>';

echo($jMenu);
?>
	
	<!--/.well -->
</div><!--/span-->
